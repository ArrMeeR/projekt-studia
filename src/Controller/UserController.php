<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostTagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route(path: '/users/{username}', name: 'get_user', methods: ['GET'])]
    public function get(User $user, PostTagRepository $postTagRepository): Response
    {
        return $this->render('user.html.twig', [
            'user' => $user,
            'popularTags' => $postTagRepository->getPopularTags(),
        ]);
    }
}
