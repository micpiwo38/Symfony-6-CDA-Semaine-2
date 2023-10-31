<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\Distributeurs;
use App\Entity\Produits;
use App\Entity\References;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //composer require --dev orm-fixtures

        //composer require fakerphp/faker
        //symfony console doctrine:fixtures:load --no-interaction
        //Les erreurs de Faker => Lorem.php => vendor/fzaninotto/faker/src/Faker/Provider/Lorem.php
        $faker = \Faker\Factory::create('fr_FR');
        //4 tableaux de données

        $produits = [];
        $categories = [];
        $distributeurs = [];
        $references = [];


        for($i = 0; $i < 30; $i++){
            $reference = new References();
            $reference->setName('REF_' . mt_rand(1,9999));
            $references[] = $reference;
            $manager->persist($reference);
        }

        for($i = 0; $i < 30; $i++){
            $categorie = new Categories();
            $categorie->setName($faker->word);
            $categories[] = $categorie;
            $manager->persist($categorie);
        }

        for($i = 0; $i < 30; $i++){
            $distributeur = new Distributeurs();
            $distributeur->setName($faker->word);
            $distributeurs[] = $distributeur;
            $manager->persist($distributeur);
        }

        for($i = 0; $i < 30; $i++){

            $produit = new Produits();
            $produit->setName($faker->word);
            $produit->setDescription($faker->text($maxNbChars = 200));
            $produit->setImage('https://picsum.photos/200');
            $produit->setPrice(mt_rand(1, 2000));
            $produit->setSlug($produit->getName());



            for($c = 0; $c < count($categories); $c++){
                $produit->setCategorie($faker->randomElement($categories));
            }

            for($d = 0; $d < count($distributeurs); $d++){
                $produit->addDistributeur($faker->randomElement($distributeurs));
            }

            for($r = 0; $r < count($references); $r++){
                $produit->setReference($references[$i]);
            }

            $produits[] = $produit;
            $manager->persist($produit);

        }

        $manager->flush();
    }
}
