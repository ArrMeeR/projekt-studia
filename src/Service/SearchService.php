<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\SearchResultDTO;
use App\Entity\PostTag;
use App\Entity\User;
use App\Repository\PostTagRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class SearchService
{
    public function __construct(
        private UserRepository $userRepository,
        private PostTagRepository $postTagRepository,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @return SearchResultDTO[]
     */
    public function searchUsersAndTags(string $query): array
    {
        $query = str_replace(['#', '@'], '', $query);

        $users = $this->userRepository->searchByUsername($query);
        $tags = $this->postTagRepository->searchByTag($query);

        $result = [];

        /** @var User $user */
        foreach ($users as $user) {
            $result[] = new SearchResultDTO(
                '@' . $user->getUsername(),
                $this->urlGenerator->generate('get_user', ['username' => $user->getUsername()]),
            );
        }

        /** @var PostTag $tag */
        foreach ($tags as $tag) {
            $result[] = new SearchResultDTO(
                '#' . $tag->getTag(),
                $this->urlGenerator->generate('get_tag', ['tag' => $tag->getTag()]),
            );
        }

        usort($result, static function (SearchResultDTO $a, SearchResultDTO $b) use ($query) {
            return similar_text($a->label, $query) <=> similar_text($b->label, $query);
        });

        return $result;
    }
}
