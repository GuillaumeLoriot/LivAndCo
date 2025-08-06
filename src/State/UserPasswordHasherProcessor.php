<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHasherProcessor implements ProcessorInterface
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
        $plainPassword = $data->getPassword();

        if ($plainPassword) {
            $hashedPassword = $this->hasher->hashPassword($data, $plainPassword);
            $data->setPassword($hashedPassword);
        }

        if ($operationName === 'create_user') {
            $data->setPassword($this->hasher->hashPassword($data, $plainPassword));
            $data->setRoles(['ROLE_USER']);
            $data->setIsVerified(false);
            $data->setCreatedAt(new \DateTimeImmutable());
    
            $this->em->persist($data);
        }
        
        $this->em->flush();

        return $data;
    }
}