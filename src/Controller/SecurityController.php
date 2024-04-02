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
            ->from(new Address('lisa.vincent31150@gmail.com', 'Snowtricks'))
            ->to($user->getEmail())
            ->subject('Modification de votre mot de passe')
            ->htmlTemplate('auth/reset_password.html.twig')
        );

        $this->addFlash(
            'success', 
            'Nous vous avons envoyé un email pour modifier votre mot de passe.'
        );
        return $this->redirectToRoute('app_home');
    }

    #[Route(path: '/reset_password', name: 'app_reset_password')]
    public function reset_password(): void
    {
        
    }
}
