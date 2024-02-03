<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostTagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    #[Route(path: '/posts/{id}', name: 'get_post', methods: ['GET'])]
    public function get(Post $post, PostTagRepository $postTagRepository): Response
    {
        return $this->render('post.html.twig', [
            'post' => $post,
            'popularTags' => $postTagRepository->getPopularTags(),
        ]);
    }
}
