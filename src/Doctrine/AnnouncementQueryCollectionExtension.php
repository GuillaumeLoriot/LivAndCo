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
        } catch (\Exception $e) {
            return; // j'arrète si la date est invalide
        }

        // par défault, la recherche sera de 1 mois si l'utilisateur ne rempli pas le champ months
        $months = 1;
        // si le paramètre months est présent je défini qu'il doit être supérieur à 0 et inférieur à 24 mois
        if (isset($filters['months'])) {
            $months = intval($filters['months']);
            if ($months < 1) {
                $months = 1;
            }
            if ($months >= 24) {
                $months = 24;
            }
        }

        $endDate = $startDate->modify("+{$months} month");

        // rootAllias corespond à l'alias principale généré par doctrine pour la requète et enregistrer à l'indice 0
        $rootAlias = $queryBuilder->getRootAliases()[0];

        // query qui cherche les announcements sans reservation ou indisponibilités
        // au dates recherchées et qui sera ajoutée à la suite des filtres standards api platform
        $queryBuilder
            ->distinct()
            ->leftJoin(
                "$rootAlias.reservations",
                'r',
                'WITH',
                'r.startDate <= :end AND r.endDate >= :start'
            )
            ->leftJoin(
                "$rootAlias.unavailabilities",
                'u',
                'WITH',
                'u.startDate <= :end AND u.endDate >= :start'
            )
            ->andWhere('r.id IS NULL')
            ->andWhere('u.id IS NULL')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);
    }
}