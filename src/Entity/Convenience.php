<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ConvenienceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ConvenienceRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['convenience:read']
            ]
        ),
        new Get(
            normalizationContext: [
                'groups' => ['convenience:read:item']
            ]
        ),
        new Post(
            normalizationContext: ['groups' => ['convenience:read:item']],
            denormalizationContext: ['groups' => ['convenience:admin:write']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            normalizationContext: ['groups' => ['convenience:read:item']],
            denormalizationContext: ['groups' => ['convenience:write']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Patch(
            normalizationContext: ['groups' => ['convenience:read:item']],
            denormalizationContext: ['groups' => ['convenience:write']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ]
)]
class Convenience
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['convenience:read', 'convenience:read:item'])]

    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Groups(['convenience:read', 'convenience:read:item', 'convenience:admin:write', 'announcement:read'])]

    private ?string $name = null;


    #[ORM\Column(length: 50)]
    #[Groups(['convenience:read', 'convenience:read:item', 'convenience:admin:write', 'announcement:read'])]

    private ?string $icon = null;

    /**
     * @var Collection<int, Accomodation>
     */
    #[ORM\ManyToMany(targetEntity: Accomodation::class, mappedBy: 'conveniences')]
    #[Groups(['convenience:read:item'])]

    private Collection $accomodations;

    /**
     * @var Collection<int, Announcement>
     */
    #[ORM\ManyToMany(targetEntity: Announcement::class, mappedBy: 'conveniences')]
    #[Groups(['convenience:read:item'])]

    private Collection $announcements;

    public function __construct()
    {
        $this->accomodations = new ArrayCollection();
        $this->announcements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return Collection<int, Accomodation>
     */
    public function getAccomodations(): Collection
    {
        return $this->accomodations;
    }

    public function addAccomodation(Accomodation $accomodation): static
    {
        if (!$this->accomodations->contains($accomodation)) {
            $this->accomodations->add($accomodation);
            $accomodation->addConvenience($this);
        }

        return $this;
    }

    public function removeAccomodation(Accomodation $accomodation): static
    {
        if ($this->accomodations->removeElement($accomodation)) {
            $accomodation->removeConvenience($this);
        }

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
            $announcement->addConvenience($this);
        }

        return $this;
    }

    public function removeAnnouncement(Announcement $announcement): static
    {
        if ($this->announcements->removeElement($announcement)) {
            $announcement->removeConvenience($this);
        }

        return $this;
    }
}
