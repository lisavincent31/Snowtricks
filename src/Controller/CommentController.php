<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommentController extends AbstractController
{
    #[Route('/comments/load-more', name:"load_more_comments", methods:"POST")]
    public function loadMoreComments(Request $request): JsonResponse
    {
        dd($request);

        return $this->json($comments);
    }
}
