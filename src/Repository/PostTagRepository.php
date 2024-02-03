<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\PopularTagDTO;
use App\Entity\PostTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class PostTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostTag::class);
    }

    /**
     * @param string[] $tags
     * @return PostTag[]
     */
    public function findByTags(array $tags): array
    {
        return $this
            ->createQueryBuilder('t')
            ->where('t.tag in (:tags)')
            ->setParameter('tags', $tags)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getPopularTags(): array
    {
        $tags = $this
            ->getEntityManager()
            ->getConnection()
            ->executeQuery(
                'SELECT pt.tag, COUNT(p.id) as postsCount 
                FROM post_post_tag ppt 
                JOIN post p ON p.id = ppt.post_id
                JOIN post_tag pt ON pt.id = ppt.post_tag_id 
                GROUP BY pt.id 
                ORDER BY postsCount DESC LIMIT 10'
            )
            ->fetchAllAssociative()
        ;

        $rank = 1;
        $result = [];
        foreach ($tags as $tag) {
            $result[] = new PopularTagDTO($rank, $tag['tag'], $tag['postsCount']);
            $rank++;
        }

        return $result;
    }

    public function searchByTag(string $tag): array
    {
        return $this
            ->createQueryBuilder('pt')
            ->where('pt.tag like :tag')
            ->setParameter('tag', '%' . $tag . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }
}
