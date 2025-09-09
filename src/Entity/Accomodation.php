<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\AccomodationRepository;
use App\State\AccomodationProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AccomodationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['accomodation:read']
            ]
        ),
        new Get(
            normalizationContext: [
                'groups' => ['accomodation:read:item']
            ]
        ),
        new Post(
            normalizationContext: ['groups' => ['accomodation:read:item']],
            denormalizationContext: ['groups' => ['accomodation:write']],
            processor: AccomodationProcessor::class,
            security: "is_granted('ROLE_USER')"
        ),
        new Put(
            normalizationContext: ['groups' => ['accomodation:read:item']],
            denormalizationContext: ['groups' => ['accomodation:write']],
            security: "object.getOwner() == user"
        ),
        new Patch(
            normalizationContext: ['groups' => ['accomodation:read:item']],
            denormalizationContext: ['groups' => ['accomodation:write']],
            security: "object.getOwner() == user"
        ),
        new Delete(
            security: "object.getOwner() == user"
        )
    ]
)]

#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'owner.id' => 'exact',
])]

class Accomodation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['accomodation:read:item', 'accomodation:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['accomodation:read','accomodation:read:item', 'accomodation:write', 'reservation:read'])]
    private ?string $addressLine1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['accomodation:read:item', 'accomodation:write'])]
    private ?string $addressLine2 = null;

    #[ORM\Column(length: 70)]
    #[Groups(['accomodation:read:item', 'accomodation:read', 'accomodation:write', 'announcement:read:item', 'announcement:read', 'reservation:read'])]
    private ?string $city = null;

    #[ORM\Column(length: 20)]
    #[Groups(['accomodation:read:item', 'accomodation:read', 'accomodation:write', 'announcement:read:item', 'announcement:read', 'reservation:read'])]
    private ?string $zipCode = null;

    #[ORM\Column(length: 70)]
    #[Groups(['accomodation:read:item', 'accomodation:read', 'accomodation:write'])]
    private ?string $country = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7)]
    #[Groups(['accomodation:read:item', 'accomodation:read', 'accomodation:write', 'announcement:read:item', 'announcement:read'])]
    private ?string $longitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7)]
    #[Groups(['accomodation:read:item', 'accomodation:read', 'accomodation:write', 'announcement:read:item', 'announcement:read'])]
    private ?string $latitude = null;

    #[ORM\Column]
    #[Groups(['accomodation:read:item', 'accomodation:read', 'accomodation:write', 'announcement:read:item', 'announcement:read', 'reservation:read'])]
    private ?int $surface = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['accomodation:read:item', 'accomodation:write', 'announcement:read:item', 'announcement:read'])]
    private ?bool $mixedGender = null;

    #[ORM\Column(length: 255)]
    #[Groups(['accomodation:write'])]
    private ?string $ownershipDeedPath = null;

    #[ORM\Column(length: 255)]
    #[Groups(['accomodation:write'])]
    private ?string $insuranceCertificatePath = null;

    #[ORM\Column(length: 255)]
    #[Groups(['accomodation:read:item', 'accomodation:read', 'accomodation:write', 'announcement:read:item'])]
    private ?string $coverPicture = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'accomodation')]
    #[Groups(['accomodation:read:item', 'accomodation:read', 'accomodation:write', 'announcement:read:item'])]
    private Collection $images;

    #[ORM\ManyToOne(inversedBy: 'accomodations')]
    #[Groups(['accomodation:read:item', 'announcement:read:item'])]
    private ?User $owner = null;

    /**
     * @var Collection<int, Announcement>
     */
    #[ORM\OneToMany(targetEntity: Announcement::class, mappedBy: 'accomodation')]
    #[Groups(['accomodation:read:item'])]
    private Collection $announcements;

    /**
     * @var Collection<int, Convenience>
     */
    #[ORM\ManyToMany(targetEntity: Convenience::class, inversedBy: 'accomodations')]
    #[Groups(['accomodation:read:item', 'accomodation:write', 'announcement:read:item', 'announcement:read'])]
    private Collection $conveniences;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->announcements = new ArrayCollection();
        $this->conveniences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(string $addressLine1): static
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): static
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(int $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    public function isMixedGender(): ?bool
    {
        return $this->mixedGender;
    }

    public function setMixedGender(?bool $mixedGender): static
    {
        $this->mixedGender = $mixedGender;

        return $this;
    }

    public function getOwnershipDeedPath(): ?string
    {
        return $this->ownershipDeedPath;
    }

    public function setOwnershipDeedPath(string $ownershipDeedPath): static
    {
        $this->ownershipDeedPath = $ownershipDeedPath;

        return $this;
    }

    public function getInsuranceCertificatePath(): ?string
    {
        return $this->insuranceCertificatePath;
    }

    public function setInsuranceCertificatePath(string $insuranceCertificatePath): static
    {
        $this->insuranceCertificatePath = $insuranceCertificatePath;

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

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setAccomodation($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAccomodation() === $this) {
                $image->setAccomodation(null);
            }
        }

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

    /**
     * @return Collection<int, Announcement>
     */
    public function getAnnouncements(): Collection
    {
        return $this->announcements;
    }

    public function addAnnouncement(Announcement $announcement): static
    {
        if (!$this->announcements->contains($announcement)) {
            $this->announcements->add($announcement);
            $announcement->setAccomodation($this);
        }

        return $this;
    }

    public function removeAnnouncement(Announcement $announcement): static
    {
        if ($this->announcements->removeElement($announcement)) {
            // set the owning side to null (unless already changed)
            if ($announcement->getAccomodation() === $this) {
                $announcement->setAccomodation(null);
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
}
