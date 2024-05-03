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
use App\Repository\UsersRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\TrickRepository;

use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
// use App\Services\SendMailService;

class AuthController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private UsersRepository $userRepository;

    public function __construct(EmailVerifier $emailVerifier, UsersRepository $userRepository) 
    {
        $this->emailVerifier = $emailVerifier;
        $this->userRepository = $userRepository;
    }

    // Return the register view
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, 
                            EntityManagerInterface $entityManager, 
                            SluggerInterface $slugger, 
                            UserPasswordHasherInterface $passwordHasher,
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
                    $errorsString = (string) $e;

                    // return the view
                    return $this->render('auth/register.html.twig', [
                        'form' => $form->createView(),
                        'errors' => $errorsString
                    ]);;
                }
                $user->setAvatar($newFilename);

                // hash password before save
                $plainPassword = $form->get('password')->getData();
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
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

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                ->from(new Address($_ENV['ADDRESS_MAIL'], 'Snowtricks'))
                ->to($user->getEmail())
                ->subject('Please confirm your Email')
                ->htmlTemplate('emails/confirmation_email.html.twig')
            );

            $this->addFlash('success', 'Votre compte a bien été créé.');
            return $this->redirectToRoute('app_home');
        }
        // return the view
        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/resend_email/{id}', name: 'app_resend_email')]
    public function resendEmail(int $id,TrickRepository $trickRepository): Response
    {
        $user = $this->userRepository->find($id);

        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
            ->from(new Address($_ENV['ADDRESS_MAIL'], 'Snowtricks'))
            ->to($user->getEmail())
            ->subject('Please confirm your Email')
            ->htmlTemplate('emails/confirmation_email.html.twig')
        );
        $this->addFlash('success', 'Email de vérification renvoyé avec succès.');

        $tricks = $trickRepository->findAll();
        return $this->render('home/index.html.twig', [
            'title' => "Accueil",
            'tricks' => $tricks
        ]);
    }

    #[Route('/verify_email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $id = $request->get('id'); // retrieve user_id from url
        // verify if user_id exists and is not null
        if(null === $id) {
            return $this->redirectToRoute('app_home');
        }

        $user = $this->userRepository->find($id);

        // ensure the user exists in persistence
        if(null === $user) {
            return $this->redirectToRoute('app_home');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }

}
