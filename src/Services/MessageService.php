<?php

namespace App\Services;

use Twig\Extension\AbstractExtension;


//Notre service herite du parent Environement TWIG
//Ici symfony comprend tous seul l'heritage de service
class MessageService extends AbstractExtension{

    public function AfficherMessage(){
        $message = [
            'Symfony 6 est super !',
            'Les services sont des classes PHP',
            'Je vais me faire un café !'
        ];
        $index = array_rand($message);
        //Appel du service + la methode render de Environement + une vue
    
        return $message[$index];
    }
}