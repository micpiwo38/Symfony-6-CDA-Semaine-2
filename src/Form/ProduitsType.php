<?php

namespace App\Form;

use App\Entity\Produits;
use App\Entity\Categories;
use App\Entity\Distributeurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Nom du produit'
            ])
            ->add('description', TextareaType::class,[
                'label' => 'Description du produit'
            ])
            ->add('image', FileType::class,[
                'required' => false,
                'mapped' => false
            ])
            ->add('price', MoneyType::class,[
                'label' => 'Prix du produit'
            ])
            ->add('reference', ReferencesType::class)
            ->add('distributeur', EntityType::class,[
                'class' => Distributeurs::class,
                'multiple' => true,
                'choice_label' => 'name'
            ])
            ->add('Categorie', EntityType::class,[
                'class' => Categories::class,
                'choice_label' => 'name' 
            ])
            ->add('submit', SubmitType::class,[
                'label' => 'Ajouter produit'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
