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
use App\Entity\Trick;
use App\Entity\Media;
// Form
use App\Form\TricksType;

class TrickController extends AbstractController
{

    #[Route('/trick/new', name: 'app_trick')]
    public function index(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $date = new DateTimeImmutable('now');

        $trick = new Trick();
        // create the form
        $form = $this->createForm(TricksType::class, $trick);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            // get all images
            $images = $request->files->get('images');
            if($images) {
                foreach($images as $image) {
                    // change name of the media
                    $newFilename = $this->changeFilename($image, $slugger);
                    
                    // place the media in the folder
                    $image->move(
                        $this->getParameter('medias.tricks_directory'),
                        $newFilename
                    );
    
                    try{
                        // register image
                        $media = $this->registerMedia($image, $newFilename, 'image');
                        if($media) {
                            $media->setUrl($newFilename);
                            $trick->addMedium($media);
                        }
                    }catch(GeneralException $e){
                        $error = 'Nous n\'avons pas pu enregistrer l\'image '.$image->getClientOriginalName();
                        return $this->render('trick/index.html.twig', [
                            'form' => $form->createView(),
                            'error' => $error
                        ]);
                    }
                }
            }

            // get the featured_image
            $featured_image = $form->get('featured_image')->getData();
            if($featured_image) {

                // change name of the featured_image
                $newFilename = $this->changeFilename($featured_image, $slugger);
                
                // place the featured_image in the folder
                $featured_image->move(
                    $this->getParameter('medias.tricks_directory'),
                    $newFilename
                );

                // register the featured_image in database
                try{
                    // register image
                    $media = $this->registerMedia($featured_image, $newFilename, 'image');
                    if($media) {
                        $media->setUrl($newFilename);
                        $trick->setFeaturedImage($media);
                    }
                }catch(GeneralException $e){
                    $error = 'Nous n\'avons pas pu enregistrer l\'image '.$image->getClientOriginalName();
                    return $this->render('trick/index.html.twig', [
                        'form' => $form->createView(),
                        'error' => $error
                    ]);
                }
            }
            // get the videos
            $videos = $request->get('videos');
            foreach($videos as $video) {
                // register media in database
                try{
                    // register image
                    $newFilename = 'video-'.uniqid();
                    $media = $this->registerMedia($video, $newFilename, 'video');
                    if($media) {
                        $media->setUrl($video);
                        $trick->addMedium($media);
                    }
                }catch(GeneralException $e){
                    $error = 'Nous n\'avons pas pu enregistrer la vidéo.';
                    return $this->render('trick/index.html.twig', [
                        'form' => $form->createView(),
                        'error' => $error
                    ]);
                }
            }

            $trick->setAuthor($this->getUser());
            $slug = $slugger->slug($form->get('name')->getData());
            $trick->setCreatedAt($date);
            $trick->setUpdatedAt($date);
            $trick->setSlug(strtolower($slug));

            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/index.html.twig', [
           'form' => $form->createView()
        ]);
    }

    #[Route('/trick/edit/{id}', name: 'app_trick_edit')]
    public function edit(int $id)
    {

    }

    #[Route('/trick/delete/{id}', name: 'app_trick_delete')]
    public function delete(int $id)
    {

    }

    public function changeFilename(UploadedFile $file, SluggerInterface $slugger)
    {
        try {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        }catch(GeneraleException $e) {
            return redirect()->back()->with('error', 'Error while changing the name of the file.');
        }
        return $newFilename;
    }

    public function registerMedia($media, string $name, string $type)
    {
        $date = new DateTimeImmutable('now');

        // register media in database
        $media = new Media();
        $media->setName($name);
        $media->setType($type);
        $media->setCreatedAt($date);
        $media->setUpdatedAt($date);
        return $media;
    }
}
