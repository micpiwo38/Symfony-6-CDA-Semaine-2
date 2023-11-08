<?php

namespace App\Controller;

use App\Entity\Photos;
use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Events\EditerProduitsEvent;
use App\Repository\PhotosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ProduitsRepository;
use App\Services\MessageService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProduitsController extends AbstractController{

   
    #[Route('/produits/message-service', name:'app_produits_message_service')]
    public function messageServiceProduit(){
        $majuscule = "tous mettre en majuscule";
       
        return $this->render('services/messages.html.twig',[
            'majuscule' => $majuscule
        ]);
    }

    #[Route('/produits', name:'app_produits')]
    /**
     * Afficher tous les produits via findAll() de ProduitsRepository et l'injection de dependance
     *
     * @param ProduitsRepository $produitsRepository
     * @return Response
     */
    public function afficherProduits(ProduitsRepository $produitsRepository) : Response {
        $min = 200;
        $max = 500;
        $cat = 55;
        $distributeur = 'quia';
        return $this->render('produits/afficher_produits.html.twig',[
            'produits' => $produitsRepository->findAll(),
            //Affiche les produits de la catégorie 35 trié par prix croissant
            //'produits' => $produitsRepository->findBy(['categorie' => 35], ['price' => 'ASC']),
            //'produits' =>$produitsRepository->getAllProductsByIdDescDQL()
            //'produits' => $produitsRepository->getProductByPriceScale($min, $max)
            //'produits' => $produitsRepository->getProductByCategory()
            //'produits' => $produitsRepository->getLastProduct()
            //'produits' => $produitsRepository->getByCategorie($cat),
            //'produits' => $produitsRepository->getProductByDistributeur($cat, $distributeur)
        ]);
    }

    #[Route('/details-produit/{id}', name:'app_details_produit')]
    /**
     * Afficher un produit par son id
     *
     * @param ProduitsRepository $produitsRepository
     * @param integer $id
     * @return Response
     */
    public function detailsProduit(ProduitsRepository $produitsRepository, int $id) : Response {
        return $this->render('produits/details_produit.html.twig',[
            //'produit' => $produitsRepository->find($id),
            'produit' => $produitsRepository->findOneBy(['id' => $id], ['price' => 'ASC'])
        ]);
    }

    #[Route("/ajouter-produits", name:"app_ajouter_produit")]
    public function ajouterProduits(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ) : Response {

        $product = new Produits();

        $form = $this->createForm(ProduitsType::class, $product);

        //Recup les attribut name via $_POST['name']
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $datas = $request->files->all();
            //Objet produits
            //dd($datas);
            $images = $datas['produits']['photos'];
            //Tableau d'image
            //dd($images);
            //Boucle de parcours du tableau d'image
            foreach($images as $image){
                //dd($image);
                //Nom des images
                $image_name = $image['name'];
                $newPhoto = new Photos();
                //$test = $image_name[0];
                //dd($test);
                //dd($image_name);
                $original_name = pathinfo($image_name->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($original_name);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$image_name->guessExtension();

                $image_name->move(
                    $this->getParameter('images_directory'),
                    $newFileName
                );

                //dd($image_name);
                //dd($fileSystem);

                $newPhoto->setName($newFileName);
                //$em->persist($newPhoto);
                $product->addPhoto($newPhoto);
                $separator = '-';
                //Le slug n'aparaot pas dans le formulaire
                //Supprimer les espaces + recuperer la valeur du champ Name
                $slug = trim($slugger->slug($form->get('name')->getData(), $separator)->lower());
                //Muter la valeur de $slug
                $product->setSlug($slug);
                $em->persist($newPhoto);
                //execute la requète SQL
                $em->flush();
            }

            $em->persist($product);
            //execute la requète SQL
            $em->flush();
            //Si ca marche
            $this->addFlash('success', 'Votre produit a bien été ajouté !');
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
        SluggerInterface $sluggerInterface,
        EventDispatcherInterface $dispatcher,
        PhotosRepository $photosRepository,
        SluggerInterface  $slugger
        ) : Response {
        
        $form = $this->createForm(ProduitsType::class, $produits);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){


            if($form->isSubmitted() && $form->isValid()){
                $datas = $request->files->all();
                //Objet produits
                //dd($datas);
                $images = $datas['produits']['photos'];
                //Tableau d'image
                //dd($images);
                //Boucle de parcours du tableau d'image
                foreach($images as $image){
                    //dd($image);
                    //Nom des images
                    $image_name = $image['name'];
                    $newPhoto = new Photos();
                    //$test = $image_name[0];
                    //dd($test);
                    //dd($image_name);
                    $original_name = pathinfo($image_name->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFileName = $slugger->slug($original_name);
                    $newFileName = $safeFileName.'-'.uniqid().'.'.$image_name->guessExtension();

                    $image_name->move(
                        $this->getParameter('images_directory'),
                        $newFileName
                    );

                    //dd($image_name);
                    //dd($fileSystem);

                    $newPhoto->setName($newFileName);
                    //$em->persist($newPhoto);
                    $produits->addPhoto($newPhoto);
                    $separator = '-';
                    //Le slug n'aparaot pas dans le formulaire
                    //Supprimer les espaces + recuperer la valeur du champ Name
                    $slug = trim($slugger->slug($form->get('name')->getData(), $separator)->lower());
                    //Muter la valeur de $slug
                    $produits->setSlug($slug);
                    $em->persist($newPhoto);
                    //execute la requète SQL
                    $em->flush();
                }

                $em->persist($produits);
                //execute la requète SQL
                $em->flush();
                //Si ca marche
                $this->addFlash('success', 'Votre produit a bien été ajouté !');
                return $this->redirectToRoute('app_produits');
            }


            //Utilisation de l'EventDispatcher
            if($produits){
                //Instance de la classe EditerProduitsEvent
                $produits_event = new EditerProduitsEvent($produits);
                //Le dispatcher distribue l'evenement
                //La methode dispatche() prend en paramètre l'instance de l'evenement et le nom de l'evenement de la classe 
                $dispatcher->dispatch($produits_event, EditerProduitsEvent::EDITER_PRODUIT);
            }
           

            $this->addFlash('success', 'Votre produit à bien été mis à jour !');
            return $this->redirectToRoute('app_produits');
        }

        return $this->render('produits/editer_produits.html.twig',[
            'produits_form' => $form->createView(),
            'images' => $photosRepository->findAll(),
            'produits' => $produits
        ]);
    }

    #[Route('/supprimer-image/{id}', name:'app_supprimer_image')]
    public  function supprimerImage(
        Produits $produits,
        Photos $photos,
        PhotosRepository $photosRepository,
        $id,
        Request $request,
        EntityManagerInterface  $em): Response
    {
        $photo = $photosRepository->find($id);
        $delete = false;
        if($photo){
            $em->remove($photo);
            $em->flush();
            $delete = true;

            $this->addFlash('success', 'Le photo a bien été supprimer');
            return $this->render('produits/editer_produits.html.twig');
        }
    }

}