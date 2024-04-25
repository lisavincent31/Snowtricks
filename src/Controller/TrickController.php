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
use Symfony\Component\HttpFoundation\RequestStack;

// Entities
use App\Entity\Trick;
use App\Entity\Media;
// Form
use App\Form\TricksType;
// Repository
use App\Repository\TrickRepository;
use App\Repository\MediaRepository;

class TrickController extends AbstractController
{

    #[Route('/trick/new', name: 'app_trick')]
    public function create(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $date = new DateTimeImmutable('now');

        $trick = new Trick();
        // create the form
        $form = $this->createForm(TricksType::class, $trick);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $errors = $validator->validate($user);
            if(count($errors) > 0) {
                // return the view
                return $this->render('trick/index.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors
                ]);
            }
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
                            $media->setUrl('/assets/medias/tricks/'.$newFilename);
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

            // get the videos
            $videos = $request->get('videos');
            foreach($videos as $video) {
                if($video !== "" && $video !== null) {
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
            }

            $trick->setAuthor($this->getUser());
            $slug = $slugger->slug($form->get('category')->getData().'-'.$form->get('name')->getData());
            $trick->setCreatedAt($date);
            $trick->setUpdatedAt($date);
            $trick->setSlug(strtolower($slug));

            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Votre article a été créé avec succès.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/index.html.twig', [
           'form' => $form->createView()
        ]);
    }

    #[Route('/trick/{slug}', name: 'app_trick_show')]
    public function show(string $slug, TrickRepository $repo, MediaRepository $mediaRepo): Response 
    {
        $trick = $repo->findOneBySlug($slug);
        $images = $mediaRepo->findByTypeImage($trick->getId(), 'image');
        $feature = [
            'url' => '/assets/medias/default.jpg',
            'id' => 0
        ];
        if(count($images) > 0) {
            $feature = $images[0];
        }
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'feature' => $feature,
        ]);
    }

    #[Route('/trick/{slug}/edit/', name: 'app_trick_edit')]
    public function edit(string $slug, TrickRepository $repo, SluggerInterface $slugger, MediaRepository $mediaRepo, EntityManagerInterface $entityManager, Request $request): Response
    {
        $trick = $repo->findOneBySlug($slug);
        $images = $mediaRepo->findByTypeImage($trick->getId(), 'image');
        if(count($images) > 0) {
            $feature = $images[0];
        }else{
            $feature = [
                'url' => '/assets/medias/default.jpg',
                'id' => 0
            ];
        }
        $form = $this->createForm(TricksType::class, $trick);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $trick= $form->getData();

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
                            $media->setUrl('/assets/medias/tricks/'.$newFilename);
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

            // get the videos
            $videos = $request->get('videos');
            foreach($videos as $video) {
                if($video !== "" && $video !== null) {
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
            }

            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'L\'article a été modifié avec succès !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'feature' => $feature,
            'form' => $form->createView()
        ]);
    }


    #[Route('/trick/delete/{id}', name: 'app_trick_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager, Request $request)
    {
        $trick = $entityManager->getRepository(Trick::class)->find($id);
        $comments = $trick->getComments();
        foreach($comments as $comment) {
            $trick->removeComment($comment);
            $entityManager->remove($comment);
        }
        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addFlash('success', 'L\'article a été supprimé avec succès !');

        return $this->redirectToRoute('app_home');
    }
    
    #[Route('/trick/{slug}/edit/media/{id}', name: 'app_trick_media_edit')]
    public function updateMedia(
        string $slug, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        SluggerInterface $slugger, 
        TrickRepository $repo): Response
    {
        $trick = $repo->findOneBySlug($request->get('slug'));
        // dd($request->get('slug'));
        
        $image = $request->files->get('image');
        // change name of the media
        $newFilename = $this->changeFilename($image, $slugger);

        if($request->get('id') == 0) {
            $media = $this->registerMedia($image, $newFilename, 'image');
            $media->setCreatedAt(new \DateTime());
            $trick->addMedium($media);

        }else{
            $media = $entityManager->getRepository(Meia::class)->find($request->get('id'));
        }    
        // place the media in the folder
        $image->move(
            $this->getParameter('medias.tricks_directory'),
            $newFilename
        );

        $media->setName($newFilename);
        $media->setType('image');
        $media->setUpdatedAt(new \DateTime());
        $media->setUrl('/assets/medias/tricks/'.$newFilename);
        // register image
        
        $entityManager->persist($media);
        $entityManager->flush();
        $this->addFlash('success', 'Le média a été modifiée avec succès !');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/trick/{slug}/delete/media/{id}', name: 'app_trick_media_delete')]
    public function deleteMedia(int $id, EntityManagerInterface $entityManager, Request $request)
    {
        $media = $entityManager->getRepository(Media::class)->find($id);

        $entityManager->remove($media);
        $entityManager->flush();

        $this->addFlash('success', 'Le média a été supprimé avec succès !');


        return $this->redirectToRoute('app_home');
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
