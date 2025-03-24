<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendUserCredentialsEmail(string $recipientEmail, string $username, string $password, string $link): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@adr-wave.com', 'Votre Application'))
            ->to($recipientEmail)
            ->subject('Vos identifiants de connexion')
            ->htmlTemplate('emails/user_credentials.html.twig')
            ->context([
                'name'     => $username,
                'password' => $password,
                'username' => $recipientEmail,
                'link'     => $link,
            ]);

        $this->mailer->send($email);
    }
}