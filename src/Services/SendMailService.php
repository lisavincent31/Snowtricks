<?php 

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService 
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(array $data)
    {
        $email = (new TemplatedEmail())
        ->from('hello@snowtricks.com')
        ->to($data['userMail'])
        ->subject($data['subject'])
        ->htmlTemplate("emails/".$data['template'].".html.twig")
        ;

        // send email
        $this->mailer->send($email);
    }
}