<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Monolog\DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
// Model
use App\Entity\Comment;
// Repositories
use App\Repository\TrickRepository;


class CommentController extends AbstractController
{
    #[Route('trick/{slug}/comment/new', name:"app_trick_comment")]
    public function new(TrickRepository $repo, Request $request, $slug, EntityManagerInterface $entityManager): Response
    {
        // get the date now
        $date = new DateTimeImmutable('now');
        // find the trick
        $trick = $repo->findOneBySlug($slug);
        // create a new comment
        $comment = new Comment();
        $comment->setContent( $request->request->get('content'));
        $comment->setTrick($trick);
        $comment->setAuthor($this->getUser());
        $comment->setCreatedAt($date);

        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Votre commentaire a bien été posté.');
        return $this->redirectToRoute('app_home');
    }
}
