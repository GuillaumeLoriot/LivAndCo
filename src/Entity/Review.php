<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\ReviewRepository;
use App\State\ReviewProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['review:read']
            ]
        ),
        new Get(
            normalizationContext: [
                'groups' => ['review:read:item']
            ]
        ),
        new Post(
            normalizationContext: ['groups' => ['review:read:item']],
            denormalizationContext: ['groups' => ['review:write']],
             processor: ReviewProcessor::class,
            security: "object.getReservation().getUser() == user"
        ),
        new Delete(
            security: "object.getUser() == user"
        )
    ]
)]


class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['review:read', 'review:read:item', 'announcement:read:item'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['review:read', 'review:read:item', 'review:write', 'announcement:read', 'announcement:read:item'])]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['review:read', 'review:read:item', 'review:write', 'announcement:read:item'])]
    private ?string $comment = null;

    #[ORM\Column]
    #[Groups(['review:read', 'review:read:item'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(inversedBy: 'review', cascade: ['persist', 'remove'])]
    #[Groups(['review:read', 'review:read:item', 'review:write'])]
    private ?Reservation $reservation = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[Groups(['review:read', 'review:read:item', 'announcement:read:item'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

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

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;

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
}
