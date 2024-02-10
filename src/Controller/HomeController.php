<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TrickRepository $repository): Response
    {
        $tricks = $repository->findAll();
        return $this->render('home/index.html.twig', [
            'title' => "Accueil",
            'tricks' => $tricks
        ]);
    }
}
