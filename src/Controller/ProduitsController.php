<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Events\AjouterProduitsEvent;
use App\Form\ProduitsType;
use App\Repository\ProduitsRepository;
use App\Service\ImageUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProduitsController extends AbstractController{


    public function __construct(
        private EventDispatcherInterface $dispatcher
    ){}

    #[Route('/produits', name:'app_produits')]
    public function afficherProduits(ProduitsRepository $produitsRepository) : Response {

        return $this->render('produits/afficher_produits.html.twig',[
            'produits' => $produitsRepository->findAll()
        ]);
    }


    
    /**
     * Methode ajouter un produit
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param ImageUploadService $imageUploadService
     * @return Response
     */
    #[Route('/ajouter-produit', name:'app_ajouter_produit')]
    public function ajouterProduit(
        Request $request, //Objet Request HttpFoundation
        EntityManagerInterface $em, //Gestionnaire d'entité
        SluggerInterface $sluggerInterface //Transforme des valeurs en string
        ) : Response {
        
        $produits = new Produits();//Instance de l'entité Produits
        //Creation du formulaire 2 paramètres : 
        //ProduitsType = formulaire de entité Produits + insatnce de l'entité Produits
        $form = $this->createForm(ProduitsType::class, $produits);
        //Analyse des champs du formulaire
        $form->handleRequest($request);
        //isSubmit() => tous les champs sont remplis
        //isSubmit() => Respect des règles de validation (Constraints, Voter, etc...)    
        if($form->isSubmitted() && $form->isValid()){
            //Le slug n'aparaot pas dans le formulaire
            //Supprimer les espaces + recuperer la valeur du champ Name
            $slug = trim($sluggerInterface->slug($form->get('name')->getData()));
            //Muter la valeur de $slug
            $produits->setSlug($slug);
            //Requète preparée => pas de query
            $em->persist($produits);
            //Execution de la requète
            $em->flush();
            //Notification si le produit est ajouté via les FlashBags
            $this->addFlash('success', 'Votre produit à bien été ajouté !');
            //Redirection
            return $this->redirectToRoute('app_produits');
        }
        //Appel de la vue qui affiche le formulaire
        return $this->render('produits/ajouter_produits.html.twig',[
            //Cle + valeur => generation du formulaire
            //La cle est appelée dans la vue Twig {{form(produits_form)}}
            'produits_form' => $form->createView()
        ]);
    }

    
    #[Route('/editer-produit/{name}', name:'app_editer_produit')]
   
    public function editerProduit(
        Request $request, 
        EntityManagerInterface $em,
        Produits $produits,
        SluggerInterface $sluggerInterface
        ) : Response {
        
        $form = $this->createForm(ProduitsType::class, $produits);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //Formater le nom du produit
            trim($sluggerInterface->slug($produits->getName())->lower());
          
            //Appel de l'attribut prePersist -> et evenement => getCreatedValue pour generer la date Immutable
            //dd($image);
            $em->persist($produits);
            $em->flush();

            //Appel de event dispatcher
           

            $this->addFlash('success', 'Votre produit à bien été ajouté !');
            return $this->redirectToRoute('app_produits');
        }

        return $this->render('produits/editer_produits.html.twig',[
            'produits_form' => $form->createView()
        ]);
    }

    

}