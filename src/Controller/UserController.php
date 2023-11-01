<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController{

    #[Route('/profile', name:'app_profile')]
    public function changerPassword(
        Request $request
    ):Response{
        $user = $this->getUser();
        //Creation du formulaire
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

        }

        return $this->render('profile/changer_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}