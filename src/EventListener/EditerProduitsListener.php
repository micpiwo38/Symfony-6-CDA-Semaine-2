<?php

namespace App\EventListener;

use App\Entity\Produits;
use Psr\Log\LoggerInterface;
use App\Events\EditerProduitsEvent;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

//L'attribut  permet d'accrocher l'écouteur à un certain évènement, 
// évitant d'avoir à le configurer dans le fichier `services.yaml
//#[AsEntityListener(event: EditerProduitsEvent::EDITER_PRODUIT, method: 'onEditerProduits', entity: Produits::class)]
class EditerProduitsListener{
    //Initialisation du systeme de log via LoggerInterface
    public function __construct(private LoggerInterface $logger){}
    //On passe en paramètre de cette methode l'évenement creer EditerProduitsEvent
    public function onEditerProduits(EditerProduitsEvent $event){
        //Creer des lignes de log dans votre fichier var/log/dev.log
        //Afficher le contenus du produits mis à jour
        
        //Son nom a l'aide des getters
        $this->logger->debug('Nom du produit mis à jour : ' . $event->getProduits()->getName());
        $this->logger->debug('Description du produit mis à jour : ' . $event->getProduits()->getDescription());
        $this->logger->debug('Prix du produit mis à jour : ' . $event->getProduits()->getPrice());
        //etc...
        $this->logger->info('Le Produits entier mis a jour : ', ['produits' => $event->getProduits()]);
    }
}