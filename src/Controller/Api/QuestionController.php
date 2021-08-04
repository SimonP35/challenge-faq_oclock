<?php

namespace App\Controller\Api;

use App\Entity\Tag;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionController extends AbstractController
{
    /**
     * Get questions collection
     * 
     * @Route("/api/questions", name="api_questions_list", methods="GET")
     */
    public function list(QuestionRepository $qr): Response
    {
        $questions = $qr->findAll();

        return $this->json(['questions' => $questions], Response::HTTP_OK, [], ['groups' => 'questions_get']);
    }

    /**
     * Get question by id
     * 
     * @Route("/api/questions/{id<\d+>}", name="api_questions_show", methods="GET")
     */
    public function show(Question $question = null): Response
    {
        if (null === $question) {
            // Tant qu'à faire, on retourne une réponse JSON
            return $this->json(['error' => 'La question demandée n\'existe pas !'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['question' => $question], Response::HTTP_OK, [], ['groups' => 'questions_get']);
    }

    /**
     * Get question by id
     * 
     * @Route("/api/questions/add", name="api_questions_add", methods="POST")
     */
    public function add(SerializerInterface $serializer, Request $request)
    {

    }

    //?=================================
    //?    Autre solution vu en cours
    //?=================================
    /**
     * @ Route("/api/tags/{id<\d+>}/questions", name="api_questions_by_tags")
     */
    public function getQuestionsByTag(Tag $tag = null, QuestionRepository $questionRepository): Response
    {
        if (null === $tag) {
            return $this->json(['message' => 'Tag non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // Toutes les questions d'un tag donné (et questions non bloquées)
        // (voir la méthode custom dans le Repository)
        $allQuestionsFromTag = $questionRepository->findByTag($tag);

        return $this->json($allQuestionsFromTag, Response::HTTP_OK, [], ['groups' => 'questions_get']);
    }

}
