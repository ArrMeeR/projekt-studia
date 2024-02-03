<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\FeedPostDTO;
use App\Entity\Post;
use App\Entity\PostTag;
use App\Entity\User;
use App\Repository\PostTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final readonly class PostService
{
    public function __construct(
        private PostTagRepository $postTagRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function addPost(string $content, User $user): void
    {
        $post = new Post();

        $content = trim($content);
        if (empty($content)) {
            throw new UnprocessableEntityHttpException('Post cannot be empty');
        }

        $post->setContent($content);
        $post->setUser($user);

        preg_match_all('/#[a-zA-Z]+/', $content, $tags);
        $tags = array_map(function (string $tag) { return strtolower(ltrim($tag, '#')); }, $tags[0]);
        $tags = array_unique($tags);

        $existingTags = [];
        foreach ($this->postTagRepository->findByTags($tags) as $existingTag) {
            $existingTags[$existingTag->getTag()] = $existingTag;
        }

        $tagsToAdd = [];
        foreach ($tags as $tag) {
            if (array_key_exists($tag, $existingTags)) {
                $tagsToAdd[] = $existingTags[$tag];

                continue;
            }

            $newTag = new PostTag();
            $newTag->setTag($tag);
            $this->entityManager->persist($newTag);

            $tagsToAdd[] = $newTag;
        }

        $post->setTags(new ArrayCollection($tagsToAdd));

        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    /**
     * @param FeedPostDTO[] $posts
     * @return FeedPostDTO[]
     */
    public function renderTagsInPosts(array $posts): array
    {
        $result = [];
        foreach ($posts as $post) {
            $tagLink = '<a href="/tags/${2}/">${1}</a>';
            $content = preg_replace('/(#([a-zA-Z]+))/', $tagLink, $post->content);
            $result[] = new FeedPostDTO(
                $post->id,
                $post->username,
                $content,
                $post->likesCount,
                $post->commentsCount,
                $post->isLikedByUser,
                $post->createdAt,
            );
        }

        return $result;
    }
}
