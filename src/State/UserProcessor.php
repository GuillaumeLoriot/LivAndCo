<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (!$data instanceof User) {
            return $data;
        }

        $operationName = $operation->getName();

        if ($operationName === 'create_user') {
            $data
                ->setProfilePicture('generic-user.jpg')
                ->setRoles(['ROLE_USER'])
                ->setIsVerified(false)
                ->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($data);
        }
        
        $this->em->flush();

        return $data;
    }
}