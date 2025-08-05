<?php

namespace App\Entity;

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
            // security: "object.getAnnouncement().getOwner() == user"
        ),
        new Put(
            denormalizationContext: ['groups' => ['unavailability:write']],
            normalizationContext: ['groups' => ['unavailability:read:item']],
            // security: "object.getAnnouncement().getOwner() == user"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['unavailability:write']],
            normalizationContext: ['groups' => ['unavailability:read:item']],
            // security: "object.getAnnouncement().getOwner() == user"
        ),
        new Delete(
            // security: "object.getAnnouncement().getOwner() == user"
        )
    ]
)]
class Unavailability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['unavailability:read', 'unavailability:read:item', 'unavailability:write'])]
    private ?\DateTime $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['unavailability:read', 'unavailability:read:item', 'unavailability:write'])]
    private ?\DateTime $endDate = null;

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

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
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
