<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Security;
use App\Repository\UsersRepository;

class DashboardController extends AbstractController
{
    /**
     * @var Security
     * @var UsersRepository
     */
    private $security;
    private $userRepository;

    public function __construct(Security $security, UsersRepository $userRepository)
    {
       $this->security = $security;
       $this->userRepository = $userRepository;
    }

    #[Route('{username}/dashboard', name: 'app_dashboard')]
    public function index(string $username): Response
    {
        $user = $this->userRepository->findOneByUsername($username);
        if(!$this->security->getUser() || $user !== $this->security->getUser()) {
            return $this->render('security/errors/403.html.twig');
        }
        return $this->render('dashboard/index.html.twig', [
            'user' => $this->security->getUser()
        ]);
    }

    #[Route('{username}/dashboard/account', name: '')]
    public function getAccount(string $username)
    {
        $user = $this->userRepository->findOneByUsername($username);
        if(!$this->security->getUser() || $user !== $this->security->getUser()) {
            return $this->render('security/errors/403.html.twig');
        }

        return $this->render('dashboard/account.html.twig', [
            'user' => $user
        ]);
    }
}
