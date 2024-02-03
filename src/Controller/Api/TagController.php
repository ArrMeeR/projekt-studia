<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\PostTag;
use App\Repository\PostRepository;
use App\Service\PostService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class TagController extends AbstractController
{

    #[Route(path: '/api/tags/{tag}/posts', name: 'api_get_tag_posts', methods: ['GET'])]
    public function posts(
        #[MapQueryParameter(filter: FILTER_VALIDATE_INT, options: ['min_range' => 0])] int $page,
        PostTag $postTag,
        NormalizerInterface $normalizer,
        PostRepository $postRepository,
        PostService $postService,
    ): Response {
        $posts = $postRepository->getPostsWithTag($postTag, $page);
        $posts = $postService->renderTagsInPosts($posts);

        return $this->json(
            $normalizer->normalize($posts, context: [DateTimeNormalizer::FORMAT_KEY => 'd.m'])
        );
    }
}
