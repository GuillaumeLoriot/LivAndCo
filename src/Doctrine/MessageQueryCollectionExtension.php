<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final class MessageQueryCollectionExtension implements QueryCollectionExtensionInterface
{

    public function __construct(
        private Security $security,
    ) {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {

        // je vérifie que ce filtre ne s'applique qu'à l'entité Message
        if ($resourceClass !== Message::class) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return;
        }
        $connectedUserId = $user->getId();


        //  je vérifie que les filters nourris par apiPlatform sont présents avant de les assigner à ma variable
        $filters = isset($context['filters']) ? $context['filters'] : [];

        // rootAllias corespond à l'alias principale généré par doctrine pour la requète et enregistrer à l'indice 0
        $rootAlias = $queryBuilder->getRootAliases()[0];


        // la requête vas chercher tous les messages ou le participant est présent dans la propriété sender ou receiver
        $queryBuilder
            ->andWhere("(IDENTITY($rootAlias.sender) = :connectedUser OR IDENTITY($rootAlias.receiver) = :connectedUser)")
            ->setParameter('connectedUser', $connectedUserId);



        // si le filtre peer=ID est fourni, je récupère les messages ou user ou peer sont ensemble en tant que sender ou receiver
        $filters = $context['filters'] ?? [];
        if (!empty($filters['peer'])) {
            $peerId = (int) $filters['peer'];
            if ($peerId > 0 && $peerId !== $connectedUserId) {
                $queryBuilder->andWhere("( (IDENTITY($rootAlias.sender) = :connectedUser AND IDENTITY($rootAlias.receiver) = :peer)
                              OR (IDENTITY($rootAlias.sender) = :peer AND IDENTITY($rootAlias.receiver) = :connectedUser) )")
                    ->setParameter('peer', $peerId);
            }
        }

    }
}