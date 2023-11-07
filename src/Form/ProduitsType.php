<?php

namespace App\Form;

use App\Entity\Produits;
use App\Entity\Categories;
use App\Entity\Distributeurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[//Champ de type <input type='text'/>
                'label' => 'Nom du produit'
            ])
            ->add('description', TextareaType::class,[//Champ de type <textarea></textarea>
                'label' => 'Description du produit'
            ])
            ->add('price', MoneyType::class,[//Champ de type <input type='text'/>
                'label' => 'Prix du produit'
            ])
            ->add('reference', ReferencesType::class)//Imbriquer le formulaire ReferenceType 
                
            ->add('distributeur', EntityType::class,[//Champ de type Entité
                'class' => Distributeurs::class,//Appel de l'entité src/Entity/Distributeurs.php
                'multiple' => true,//Autorise plusieur entrées
                'choice_label' => 'name',//Le champ de l'entité References à afficher
            ])
            ->add('categorie', EntityType::class,[//Champ de type Entité
                'label' => 'Catégorie du produit',
                'class' => Categories::class,//Appel de l'entité src/Entity/Categories.php
                'choice_label' => 'name'//Le champ de l'entité References à afficher
            ])
            //CollectionType genere un data-protoype = gabarit de sous formulaire vide
            ->add('photos', CollectionType::class,[
                'entry_type' => PhotosType::class,
                'allow_add' => true, //ajoute des items à la collection
                'allow_delete' => true, //
                'by_reference' => false,
                'mapped' => false
            ])  
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
            'allow_file_upload' => true
        ]);
    }
}
