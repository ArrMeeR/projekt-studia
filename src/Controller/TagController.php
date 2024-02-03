<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PostTag;
use App\Repository\PostTagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TagController extends AbstractController
{
    #[Route(path: '/tags/{tag}', name: 'get_tag', methods: ['GET'])]
    public function get(PostTag $postTag, PostTagRepository $postTagRepository): Response
    {
        return $this->render('tag.html.twig', [
            'popularTags' => $postTagRepository->getPopularTags(),
            'tag' => $postTag,
        ]);
    }
}
