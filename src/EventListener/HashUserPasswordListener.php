<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, method: 'hashPassword', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'hashPasswordOnUpdate', entity: User::class)]
class HashUserPasswordListener
{
    public function __construct(
        private UserPasswordHasherInterface $hasher) { }

    public function hashPassword(User $user, PrePersistEventArgs $args): void
    {
        $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
    }

    public function hashPasswordOnUpdate(User $user, PreUpdateEventArgs $args): void
    {
        if ($args->hasChangedField('password')) {
            $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
        }
    }
}