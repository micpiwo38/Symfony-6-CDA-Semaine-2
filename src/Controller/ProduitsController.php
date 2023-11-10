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
use App\Services\SimpleUploadService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        SluggerInterface $slugger,
        SimpleUploadService $simpleUploadService
    ) : Response {

        $produits = new Produits();
        $form = $this->createForm(ProduitsType::class, $produits);
        //Recup les attribut name via $_POST['name']
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
                $photos = $request->files->all();
                //dd($photos);
                if($photos == null){
                    $this->addFlash('danger', 'Chaque produit doit contenir au moins une photo');
                    return $this->redirectToRoute('app_ajouter_produit');
                }else{
                    //dd('on continue !');
                    //Recuperer le tableau de photos
                    $images = $photos['produits']['photos'];
                    //dd($images);
                    foreach($images as $image){
                        //Instance de l'entité Photos
                        $new_photos = new Photos();
                        //Recuperer le nom de l'image
                        $image_name = $image['name'];
                        //Renomer et deplacer les photos a l'aide du service
                        $new_photo = $simpleUploadService->uploadImage($image_name);
                        //Ajouter les noms des photos a l'entité Photos
                        //A l'aide du setter
                        $new_photos->setName($new_photo);
                        //Utiliser la methode addPhoto de l'entité Produits
                        $produits->addPhoto($new_photos);

                        $separator = '-';
                        //Le slug n'aparait pas dans le formulaire
                        //Supprimer les espaces + recuperer la valeur du champ Name
                        $slug = trim($slugger->slug($form->get('name')->getData(), $separator)->lower());
                        //Muter la valeur de $slug
                        $produits->setSlug($slug);

                        //Persister l'entité Photos a l'aide de EntityManagerInterface
                        $em->persist($new_photos);
                        //Executer la requete INSERT INTO
                        $em->flush();

                    }
                }

            $em->persist($produits);
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
        SluggerInterface  $slugger,
        SimpleUploadService $simpleUploadService
        ) : Response {
        
        $form = $this->createForm(ProduitsType::class, $produits);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $photos = $request->files->all();
            //dd($photos);
            if($photos == null){
                $this->addFlash('danger', 'Chaque produit doit contenir au moins une photo');
                return $this->redirectToRoute('app_ajouter_produit');
            }else{
                //dd('on continue !');
                //Recuperer le tableau de photos
                $images = $photos['produits']['photos'];
                //dd($images);
                foreach($images as $image){
                    //Instance de l'entité Photos
                    $new_photos = new Photos();
                    //Recuperer le nom de l'image
                    $image_name = $image['name'];
                    //Renomer et deplacer les photos a l'aide du service
                    $new_photo = $simpleUploadService->uploadImage($image_name);
                    //Ajouter les noms des photos a l'entité Photos
                    //A l'aide du setter
                    $new_photos->setName($new_photo);
                    //Utiliser la methode addPhoto de l'entité Produits
                    $produits->addPhoto($new_photos);

                    $separator = '-';
                    //Le slug n'aparait pas dans le formulaire
                    //Supprimer les espaces + recuperer la valeur du champ Name
                    $slug = trim($slugger->slug($form->get('name')->getData(), $separator)->lower());
                    //Muter la valeur de $slug
                    $produits->setSlug($slug);

                    //Persister l'entité Photos a l'aide de EntityManagerInterface
                    $em->persist($new_photos);
                    //Executer la requete INSERT INTO
                    $em->flush();

                }
            }

            $em->persist($produits);
            //execute la requète SQL
            $em->flush();
            //Si ca marche
            $this->addFlash('success', 'Votre produit a bien été ajouté !');
            return $this->redirectToRoute('app_produits');
    
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
            'photos' => $photosRepository->findBy(['produits' => $produits]),
            'produit' => $produits
        ]);
    }

    #[Route('/supprimer-image-produits/{id}', name:'app_suppimer_image_produits', methods:['DELETE'])]
    public  function supprimerImage(
        Photos $photos,
        Request $request,
        EntityManagerInterface $em,
        SimpleUploadService $simpleUploadService):JsonResponse
    {
        //json_decode = Prend une chaîne codée JSON et la convertit en une valeur PHP.
       $data = json_decode($request->getContent(), true);
       //Recupere le data-token du bouton + id de l'image et check si ca matche avec le token decoder
       if($this->isCsrfTokenValid("delete" . $photos->getId(), $data['_token'])){
        //Récuperation du nom de l'image
        $photo_name = $photos->getName();
        //Appel de la methode du service = 
        //chemin de l'image + status (succes de la requete HTTP DELETE ou echec de la promesse) + supression du fichier
        if($simpleUploadService->deleteImage($photo_name)){
            //En cas de succès de la requète
            //On supprimer le nom et la jointure de l'image au produit
            $em->remove($photos);
            //Execute le delete en BDD
            $em->flush();
            $this->addFlash('success', 'La photo à bien été supprimer !');
            return new JsonResponse(['success' => "La photo a bien été supprimée !"],200);
        }
        $this->addFlash('danger', "La suppression de la photo a échoué !");
        return new JsonResponse(['success' => "Erreur de supression de la photo !"],200);
       }
       return new JsonResponse(['error' => 'Le jeton est invalide'], 400);
    }
}