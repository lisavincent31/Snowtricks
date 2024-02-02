<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UsersType;
use App\Entity\Users;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    // Return the register view
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, 
                SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher,
                ValidatorInterface $validator): Response
    {
        // create a user instance
        $user = new Users();
        // create the user form
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);
        
        if($form->isSubmitted()) {
            // validation after the form submit button
            $errors = $validator->validate($user);
            if(count($errors) > 0) {
                $errorsString = (string) $errors;

                // return the view
                return $this->render('auth/register.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $errors
                ]);
            }
            // register user avatar
            $avatar = $form->get('avatar')->getData();
            if($avatar) {
                $originalFilename = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$avatar->guessExtension();
                // move the file to the directory where avatars are stored
                try{
                    $avatar->move(
                        $this->getParameter('medias.avatars_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dd($e);
                    $error = 'Une erreur s\'est produite lors de l\'enregistrement de l\'image.';
                }
                $user->setAvatar($newFilename);

                // hash password before save
                $plainPassword = $form->get('password')->getData();
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                if(!$hashedPassword) {
                    dd('Un problème est arrivé lors du hash du mot de passe.');
                    $error = 'Un problème est arrivé lors du hash du mot de passe.';
                }
                $user->setPassword($hashedPassword);
                // set is_verified to false
                $user->setIsVerified(false);
                // set the user role
                $roles[] = 'ROLE_USER';
                $user->setRoles($roles);
                // save into the database
                $entityManager->persist($user);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_home');
        }
        // return the view
        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
