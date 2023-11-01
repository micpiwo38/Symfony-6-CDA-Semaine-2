<?php

namespace App\Repository;

use App\Entity\Produits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produits>
 *
 * @method Produits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produits[]    findAll()
 * @method Produits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produits::class);
    }

    ///////////////////////////////DQL SIMPLE/////////
    public function getAllProductsByIdDescDQL(){
        //Appel de la methode getEntityManger de EntityRepository
        $produits = $this->getEntityManager()
            //Appel de la methode createQuery + ecriture de la requete DQL sous forme de string
            //p est un alias de l'entité Produits appelée par son namespace
            ->createQuery("SELECT p FROM App\Entity\Produits p ORDER BY p.id DESC")
            //getResult() retourne un tableau de Produits
            ->getResult();
            //On retorune un ensemble de resultat de la requète
            return $produits;
    }
    ////////////////////DQL avec des paramètres/////////////////
    public function getProductByPriceScale($min, $max){
        $produits = $this->getEntityManager()
            ->createQuery("
                SELECT p FROM App\Entity\Produits p
                WHERE p.price >= :min AND p.price <= :max
                ORDER BY p.price ASC
            ")
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->getResult();

            return $produits;
    }

    /////////////////DQL avec une jointure/////////////////
    public function getProductByCategory(){
        $produit_cat = $this->getEntityManager()
            ->createQuery("SELECT DISTINCT p.name FROM App\Entity\Categories p JOIN p.produits c")
            ->getResult();
            //dd($produit_cat);
            return $produit_cat;
    }



    //////////////////////////////QUERY BUILDER //////////////////////////
    public function getLastProduct(){
        //createQueryBuilder récupère l'alias de l'entité Produits
        $dernier_produit = $this->createQueryBuilder('p')
        //Tri par ID decroissant
            ->orderBy('p.id', 'DESC')
            //On souhaite un seul résultat
            ->setMaxResults(1)
            //Execute la requète
            ->getQuery()
            //Retourne un seul resultat
            ->getOneOrNullResult();
            //dd($dernier_produit);
        //Retourne le resultat de la requète
        return $dernier_produit;
    }

    public function getByCategorie($cat){
        return $this->createQueryBuilder('p')
            ->where('p.categorie = :cat')
            ->setParameter('cat', $cat)
            ->getQuery()
            ->getResult();
    }

    //Afficher tous les produits en fonction du nom du distributeur et id de la catégorie
    public function getProductByDistributeur($categorie, $distributeur){
        $produits_dist =  $this->createQueryBuilder('p')
            ->addSelect('c')
            ->addSelect('d')//Ajoute la table Distributeur
            ->join('p.categorie', 'c')
            ->join('p.distributeur', 'd')
            ->where('c.id = :cat')
            ->andWhere('d.name = :dist')
            ->setParameter('cat', $categorie)
            ->setParameter('dist', $distributeur)
            ->getQuery()
            ->getResult();
            //dd($produits_dist);
            return $produits_dist;
    }

}
