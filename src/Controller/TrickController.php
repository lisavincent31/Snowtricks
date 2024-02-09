<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Tricks;
use App\Entity\Medias;

class TrickController extends AbstractController
{
    #[Route('/trick/new', name: 'app_trick')]
    public function index(Request $request): Response
    {
        
        $trick = new Tricks;
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }
}
