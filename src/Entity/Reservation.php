<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['reservation:read']
            ]
        ),
        new Get(
            normalizationContext: [
                'groups' => ['reservation:read:item']
            ]
        ),
        new Post(
            normalizationContext: ['groups' => ['reservation:read:item']],
            denormalizationContext: ['groups' => ['reservation:write']]
        ),
        new Put(
            normalizationContext: ['groups' => ['reservation:read:item']],
            denormalizationContext: ['groups' => ['reservation:write']]
        ),
        new Patch(
            normalizationContext: ['groups' => ['reservation:read:item']],
            denormalizationContext: ['groups' => ['reservation:write']]
        ),
    ]
)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation:read:item', 'announcement:read:item'])]

    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['reservation:read:item', 'announcement:read:item', 'reservation:write'])]

    private ?\DateTime $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['reservation:read:item', 'announcement:read:item', 'reservation:write'])]

    private ?\DateTime $endDate = null;

    #[ORM\Column(length: 100)]
    #[Groups(['reservation:read:item'])]

    private ?string $status = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['reservation:read:item'])]

    private ?string $totalPrice = null;

    #[ORM\Column]
    #[Groups(['reservation:read:item'])]

    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[Groups(['reservation:read:item'])]

    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[Groups(['reservation:read:item'])]

    private ?Announcement $announcement = null;

    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    #[Groups(['reservation:read:item', 'announcement:read:item'])]

    private ?Review $review = null;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): static
    {
        // unset the owning side of the relation if necessary
        if ($review === null && $this->review !== null) {
            $this->review->setReservation(null);
        }

        // set the owning side of the relation if necessary
        if ($review !== null && $review->getReservation() !== $this) {
            $review->setReservation($this);
        }

        $this->review = $review;

        return $this;
    }
}
