<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\AddPostDTO;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\PostService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class PostController extends AbstractController
{
    #[Route(path: '/api/posts', name: 'api_add_post', methods: ['POST'], format: 'json')]
    public function add(
        #[MapRequestPayload(acceptFormat: 'json')] AddPostDTO $addPostDTO,
        Security $security,
        PostService $postService,
    ): Response {
        $postService->addPost($addPostDTO->content, $security->getUser());

        return new Response(status: Response::HTTP_CREATED);
    }

    #[Route(path: '/api/posts', name: 'api_posts_feed', methods: ['GET'], format: 'json')]
    public function feed(
        #[MapQueryParameter(filter: FILTER_VALIDATE_INT, options: ['min_range' => 0])] int $page,
        NormalizerInterface $normalizer,
        PostRepository $postRepository,
        PostService $postService,
        Security $security,
    ): Response {
        $posts = $postRepository->getPostsForUserFeed($security->getUser(), $page);
        $posts = $postService->renderTagsInPosts($posts);

        return $this->json(
            $normalizer->normalize($posts, context: [DateTimeNormalizer::FORMAT_KEY => 'd.m'])
        );
    }

    #[Route(path: '/api/posts/{id}/like', name: 'api_like_post', methods: ['POST'], format: 'json')]
    public function like(Post $post, EntityManagerInterface $entityManager, Security $security): Response
    {
        $post->addOrRemoveLike($security->getUser());
        $entityManager->flush();

        return new Response(status: Response::HTTP_CREATED);
    }
}
