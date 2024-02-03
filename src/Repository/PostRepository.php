<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\FeedPostDTO;
use App\Entity\Post;
use App\Entity\PostTag;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

final class PostRepository extends ServiceEntityRepository
{
    private const int PAGE_SIZE = 10;

    public function __construct(ManagerRegistry $registry, private readonly Security $security)
    {
        parent::__construct($registry, Post::class);
    }

    public function getPostsForUserFeed(User $user, int $page): array
    {
        $offset = self::PAGE_SIZE * ($page - 1);

        $result = $this
            ->getEntityManager()
            ->getConnection()
            ->executeQuery(
                'SELECT 
	                p.id, p.content, p.created_at, u.username,
	                (SELECT COUNT(*) FROM post_comment pc WHERE pc.post_id = p.id) as commentsCount,
	                (SELECT COUNT(*) FROM post_like pl WHERE pl.post_id = p.id) as likesCount,
	                (SELECT COUNT(*) FROM post_like pl WHERE pl.post_id = p.id AND pl.user_id = :userId) as isLikedByUser
                FROM 
	                post p 
	                JOIN `user` u ON u.id = p.user_id
                WHERE u.id IN (SELECT user_target FROM user_following WHERE user_source = :userId) OR u.id = :loggedInUserId
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset',
                [
                    'userId' => $user->getId(),
                    'loggedInUserId' => $this->security->getUser()->getId(),
                    'limit' => self::PAGE_SIZE,
                    'offset' => $offset,
                ],
                [
                    'limit' => ParameterType::INTEGER,
                    'offset' => ParameterType::INTEGER,
                ],
            )
            ->fetchAllAssociative()
        ;

        $posts = [];
        foreach ($result as $post) {
            $posts[] = new FeedPostDTO(
                $post['id'],
                $post['username'],
                $post['content'],
                $post['likesCount'],
                $post['commentsCount'],
                (bool) $post['isLikedByUser'],
                new DateTimeImmutable($post['created_at']),
            );
        }

        return $posts;
    }

    public function getUserPosts(User $user, int $page): array
    {
        $offset = self::PAGE_SIZE * ($page - 1);

        $result = $this
            ->getEntityManager()
            ->getConnection()
            ->executeQuery(
                'SELECT 
	                p.id, p.content, p.created_at, u.username,
	                (SELECT COUNT(*) FROM post_comment pc WHERE pc.post_id = p.id) as commentsCount,
	                (SELECT COUNT(*) FROM post_like pl WHERE pl.post_id = p.id) as likesCount,
	                (SELECT COUNT(*) FROM post_like pl WHERE pl.post_id = p.id AND pl.user_id = :loggedInUserId) as isLikedByUser
                FROM 
	                post p 
	                JOIN `user` u ON u.id = p.user_id
                WHERE p.user_id = :userId
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset',
                [
                    'loggedInUserId' => $this->security->getUser()->getId(),
                    'userId' => $user->getId(),
                    'limit' => self::PAGE_SIZE,
                    'offset' => $offset,
                ],
                [
                    'limit' => ParameterType::INTEGER,
                    'offset' => ParameterType::INTEGER,
                ],
            )
            ->fetchAllAssociative()
        ;

        $posts = [];
        foreach ($result as $post) {
            $posts[] = new FeedPostDTO(
                $post['id'],
                $post['username'],
                $post['content'],
                $post['likesCount'],
                $post['commentsCount'],
                (bool) $post['isLikedByUser'],
                new DateTimeImmutable($post['created_at']),
            );
        }

        return $posts;
    }

    public function getUserLikedPosts(User $user, int $page): array
    {
        $offset = self::PAGE_SIZE * ($page - 1);

        $result = $this
            ->getEntityManager()
            ->getConnection()
            ->executeQuery(
                'SELECT 
	                p.id, p.content, p.created_at, u.username,
	                (SELECT COUNT(*) FROM post_comment pc WHERE pc.post_id = p.id) as commentsCount,
	                (SELECT COUNT(*) FROM post_like pl WHERE pl.post_id = p.id) as likesCount,
	                (SELECT COUNT(*) FROM post_like pl WHERE pl.post_id = p.id AND pl.user_id = :loggedInUserId) as isLikedByUser
                FROM 
	                post p 
	                JOIN `user` u ON u.id = p.user_id
                WHERE p.id IN (SELECT post_id FROM post_like WHERE user_id = :userId)
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset',
                [
                    'loggedInUserId' => $this->security->getUser()->getId(),
                    'userId' => $user->getId(),
                    'limit' => self::PAGE_SIZE,
                    'offset' => $offset,
                ],
                [
                    'limit' => ParameterType::INTEGER,
                    'offset' => ParameterType::INTEGER,
                ],
            )
            ->fetchAllAssociative()
        ;

        $posts = [];
        foreach ($result as $post) {
            $posts[] = new FeedPostDTO(
                $post['id'],
                $post['username'],
                $post['content'],
                $post['likesCount'],
                $post['commentsCount'],
                (bool) $post['isLikedByUser'],
                new DateTimeImmutable($post['created_at']),
            );
        }

        return $posts;
    }

    public function getPostsWithTag(PostTag $tag, int $page): array
    {
        $offset = self::PAGE_SIZE * ($page - 1);

        $result = $this
            ->getEntityManager()
            ->getConnection()
            ->executeQuery(
                'SELECT 
	                p.id, p.content, p.created_at, u.username,
	                (SELECT COUNT(*) FROM post_comment pc WHERE pc.post_id = p.id) as commentsCount,
	                (SELECT COUNT(*) FROM post_like pl WHERE pl.post_id = p.id) as likesCount,
	                (SELECT COUNT(*) FROM post_like pl WHERE pl.post_id = p.id AND pl.user_id = :loggedInUserId) as isLikedByUser
                FROM 
	                post p 
	                JOIN `user` u ON u.id = p.user_id
                WHERE p.id IN (SELECT post_id FROM post_post_tag WHERE post_tag_id = :tagId)
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset',
                [
                    'loggedInUserId' => $this->security->getUser()->getId(),
                    'tagId' => $tag->getId(),
                    'limit' => self::PAGE_SIZE,
                    'offset' => $offset,
                ],
                [
                    'limit' => ParameterType::INTEGER,
                    'offset' => ParameterType::INTEGER,
                ],
            )
            ->fetchAllAssociative()
        ;

        $posts = [];
        foreach ($result as $post) {
            $posts[] = new FeedPostDTO(
                $post['id'],
                $post['username'],
                $post['content'],
                $post['likesCount'],
                $post['commentsCount'],
                (bool) $post['isLikedByUser'],
                new DateTimeImmutable($post['created_at']),
            );
        }

        return $posts;
    }
}
