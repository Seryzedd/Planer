<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailSender
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, string $template, ?array $data = [])
    {
        $email = (new TemplatedEmail())
            ->from(new Address('planer@noreply.fr', 'test'))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($data)
        ;

        $this->mailer->send($email);
    }
}