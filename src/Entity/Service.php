<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['service:read']
            ]
        ),
        new Get(
            normalizationContext: [
                'groups' => ['service:read:item']
            ]
        ),
        new Post(
            normalizationContext: ['groups' => ['service:read:item']],
            denormalizationContext: ['groups' => ['service:admin:write']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Put(
            normalizationContext: ['groups' => ['service:read:item']],
            denormalizationContext: ['groups' => ['service:write']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Patch(
            normalizationContext: ['groups' => ['service:read:item']],
            denormalizationContext: ['groups' => ['service:write']],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')"
        )
    ]
)]

class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['service:read', 'service:read:item'])]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['service:read', 'service:read:item', 'service:admin:write'])]

    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['service:read', 'service:read:item', 'service:admin:write'])]

    private ?string $description = null;

    /**
     * @var Collection<int, Announcement>
     */
    #[ORM\ManyToMany(targetEntity: Announcement::class, mappedBy: 'services')]
    #[Groups(['service:read:item'])]

    private Collection $announcements;

    public function __construct()
    {
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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
            $announcement->addService($this);
        }

        return $this;
    }

    public function removeAnnouncement(Announcement $announcement): static
    {
        if ($this->announcements->removeElement($announcement)) {
            $announcement->removeService($this);
        }

        return $this;
    }
}
