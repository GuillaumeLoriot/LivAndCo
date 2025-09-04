<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Accomodation;
use App\Entity\Announcement;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AnnouncementProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Announcement
    {
        if (!$data instanceof Announcement || $data->getAccomodation() === null) {
            return $data;
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $accomodationOwner = $data->getAccomodation()->getOwner();

        if ($user !== $accomodationOwner) {
            throw new AccessDeniedHttpException;
        }
      
        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}