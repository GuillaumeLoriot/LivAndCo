<?php

namespace App\Entity;

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
use ApiPlatform\Metadata\Put;
use App\Repository\AnnouncementRepository;
use App\State\AnnouncementProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnnouncementRepository::class)]
#[ApiResource(
    paginationEnabled: true,
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['announcement:read']
            ],
            paginationClientEnabled: true,
            paginationClientItemsPerPage: true,
        ),
        new Get(
            normalizationContext: [
                'groups' => ['announcement:read:item']
            ]
        ),
        new Post(
            normalizationContext: ['groups' => ['announcement:read:item']],
            denormalizationContext: ['groups' => ['announcement:write']],
            processor: AnnouncementProcessor::class,
            security: "is_granted('ROLE_OWNER')"
        ),
        new Put(
            normalizationContext: ['groups' => ['announcement:read:item']],
            denormalizationContext: ['groups' => ['announcement:write']],
            security: "object.getOwner() == user"
        ),
        new Patch(
            normalizationContext: ['groups' => ['announcement:read:item']],
            denormalizationContext: ['groups' => ['announcement:write']],
            security: "object.getAccomodation().getOwner() == user"
        ),
        new Delete(
            security: "object.getAccomodation().getOwner() == user"
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => 'partial',
    'accomodation.address' => 'partial',
    'accomodation.city' => 'partial',
    'accomodation.zipcode' => 'exact',
    'accomodation.id' => 'exact',
    'accomodation.owner.id' => 'exact',
    'services.id' => 'exact',
    'equipment.id' => 'exact',
])]
#[ApiFilter(RangeFilter::class, properties: [
    'dailyPrice',
    'nbPlace'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'dailyPrice',
    'nbPlace',
])]
#[ApiFilter(ExistsFilter::class, properties: [
    'images',
    'services',
    'equipment'
])]
class Announcement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['announcement:read:item', 'announcement:read', 'accomodation:read:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['announcement:read:item', 'announcement:read', 'accomodation:read:item', 'announcement:write', 'reservation:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['announcement:read:item', 'accomodation:read:item', 'announcement:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\Type('integer')]
    #[Assert\Positive]
    #[Groups(['announcement:read:item', 'announcement:read', 'accomodation:read:item', 'announcement:write'])]
    private ?int $dailyPrice = null;

    #[ORM\Column]
    #[Assert\Type('integer')]
    #[Assert\Positive]
    #[Groups(['announcement:read:item', 'announcement:read', 'accomodation:read:item', 'announcement:write', 'reservation:read'])]
    private ?int $nbPlace = null;

    #[ORM\ManyToOne(inversedBy: 'announcements')]
    #[Groups(['announcement:read:item', 'announcement:read', 'announcement:write', 'reservation:read'])]
    private ?Accomodation $accomodation = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'announcement')]
    #[Groups(['announcement:read:item', 'announcement:read'])]
    private Collection $reservations;

    /**
     * @var Collection<int, Unavailability>
     */
    #[ORM\OneToMany(targetEntity: Unavailability::class, mappedBy: 'announcement')]
    #[Groups(['announcement:read:item'])]
    private Collection $unavailabilities;

    /**
     * @var Collection<int, Convenience>
     */
    #[ORM\ManyToMany(targetEntity: Convenience::class, inversedBy: 'announcements')]
    #[Groups(['announcement:read:item', 'announcement:read', 'announcement:write'])]

    private Collection $conveniences;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'announcements')]
    #[Groups(['announcement:read:item'])]

    private Collection $services;

    #[ORM\Column(length: 100)]
    #[Groups(['announcement:read:item', 'announcement:read', 'accomodation:read:item', 'announcement:write', 'reservation:read'])]

    private ?string $coverPicture = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->unavailabilities = new ArrayCollection();
        $this->conveniences = new ArrayCollection();
        $this->services = new ArrayCollection();
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

    /**
     * @return Collection<int, Convenience>
     */
    public function getConveniences(): Collection
    {
        return $this->conveniences;
    }

    public function addConvenience(Convenience $convenience): static
    {
        if (!$this->conveniences->contains($convenience)) {
            $this->conveniences->add($convenience);
        }

        return $this;
    }

    public function removeConvenience(Convenience $convenience): static
    {
        $this->conveniences->removeElement($convenience);

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        $this->services->removeElement($service);

        return $this;
    }

    public function getCoverPicture(): ?string
    {
        return $this->coverPicture;
    }

    public function setCoverPicture(string $coverPicture): static
    {
        $this->coverPicture = $coverPicture;

        return $this;
    }
}
