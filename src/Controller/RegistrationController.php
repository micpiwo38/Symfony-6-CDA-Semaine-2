<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Services\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $userPasswordHasher, 
        EntityManagerInterface $entityManager,
        SendEmailService $emailService
        
        ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            //Utilisation de email service
            $emailService->sendEmail(
                'app_verify_email',
                $user,
                'admin@admin.com',
                $user->getEmail(),
                'Activation de votre compte',
                'confirmation_email',
                ['user' => $user]
                );
            
            
            $this->addFlash('warning', 'Merci de valider votre compte a l\'aide du lien envoyé sur votre boite email !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre compte a bien été verifié.');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/renoyer-validation', name: 'app_resend_verif')]
    public function resendEmailVerification(SendEmailService $emailService):Response{
        //Recuperer l'utilisateur connecter
        $user = $this->getUser();
        //dd($user);
        //Si l'utilisateur n'est pas connecté
        if(!$user){
            $this->addFlash('danger', 'Merci de vous connectez pour acceder à cette page !');
            return $this->redirectToRoute('app_login');
        }

        //Si utilisateur à deja verifié son compte
        if($user->isVerified()){
            $this->addFlash('warning', 'Votre compte à deja été activé !');
            return $this->redirectToRoute('app_profile_index');
        }
        //Si l'utilisateur est connecté et que le compte n'est pas verifié
        if($user){
             //Utilisation de email service
             $emailService->sendEmail(
                'app_verify_email',
                $user,
                'admin@admin.com',
                $user->getEmail(),
                'Activation de votre compte',
                'confirmation_email',
                ['user' => $user]
                );

            $this->addFlash('success', 'Un email d\'activation a bien été envoyé  !');
        }
        return $this->render('email/envoi_verification.html.twig');
    }
}
