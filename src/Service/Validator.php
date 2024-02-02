<?php 

namespace App\Service;

use App\Entity\Users;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator 
{
    public function user(ValidatorInterface $validator): Response 
    {
        $user = new Users();

        

        return new Response('The user is valid.');        
    }
}