<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
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
use App\Repository\ReservationRepository;
use App\State\ReservationProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['reservation:read']
            ]
        ),
        new Get(
            normalizationContext: ['groups' => ['reservation:read:item']],
            security: "object.getUser() == user || object.getAnnouncement().getAccomodation().getOwner() == user"
        ),
        new Post(
            normalizationContext: ['groups' => ['reservation:read:item']],
            denormalizationContext: ['groups' => ['reservation:write']],
            validationContext: ['groups' => ['Default', 'reservation:write']],
            processor: ReservationProcessor::class,
            security: "is_granted('ROLE_USER')"      
        ),
        new Patch(
            normalizationContext: ['groups' => ['reservation:read:item']],
            denormalizationContext: ['groups' => ['reservation:write:owner']],
            validationContext: ['groups' => ['Default', 'reservation:write:owner']],
            security: "object.getAnnouncement().getAccomodation().getOwner() == user"
        ),
        new Delete(
            security: "object.getUser() == user"
        )
    ]
)]

#[ApiFilter(SearchFilter::class, properties: [
    'status' => 'exact',
    'user.id' => 'exact',
    'announcement.id' => 'exact',
    'announcement.accomodation.owner.id' => 'exact'
])]
#[ApiFilter(RangeFilter::class, properties: [
    'totalPrice',
    'createdAt'
])]
#[ApiFilter(DateFilter::class, properties: [
    'startDate', 
    'endDate'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'startDate',
    'endDate',
    'totalPrice',
    'createdAt',
    'status'
])]
class Reservation
{

    public const STATUSES = ['pending', 'confirmed', 'cancelled'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation:read:item', 'reservation:read', 'announcement:read:item'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull(message: 'La date de début est obligatoire.', groups: ['reservation:write'])]
    #[Assert\GreaterThan('today', message: "La date de début doit être postérieure à aujourd'hui.", groups: ['reservation:write'])]
    #[Groups(['reservation:read:item', 'reservation:read' , 'announcement:read:item', 'reservation:write'])]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Groups(['reservation:read:item', 'reservation:read','announcement:read:item'])]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(length: 100)]
    #[Assert\Choice(choices: self::STATUSES, message: 'Status invalide.')]
    #[Groups(['reservation:read:item', 'reservation:read', 'reservation:write', 'reservation:write:owner'])]
    private ?string $status = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['reservation:read:item', 'reservation:read'])]
    private ?string $totalPrice = null;

    #[ORM\Column]
    #[Groups(['reservation:read:item', 'reservation:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    // donnée pour calculé la date de fin, exprimée en mois, non stockée en bdd
    #[Assert\NotNull(message: 'La durée est obligatoire.',  groups: ['reservation:write'])]
    #[Assert\Type('integer', message: 'La durée doit être un entier (mois).')]
    #[Assert\Range(min: 1, max: 24, notInRangeMessage: 'La durée doit être comprise entre {{ min }} et {{ max }} mois.')]
    #[Groups(['reservation:write'])]
    private ?int $duration = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[Groups(['reservation:read:item', 'reservation:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[Groups(['reservation:read:item', 'reservation:write', 'reservation:read'])]
    private ?Announcement $announcement = null;

    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    #[Groups(['reservation:read:item', 'announcement:read:item', 'announcement:read'])]
    private ?Review $review = null;

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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

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
