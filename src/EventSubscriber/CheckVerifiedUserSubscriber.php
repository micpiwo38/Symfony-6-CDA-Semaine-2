<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface{

    public static function getSubscribedEvents():array{
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event):void{
        $passport = $event->getPassport();
        $user = $passport->getUser();
        if(!$user instanceof User){
            throw new \Exception('Utilisateur inconnu !');
        }
        if(!$user->isVerified()){
            throw new AuthenticationException();
        }
    }
    
}