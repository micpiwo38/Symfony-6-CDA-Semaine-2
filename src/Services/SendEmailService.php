<?php

namespace App\Services;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendEmailService{

    //Par defaut Symfony 6 envoie le mail a la table phpmyadmin : messanger_messages
    //CONFIGURATION
    //config -> packages -> messenger.yaml
    //Commenter la ligne suivante : # Symfony\Component\Mailer\Messenger\SendEmailMessage: async

    public function __construct(private MailerInterface $mailer, private EmailVerifier $emailVerifier){}
    //Les paramètres : EmailVerifier + utilisteur + adresse de l'expéditeur + adresse du destinataire + sujet + Template Twig + tableau d'options
    public function sendEmail(string $email_verifier, User $user, string $from, string $to, string $subject, string $template, array $context = []):void{
        $email = (new TemplatedEmail())
        ->from($from)
        ->to($to)
        ->subject($subject)
        ->htmlTemplate("registration/$template.html.twig")
        ->context($context);

        //Envoi de l'email
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
                $user,
                $email
        );
    }
}