<?php

namespace App\Services;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImagesUploadService{

    public function __construct(private ParameterBagInterface $params){}

    public function ajouterPhotos(UploadedFile $photos, ?string $dossier = '', ?int $width = 250, ?int $height = 250){

        //Renomer les images = nom hacher + pas de doublons + chaine de caractères randomiser + extenssion .webp
        $fichier = md5(uniqid(rand(), true)) . '.webp';
        //Recuperer les infos de l'image
        $photo_infos = getimagesize($photos);

        if(!$photo_infos){
            throw  new Exception("Format d'image incorect !");
        }

        //Extension de l'image
        switch($photo_infos['mime']){
            case 'image/jpeg':
                $photo_source = imagecreatefromjpeg($photos);
                break;
            case 'image/png':
                $photo_source = imagecreatefrompng($photos);
                break;
            case 'image/webp':
                $photo_source = imagecreatefromwebp($photos);
                break;
            default:
            throw  new Exception("L'extension de l'image incorect!");
        }

        //Recadrer l'image
        //La largeur et la hauteur
        $photo_width = $photo_infos[0];
        $photo_height = $photo_infos[1];

        //Orientation de l'image = portrait ou paysage
        //Utilisation du symbole spaceship <=> = triple comparaison ternaire
        //3 valeurs >= -1 - egale = 0 ou <= 1
        switch($photo_width <=> $photo_height){
            //Mode portrait
            case -1:
                $square_size = $photo_width;
                $src_x = 0;
                $src_y = ($photo_width - $square_size) / 2;
                break;
            case 0:
                $square_size = $photo_width;
                $src_x = 0;
                $src_y = 0;
                break;
            case 1://Paysage
                $square_size = $photo_width;
                $src_x = ($photo_width - $square_size) / 2;
                $src_y = 0;
                break;
        }

        //Generer une nouvelle image + recadrage
        $resize_photo = imagecreatetruecolor($width, $height);
        imagecopyresampled($resize_photo, $photo_source,0,0, $src_x, $src_y, $width,$height,$square_size, $square_size);

        //Chemin de destination
        $path = $this->params->get('images_directory'). $dossier;

        //Creer un dossier si il est absent
        if(!file_exists($path . '/miniatures')){
            //Creation du dossier vec la commane make directory de php
            //nom du dossier + permission RWX = lecture + ecriture + executable + appel recursif
            //Si recursif = true ! Si true, alors tous les répertoires parents au directory spécifié seront également créés, avec les mêmes permissions. 
            mkdir($path . '/miniatures', 0777, true);
        }

        //Stock de l'image + modification de l'extension
        //imagewenp = Affiche une image WebP vers un navigateur ou un fichier
        //params = image + file + qualiter
        //image = Un objet GdImage, retournée par une des fonctions de création d'images, comme imagecreatetruecolor(). 
        //file = Le chemin ou un flux de ressource ouvert (qui sera automatiquement fermé après le retour de cette fonction) vers lequel le 
        //fichier sera sauvegardé. Si non-défini ou null, le flux brute de l'image sera affiché directement.
        //quality = quality plage de 0 (la pire qualité, plus petit fichier) à 100 (meilleure qualité, plus grand fichier).
        //return = Cette fonction retourne true en cas de succès ou false si une erreur survient.  
        imagewebp($resize_photo, $path .'/miniatures/'. $width . 'x'. $height. '-'. $fichier);
        //Deplacer image move() = move_uploaded_file en php classique =  move_uploaded_file(string $from, string $to): bool
        $photos->move($path .'/miniatures/', $fichier);

        return $fichier;

    }
}