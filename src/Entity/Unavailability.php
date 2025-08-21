<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UnavailabilityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UnavailabilityRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['unavailability:read']
            ]
        ),
        new Get(
            normalizationContext: [
                'groups' => ['unavailability:read:item']
            ]
        ),
        new Post(
            normalizationContext: ['groups' => ['unavailability:read:item']],
            denormalizationContext: ['groups' => ['unavailability:write']],
            security: "object.getAnnouncement().getOwner() == user"
        ),
        new Put(
            denormalizationContext: ['groups' => ['unavailability:write']],
            normalizationContext: ['groups' => ['unavailability:read:item']],
            security: "object.getAnnouncement().getOwner() == user"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['unavailability:write']],
            normalizationContext: ['groups' => ['unavailability:read:item']],
            security: "object.getAnnouncement().getOwner() == user"
        ),
        new Delete(
            security: "object.getAnnouncement().getOwner() == user"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'announcement.id' => 'exact'
])]
#[ApiFilter(RangeFilter::class, properties: [
    'startDate',
    'endDate',
])]
#[ApiFilter(OrderFilter::class, properties: [
    'startDate',
    'endDate',
])]
class Unavailability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['unavailability:read', 'unavailability:read:item', 'unavailability:write'])]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['unavailability:read', 'unavailability:read:item', 'unavailability:write'])]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['unavailability:read:item', 'unavailability:write'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'unavailabilities')]
    #[Groups(['unavailability:read', 'unavailability:read:item', 'unavailability:write'])]
    private ?Announcement $announcement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAnnouncement(): ?Announcement
    {
        return $this->announcement;
    }

    public function setAnnouncement(?Announcement $announcement): static
    {
        $this->announcement = $announcement;

        return $this;
    }
}
