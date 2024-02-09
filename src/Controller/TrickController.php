<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Monolog\DateTimeImmutable;

// Entities
use App\Entity\Tricks;
use App\Entity\Medias;
// Form
use App\Form\TricksType;

class TrickController extends AbstractController
{
    #[Route('/trick/new', name: 'app_trick')]
    public function index(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $date = new DateTimeImmutable('now');

        $trick = new Tricks();
        // create the form
        $form = $this->createForm(TricksType::class, $trick);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // get all medias
            $images = $form->get('medias')->getData();

            foreach($images as $image) {
                // foreach medias register in database
                $file = md5(uniqid()).'.'.$image->guessExtension();

                $image->move(
                    $this->getParameter('medias.tricks_directory'),
                    $file
                );

                $media = new Medias();
                $media->setUrl($file);
                $media->setType('image');
                $media->setCreatedAt($date);
                $trick->addMedia($media);
            }

            $featured_image = $form->get('featured_image')->getData();
            if($featured_image) {
                $file = md5(uniqid()).'.'.$featured_image->guessExtension();

                $featured_image->move(
                    $this->getParameter('medias.tricks_directory'),
                    $file
                );

                $media = new Medias();
                $media->setUrl($file);
                $media->setType('image');
                $media->setCreatedAt($date);
                $trick->setFeaturedImage($media);
            }

            $trick->setAuthor($this->getUser());
            $slug = $slugger->slug($form->get('name')->getData());
            $trick->setCreatedAt($date);
            $trick->setSlug(strtolower($slug));

            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirecttoRoute('app_home');
        }

        return $this->render('trick/index.html.twig', [
           'form' => $form->createView()
        ]);
    }
}
