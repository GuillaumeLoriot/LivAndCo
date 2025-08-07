<?php
// api/src/Doctrine/CurrentUserExtension.php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Announcement;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final class AnnouncementQueryCollectionExtension implements QueryCollectionExtensionInterface
{

    public function __construct(
        private Security $security,
    ) {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {

        // je vérifie que ce filtre ne s'applique qu'à l'entité Announcement
        if ($resourceClass !== Announcement::class) {
            return;
        }

        //  je vérifie que les filters nourris par apiPlatform sont présents avant de les assigner à ma variable
        $filters = isset($context['filters']) ? $context['filters'] : [];

        if (!isset($filters['startDate'])) {
            return;
        }

        try {
            $startDate = new \DateTimeImmutable($filters['startDate']);

            // Si endDate est fourni par l'utilisateur dans les filtres, je l’utilise, sinon j'ajoute 1 mois car c'est le minimum de réservation
            $endDate = isset($filters['endDate']) ? new \DateTimeImmutable($filters['endDate']) : $startDate->modify('+1 month');
            // Si endDate est fourni mais non logique car plus petit que startDate, j'ajoute par défaut 1 mois à starDate pour avoir un interval valide
            if ($endDate <= $startDate) {
                $endDate = $startDate->modify('+1 month');
            }
        } catch (\Exception $e) {
            return; // j'arrète si une des deux dates est invalide
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        // query qui cherche les announcements sans reservation ou libre au date recherchées et qui sera ajoutée à la suite au filtres standarts api platform
        $queryBuilder
            ->leftJoin("$rootAlias.reservations", 'r')
            ->leftJoin("$rootAlias.unavailabilities", 'u')
            ->andWhere('(r.id IS NULL OR r.endDate < :start OR r.startDate > :end)')
            ->andWhere('(u.id IS NULL OR u.endDate < :start OR u.startDate > :end)')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->groupBy("$rootAlias.id");

    }
}