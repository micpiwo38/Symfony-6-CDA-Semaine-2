<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Nom du produit'
            ])
            ->add('productCode', TextType::class,[
                'label' => 'Code du produit : Les 3 premières lettres de la catégorie (minuscule ou majuscule) et REGEX 4 chiffres !'
            ])
            ->add('category', TextType::class,[
                'label' => 'Categorie du produit'
            ])
            ->add('price', MoneyType::class,[
                'label' => 'Prix du produit'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            //Création d'un groupe prixProduits qui indique le nom du sous-ensemble des contraintes à appliquer.
            //Il reste maintenant à définir dans l'entité les contraintes qui appartiennent à ce groupe :
            'validation_groups' => ['prixProduit']
        ]);
    }
}
