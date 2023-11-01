<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use App\Events\EditerProduitsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EditerProduitsSubscriber implements EventSubscriberInterface{

    //Initialisation du systeme de log via LoggerInterface
    public function __construct(private LoggerInterface $logger){}

    //Interface => implements EventSubscriberInterface impose la methode getSubscribedEvents
    //Déclarer les propriétés de la classe ou les méthodes comme statiques les rend accessibles 
    //sans avoir besoin d'une instanciation de la classe. 
    //Ceux-ci peuvent également être accessibles statiquement à l'intérieur d'un objet de classe instancié.
    
    public static function getSubscribedEvents(): array{
        //Appel de la classe EditerProduitsEvent + le nom de la constante qui déclenche la methode onEditerProduits()
        return [
            EditerProduitsEvent::EDITER_PRODUIT => 'onEditerProduits'
        ];
    }
    //Cette methode est la meme que dans EditerProduitsListener()
    public function onEditerProduits(EditerProduitsEvent $event){
        //Creer des lignes de log dans votre fichier var/log/dev.log
        //Afficher le contenus du produits mis à jour
        
        //Son nom a l'aide des getters
        $this->logger->debug('SUBSCRIBER :: Nom du produit mis à jour  : ' . $event->getProduits()->getName());
        $this->logger->debug('SUBSCRIBER :: Description du produit mis à jour : ' . $event->getProduits()->getDescription());
        $this->logger->debug('SUBSCRIBER :: Prix du produit mis à jour : ' . $event->getProduits()->getPrice());
        //etc...
        $this->logger->info('SUBSCRIBER :: Le Produits entier mis a jour : ', ['produits' => $event->getProduits()]);
    }

}