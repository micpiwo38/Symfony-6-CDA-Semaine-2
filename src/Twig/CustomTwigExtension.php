<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CustomTwigExtension extends AbstractExtension{

    // new TwigFilter = public function __construct(string $name, $callable = null, array $options = []) { }
    public function getFilters(){
        return [
            new TwigFilter('mon_filtre_twig', [$this,'monFiltreTwig']),
        ];
    }
    //Renvoie une chaîne en majuscules
    public function monFiltreTwig($value){
        return strtoupper($value);
    }
}