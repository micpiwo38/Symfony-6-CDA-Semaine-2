<?php

namespace App\Events;

use App\Entity\Produits;
use Symfony\Contracts\EventDispatcher\Event;

class EditerProduitsEvent extends Event{

    //Nom de l'evenement a declarer dans le fichier `services.yaml` ou à l'aide des attributs PHP 8 de votre EventListener
    public const EDITER_PRODUIT = 'produits.editer';

    //La cible de l'événements est lié a une instance de l'entité Produits
    public function __construct(private Produits $produits){}

    //On utilise un accesseur (getter) qui delivre l'objet ciblé a l'ecouteur 
    public function getProduits(): Produits{
        return $this->produits;
    }
}