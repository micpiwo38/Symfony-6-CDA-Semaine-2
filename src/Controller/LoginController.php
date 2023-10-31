<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route("/connexion", name:'app_login')]
    public function connexion(AuthenticationUtils $authenticationUtils):Response{
        //Les erreurs
        $errors = $authenticationUtils->getLastAuthenticationError();
        //Le dernier utilisateurs connceter
        $lastUserName = $authenticationUtils->getLastUsername();

        return $this->render('connexion/connnexion.html.twig',[
            'last_username' => $lastUserName,
            'error' => $errors
        ]);
    }

    #[Route("/deconnexion", name:'app_logout')]
    public function deconnexion():void{
        throw new \LogicException('Vous etes deconnectez !');
    }
}
