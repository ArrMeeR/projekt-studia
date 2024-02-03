<?php

declare(strict_types=1);

namespace App\DTO;

use DateTimeImmutable;

final readonly class FeedPostDTO
{
    public function __construct(
        public int $id,
        public string $username,
        public string $content,
        public int $likesCount,
        public int $commentsCount,
        public bool $isLikedByUser,
        public DateTimeImmutable $createdAt,
    ) {
    }
}
