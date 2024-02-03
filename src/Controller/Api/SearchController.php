<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route(path: '/api/search', name: 'api_search', methods: ['GET'])]
final class SearchController extends AbstractController
{
    public function __invoke(
        #[MapQueryParameter] string $query,
        SearchService $searchService,
        NormalizerInterface $normalizer,
    ): Response {
        if (empty($query)) {
            return $this->json([]);
        }

        return $this->json($normalizer->normalize($searchService->searchUsersAndTags($query)));
    }
}
