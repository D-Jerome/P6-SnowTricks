<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    
    /**
     * Email
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $template
     * @param array<string> $context
     * 
     * @return void
     * 
     */
    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array  $context
    ): void {
        // On crée le mail
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("emails/{$template}.html.twig")
            ->context($context)
        ;

        // On envoie le mail
        $this->mailer->send($email);
    }
}
