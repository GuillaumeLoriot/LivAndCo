<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['image:read']
            ]
        ),
        new Get(
            normalizationContext: [
                'groups' => ['image:read:item']
            ]
        ),
        new Post(
            normalizationContext: ['groups' => ['image:read:item']],
            denormalizationContext: ['groups' => ['image:write']]
        )
    ]
)]

class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['accommodation:read:item', 'accommodation:read', 'image:read', 'image:read:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['accommodation:read:item', 'accommodation:read', 'image:read', 'image:read:item', 'image:write'])]
    private ?string $path = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[Groups(['image:write'])]

    private ?Accomodation $accomodation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

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
}
