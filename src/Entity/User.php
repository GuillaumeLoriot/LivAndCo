<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\UserController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\State\UserPasswordHasherProcessor;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: [
                'groups' => ['user:read']
            ],
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Get(
            name: 'Me',
            uriTemplate: '/me',
            controller: UserController::class,
            normalizationContext: ['groups' => ['user:read:item']],
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
        ),
        new Get(
            security: "object == user"
        ),
        new Post(
            name: 'create_user',
            normalizationContext: ['groups' => ['user:read']],
            denormalizationContext: ['groups' => ['user:write']],
            processor: UserPasswordHasherProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => ['user:write']],
            normalizationContext: ['groups' => ['user:read:item']],
            processor: UserPasswordHasherProcessor::class,
            security: "object == user"
        ),
        new Delete(
            security: "object == user"
        )
    ]
)]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'user:read:item', 'accomodation:read:item', 'announcement:read:item', 'review:read', 'review:read:item', 'message:read', 'reservation:read:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read:item', 'user:write', 'reservation:read:item'])]
    #[Assert\Email()]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user:read:item'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:write'])]
    #[Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} charactères')]
    private ?string $password = null;

    #[ORM\Column(length: 60)]
    #[Groups(['user:read:item', 'user:write', 'message:read', 'reservation:read:item'])]

    private ?string $lastName = null;

    #[ORM\Column(length: 60)]
    #[Groups(['user:read:item', 'user:write', 'announcement:read:item', 'message:read', 'reservation:read:item'])]

    private ?string $firstName = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['user:read:item', 'user:write'])]

    private ?\DateTimeImmutable $birthDate = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['user:read:item', 'user:write'])]

    private ?string $gender = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read:item', 'user:write'])]

    private ?string $billingAddress = null;

    #[ORM\Column]
    #[Groups(['user:read', 'user:read:item', 'accomodation:read:item', 'message:read', 'reservation:read:item'])]
    private ?bool $isVerified = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'user:read:item', 'accomodation:read:item', 'announcement:read:item', 'review:read', 'review:read:item', 'message:read', 'reservation:read:item'])]
    private ?string $profilePicture = null;

    #[ORM\Column]
    #[Groups(['user:read', 'user:read:item', 'accomodation:read:item', 'announcement:read:item', 'reservation:read:item'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    #[Groups(['user:read:item'])]

    private Collection $sentMessages;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'receiver')]
    #[Groups(['user:read:item'])]
    private Collection $receivedMessages;

    /**
     * @var Collection<int, Accomodation>
     */
    #[ORM\OneToMany(targetEntity: Accomodation::class, mappedBy: 'owner')]
    #[Groups(['user:read:item'])]

    private Collection $accomodations;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'user')]
    #[Groups(['user:read:item'])]
    private Collection $reservations;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'user')]
    #[Groups(['user:read:item'])]
    private Collection $reviews;

    #[ORM\Column(length: 30, nullable: true)]
    #[Groups(['user:write', 'user:read:item'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['user:read', 'user:write', 'user:read:item', 'announcement:read:item', 'review:read', 'review:read:item', 'reservation:read:item'])]
    private ?string $occupation = null;

    public function __construct()
    {
        $this->sentMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->accomodations = new ArrayCollection();
        $this->announcements = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string $role
     */
    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(string $billingAddress): static
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

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

    /**
     * @return Collection<int, Message>
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function addSentMessage(Message $sentMessage): static
    {
        if (!$this->sentMessages->contains($sentMessage)) {
            $this->sentMessages->add($sentMessage);
            $sentMessage->setSender($this);
        }

        return $this;
    }

    public function removeSentMessage(Message $sentMessage): static
    {
        if ($this->sentMessages->removeElement($sentMessage)) {
            // set the owning side to null (unless already changed)
            if ($sentMessage->getSender() === $this) {
                $sentMessage->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function addReceivedMessage(Message $receivedMessage): static
    {
        if (!$this->receivedMessages->contains($receivedMessage)) {
            $this->receivedMessages->add($receivedMessage);
            $receivedMessage->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedMessage(Message $receivedMessage): static
    {
        if ($this->receivedMessages->removeElement($receivedMessage)) {
            // set the owning side to null (unless already changed)
            if ($receivedMessage->getReceiver() === $this) {
                $receivedMessage->setReceiver(null);
            }
        }

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
            $accomodation->setOwner($this);
        }

        return $this;
    }

    public function removeAccomodation(Accomodation $accomodation): static
    {
        if ($this->accomodations->removeElement($accomodation)) {
            // set the owning side to null (unless already changed)
            if ($accomodation->getOwner() === $this) {
                $accomodation->setOwner(null);
            }
        }

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
            $reservation->setUser($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    public function setOccupation(?string $occupation): static
    {
        $this->occupation = $occupation;

        return $this;
    }
}
