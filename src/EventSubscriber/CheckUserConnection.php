<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class CheckUserConnection implements EventSubscriberInterface{

    public static function getSubscribedEvents():array{
        return [
            LoginSuccessEvent::class => ['onCheckUserConnection', -10],
        ];
    }

    public function onCheckUserConnection(LoginSuccessEvent $event): void{
        //Si la connexion reussi un dump & die affiche les informations de l'utilisateur
        //dd($event);
    }
}