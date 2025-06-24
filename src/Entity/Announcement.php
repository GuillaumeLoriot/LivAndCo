<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\AnnouncementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AnnouncementRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['announcement:read']
            ]
        ),
        new Get(
            normalizationContext: [
                'groups' => ['announcement:read:item']
            ]
        ),
    ]
)]

class Announcement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['announcement:read:item','announcement:read','accommodation:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['announcement:read:item','announcement:read','accommodation:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['announcement:read:item','announcement:read','accommodation:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['announcement:read:item','announcement:read','accommodation:read'])]
    private ?int $dailyPrice = null;

    #[ORM\Column]
    #[Groups(['announcement:read:item','announcement:read','accommodation:read'])]
    private ?int $nbPlace = null;

    #[ORM\ManyToOne(inversedBy: 'announcements')]
    #[Groups(['announcement:read:item'])]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'announcements')]
    #[Groups(['announcement:read:item'])]
    private ?Accomodation $accomodation = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'announcement')]
    #[Groups(['announcement:read:item'])]
    private Collection $reservations;

    /**
     * @var Collection<int, Unavailability>
     */
    #[ORM\OneToMany(targetEntity: Unavailability::class, mappedBy: 'announcement')]
    #[Groups(['announcement:read:item'])]
    private Collection $unavailabilities;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->unavailabilities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDailyPrice(): ?int
    {
        return $this->dailyPrice;
    }

    public function setDailyPrice(int $dailyPrice): static
    {
        $this->dailyPrice = $dailyPrice;

        return $this;
    }

    public function getNbPlace(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlace(int $nbPlace): static
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getAccomodation(): ?Accomodation
    {
        return $this->accomodation;
    }

    public function setAccomodation(?Accomodation $accomodation): static
    {
        $this->accomodation = $accomodation;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setAnnouncement($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getAnnouncement() === $this) {
                $reservation->setAnnouncement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Unavailability>
     */
    public function getUnavailabilities(): Collection
    {
        return $this->unavailabilities;
    }

    public function addUnavailability(Unavailability $unavailability): static
    {
        if (!$this->unavailabilities->contains($unavailability)) {
            $this->unavailabilities->add($unavailability);
            $unavailability->setAnnouncement($this);
        }

        return $this;
    }

    public function removeUnavailability(Unavailability $unavailability): static
    {
        if ($this->unavailabilities->removeElement($unavailability)) {
            // set the owning side to null (unless already changed)
            if ($unavailability->getAnnouncement() === $this) {
                $unavailability->setAnnouncement(null);
            }
        }

        return $this;
    }
}
