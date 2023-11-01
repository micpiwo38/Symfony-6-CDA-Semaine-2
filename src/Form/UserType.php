<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'label' => 'Votre email'
            ])
            
            //ICI LES CHAMPS POUR MODIFIER VOTRE MOT DE PASSE

            //PRE_SET_DATA | Déclenché juste avant l'affichage du formulaire et déclenche la methode preRemplirEmail()
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preRemplirEmail'])
            ->add('submit', SubmitType::class,[
                'label' => 'Modifier le mot de passe'
            ])
        ;
    }

    public function preRemplirEmail(FormEvent $event) :void{
        //Recupereration des données de l'utilisateur connecté
        $user = $event->getData();
         // Si l'utilisateur existe et est une instance de entité USer, pré-remplissez l'email à l'aide du setter
         if($user instanceof User){
            $event->getForm()->get('email')->setData($user->getEmail());
         }
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
