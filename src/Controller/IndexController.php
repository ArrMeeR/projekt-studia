<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PostTagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(PostTagRepository $postTagRepository): Response
    {
        return $this->render('index.html.twig', [
            'popularTags' => $postTagRepository->getPopularTags(),
        ]);
    }
}
