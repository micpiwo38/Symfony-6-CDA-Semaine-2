<?php

namespace App\Validator;

use App\Entity\Product;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProductCodeCompatibilityValidator{
    //La methode devient STATIC, ce qui entraine l'obligation de passer l'entité comme argument ($product)
    //On injecte en paramètre l'entité Product
    //La condition if utilise les getters pour acceder à $category et $productCode qui sont private
    public static function validate(Product $product, ExecutionContextInterface $context, mixed $payload):void{
        //dd($object);
        if(\substr($product->getCategory(), 0, 3) !== \substr($product->getProductCode(), 0, 3)){
            //Le message d'erreur dans le contexte 
            $context->buildViolation('Le code du produit doit contenir les 3 premières lettre de la catégorie en majuscule')
            //Sur la propriété 'category'
            ->atPath('category')
            ->addViolation();
            //dd($context);
        }
    }
}