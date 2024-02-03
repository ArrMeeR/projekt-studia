<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class PopularTagDTO
{
    public function __construct(public int $rank, public string $tag, public int $postsCount)
    {
    }
}
