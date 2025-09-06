<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Accomodation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AccomodationProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Accomodation
    {
        if (!$data instanceof Accomodation) {
            return $data;
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $data->setOwner($user);

        if ($user->getAccomodations()->count() === 0) {
            $roles = $user->getRoles();
            if (!in_array('ROLE_OWNER', $roles, true)) {
                
                $user->addRole('ROLE_OWNER');
                $this->em->persist($user);
            }
        }

        $this->em->persist($data);
        $this->em->flush();

        return $data;
    }
}