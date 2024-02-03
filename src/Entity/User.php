<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: 'email')]
#[UniqueEntity(fields: 'username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(unique: true)]
    private string $email;

    #[Assert\Length(min: 3, max: 32)]
    #[ORM\Column(length: 32, unique: true)]
    private string $username;

    #[Assert\Length(min: 8)]
    #[ORM\Column]
    private string $password;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 300)]
    #[ORM\Column(length: 300, nullable: true)]
    private ?string $about;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Post::class)]
    private Collection $posts;

    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'likes')]
    private Collection $likedPosts;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'following')]
    private Collection $followers;

    #[ORM\JoinTable(name: 'user_following')]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'followers')]
    private Collection $following;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->likedPosts = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): void
    {
        $this->about = $about;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getLikedPosts(): Collection
    {
        return $this->likedPosts;
    }

    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function toggleFollowing(User $user): void
    {
        if ($this->following->contains($user)) {
            $this->following->removeElement($user);

            return;
        }

        $this->following->add($user);
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
    }
}
