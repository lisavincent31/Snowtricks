<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UsersRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use App\Repository\TrickRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class SecurityController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private UsersRepository $userRepository;

    public function __construct(EmailVerifier $emailVerifier, UsersRepository $userRepository) 
    {
        $this->emailVerifier = $emailVerifier;
        $this->userRepository = $userRepository;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/send_email_form', name: 'app_send_email')]
    public function send_email_form(Request $request)
    {
        $user = $this->userRepository->findOneByEmail($request->get('send_email'));

        if(null === $user) {
            $this->addFlash(
                'error', 
                'Aucun compte trouvé avec cette adresse email.'
            );
            return $this->redirectToRoute('app_home');
        }
        $this->emailVerifier->sendEmailResetPassword('app_reset_password', $user,
            (new TemplatedEmail())
            ->from(new Address($_ENV['ADDRESS_MAIL'], 'Snowtricks'))
            ->to($user->getEmail())
            ->subject('Modification de votre mot de passe')
            ->htmlTemplate('emails/email_password.html.twig')
        );

        $this->addFlash(
            'success', 
            'Nous vous avons envoyé un email pour modifier votre mot de passe.'
        );
        return $this->redirectToRoute('app_home');
    }

    #[Route(path: '/reset_password', name: 'app_reset_password')]
    public function reset_password(Request $request): Response
    {
        $id = $request->get('id'); // retrieve user_id from url
        // verify if user_id exists and is not null
        if(null === $id) {
            $this->addFlash('error', 'Désolé, une erreur est survenue. Veuillez réessayer.');
            return $this->redirectToRoute('app_home');
        }

        $user = $this->userRepository->find($id);

        // ensure the user exists in persistence
        if(null === $user) {
            $this->addFlash('error', 'Désolé, aucun compte n\'a été trouvé avec cette adresse email.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/reset_password.html.twig', [
            'user' => $user,
        ]);

    }

    #[Route(path: '/update_password', name: 'app_update_password')]
    public function update_password(Request $request, TrickRepository $trickRepository, UserPasswordHasherInterface $passwordHasher,): Response
    {
        $user = $this->userRepository->findOneById($request->get('user_id'));

        if(null === $user) {
            $this->addFlash('error', 'Désolé nous n\'avons pas pu trouver un compte associé à cet email.');
            $this->redirectToRoute('app_home');
        }

        $password = $request->get('password');
        $confirm = $request->get('confirm_password');

        if($password !== $confirm) {
            $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            $this->redirectToRoute('app_reset_password');
        }
        
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $this->userRepository->upgradePassword($user, $hashedPassword);

        $this->addFlash('success', 'Mot de passe modifié avec succès.');

        $tricks = $trickRepository->findAll();
        return $this->render('home/index.html.twig', [
            'title' => "Accueil",
            'tricks' => $tricks
        ]);
    }
}
