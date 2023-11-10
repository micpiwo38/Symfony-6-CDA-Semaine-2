<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SimpleUploadService{

    //Acces à services.yaml via un constructeur
    public function __construct(private ParameterBagInterface $params){}

    //Injection de dépendance via l'autowiring UploadedFile = 
    //Recuperer le nom de la photo + extenssion (.webp) + deplacer les images
    public function uploadImage(UploadedFile $file){

        //Récuperer le nom originale de la photo
        $original_file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        //Le nom de base + random number + extension
        $new_file_name = $original_file_name.'-'.uniqid().'.'. $file->guessExtension();
        //Chemin de destination définis dans le fichier services.yaml parameters = images_directory: 
        //'%kernel.project_dir%/public/img/'
        $path_destination = $this->params->get('images_directory');
        //Deplacer et stocker l'image
        $file->move(
            $path_destination,
            $new_file_name
        );

        return $new_file_name;
    }

    //Supprimer une image + flush en base de donnée = cette methode est utilisée par le ProduitsController
    public function deleteImage(string $file){
        //Chemin des images
        $path = $this->params->get('images_directory');
        //Une image
        $image_object = $path . '/' . $file;
        //Status et etat de la requète apres passage de Javascript
        $success = false;

        if(file_exists($image_object)){
            //PHP unlink() - supprimer tous type de fichier
            unlink($image_object);
            //L'etat change et permet au controller d'effecteur un remove et flush en BDD
            $success = true;
            return $success;
        }
        return false;
    }

}