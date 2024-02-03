<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class SearchResultDTO
{
    public function __construct(public string $label, public string $url)
    {
    }
}
