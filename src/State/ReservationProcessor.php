<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Announcement;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\UnavailabilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ReservationProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
        private ReservationRepository $reservationRepo,
        private UnavailabilityRepository $unavailabilityRepo,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Reservation
    {

        // je verifie que je reçois bien une réservation et que l'annonce asscocié est présente
        if (!$data instanceof Reservation || $data->getAnnouncement() === null) {
            return $data;
        }
        $announcement = $data->getAnnouncement();


        $startDate = $data->getStartDate();
        $duration = $data->getDuration();

        // si on ne reçoit pas de date de départ ou invalide, je renvoi une erreur 422
        if ($startDate === null || !$startDate instanceof \DateTimeInterface) {
            throw new UnprocessableEntityHttpException('la date de début de la réservation est requise et doit être valide.');
        }

        // si la durée n'est pas renseigné ou inférieur à 1 mois, je renvoi une erreur 422
        if ($duration === null || $duration < 1) {
            throw new UnprocessableEntityHttpException('la durée de la réservation est requise et doit être de 1 mois minimum.');
        }

        // si la date de début est plus ancienne que le jour en cours, je renvoi une erreur 422
        $now = new \DateTimeImmutable('now');
        if ($startDate < $now) {
            throw new UnprocessableEntityHttpException('startDate ne peut pas être dans le passé.');
        }

        // après les vérification je définie la date précise de fin en fonction de la durée sélectionnée
        $endDate = $startDate->modify("+{$duration} month");

        // je vérifie qu'une réservation ne soie pas déja faite à ces dates
        if (
            $this->reservationRepo->hasReservation($announcement, $startDate, $endDate)
            || $this->unavailabilityRepo->hasUnavailability($announcement, $startDate, $endDate)
        ) {
            throw new ConflictHttpException(
                'Cette annonce est indisponible sur cette période.'
            );
        }

        // je récupère le user et vérifie que ce ne soit pas le propriétaire qui loue sa propre annonce et renvoi une 403
        /** @var User $user */
        $user = $this->security->getUser();
        $accomodationOwner = $data->getAnnouncement()->getAccomodation()->getOwner();

        if ($user === $accomodationOwner) {
            throw new AccessDeniedHttpException;
        }

        // je défini les données après toutes les vérifications
        $data->setUser($user)
            ->setAnnouncement($announcement)
            ->setTotalPrice(floor(($announcement->getDailyPrice() * 365) / 12))
            ->setCreatedAt(new \DateTimeImmutable())
            ->setStatus('pending')
            ->setStartDate($startDate)
            ->setEndDate($endDate);

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}

// voir si vraiment besoin de verifier tout ça ici car api platform le gère peut etre de base