<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    private User $user;

    #[Assert\NotBlank]
    #[Assert\Length(max: 300)]
    #[ORM\Column(length: 300)]
    private string $content;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'likedPosts')]
    #[ORM\JoinTable(name: 'post_like')]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: PostComment::class)]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: PostTag::class, inversedBy: 'posts')]
    private Collection $tags;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addOrRemoveLike(User $user): void
    {
        if ($this->likes->contains($user)) {
            $this->likes->removeElement($user);

            return;
        }

        $this->likes->add($user);
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function setTags(Collection $tags): void
    {
        $this->tags = $tags;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
