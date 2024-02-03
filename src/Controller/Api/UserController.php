<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\PostRepository;
use App\Service\PostService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class UserController extends AbstractController
{

    #[Route(path: '/api/users/{username}/posts', name: 'api_get_user_posts', methods: ['GET'])]
    public function posts(
        #[MapQueryParameter(filter: FILTER_VALIDATE_INT, options: ['min_range' => 0])] int $page,
        User $user,
        NormalizerInterface $normalizer,
        PostRepository $postRepository,
        PostService $postService,
    ): Response {
        $posts = $postRepository->getUserPosts($user, $page);
        $posts = $postService->renderTagsInPosts($posts);

        return $this->json(
            $normalizer->normalize($posts, context: [DateTimeNormalizer::FORMAT_KEY => 'd.m'])
        );
    }

    #[Route(path: '/api/users/{username}/liked-posts', name: 'api_get_user_liked_posts', methods: ['GET'])]
    public function likedPosts(
        #[MapQueryParameter(filter: FILTER_VALIDATE_INT, options: ['min_range' => 0])] int $page,
        User $user,
        NormalizerInterface $normalizer,
        PostRepository $postRepository,
        PostService $postService,
    ): Response {
        $posts = $postRepository->getUserLikedPosts($user, $page);
        $posts = $postService->renderTagsInPosts($posts);

        return $this->json(
            $normalizer->normalize($posts, context: [DateTimeNormalizer::FORMAT_KEY => 'd.m'])
        );
    }

    #[Route(path: '/api/users/{username}/follow', name: 'api_user_follow', methods: ['POST'])]
    public function follow(User $user, EntityManagerInterface $entityManager, Security $security): Response
    {
        $security->getUser()->toggleFollowing($user);
        $entityManager->flush();

        return new Response(status: Response::HTTP_CREATED);
    }
}
