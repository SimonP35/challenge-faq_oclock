<?php

namespace App\Controller\Api;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    /**
     * Get tags collection
     * 
     * @Route("/api/tags", name="api_tags_list", methods="GET")
     */
    public function list(TagRepository $tr): Response
    {
        $tags = $tr->findAll();

        return $this->json(['tags' => $tags,], Response::HTTP_OK, [], ['groups' => 'tags_get']);
    }

    /**
     * Get tag by id
     * 
     * @Route("/api/tags/{id<\d+>}/questions", name="api_tags_show", methods="GET")
     */
    public function show(Tag $tag = null): Response
    {
        if (null === $tag) {
            // Tant qu'à faire, on retourne une réponse JSON
            return $this->json(['error' => 'Le tag demandé n\'existe pas !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['tag' => $tag,], Response::HTTP_OK, [], ['groups' => 'tags_get']);
    }
}
