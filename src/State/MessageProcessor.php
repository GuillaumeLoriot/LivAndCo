<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class MessageProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Message
    {

        // je verifie que je reçois bien un message et que le destinataire asscocié est présent
        if (!$data instanceof Message) {
            return $data;
        }

        if ($data->getReceiver() === null) {
            throw new UnprocessableEntityHttpException("Le destinataire est requis.");
        }   
        
        
        // je récupère les user et vérifie que les 2 users ne soient pas les même
        /** @var User $user */        
        $receiver = $data->getReceiver();
        $sender = $this->security->getUser();

        if (!$sender) {
            throw new AccessDeniedHttpException('Authentification requise.');
        }

        if ($receiver === $sender) {
            throw new AccessDeniedHttpException;
        }

        // je défini les données après les vérifications
        $data->setReceiver($receiver)
        ->setSender($sender)
        ->setCreatedAt(new \DateTimeImmutable());



        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}