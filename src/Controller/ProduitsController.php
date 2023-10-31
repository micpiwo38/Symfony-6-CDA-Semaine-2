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

    #[Route('/', name:'app_produits')]
    public function afficherProduits(ProduitsRepository $produitsRepository) : Response {

        return $this->render('produits/afficher_produits.html.twig',[
            'produits' => $produitsRepository->findAll()
        ]);
    }


    #[Route('/ajouter-produit', name:'app_ajouter_produit')]
    /**
     * Methode ajouter un produit
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param ImageUploadService $imageUploadService
     * @return Response
     */
    public function ajouterProduit(
        Request $request, 
        EntityManagerInterface $em,
        ) : Response {
        
        $produits = new Produits();
        $form = $this->createForm(ProduitsType::class, $produits);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            
            //Appel de l'attribut prePersist -> et evenement => getCreatedValue pour generer la date Immutable
            //dd($image);
            $em->persist($produits);
            $em->flush();

            $this->addFlash('success', 'Votre produit à bien été ajouté !');
            return $this->redirectToRoute('app_produits');
        }

        return $this->render('produits/ajouter_produits.html.twig',[
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