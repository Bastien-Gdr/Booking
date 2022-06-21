<?php

// Pour créer une page il faut : une fonction public (class), une route (un chemin), une réponse

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController{

    /**
     * Création de notre première route
     * @Route("/", name="homepage")
     */

    public function home(){
        $noms = ['Durand'=>'visiteur','François'=>'admin','Dupont'=>'contributeur'];
        return $this->render('home.html.twig',['title'=>'Site d\'annonces !','access'=>'admin','tableau'=>$noms]);

    }

    /**
     * Montre la page qui salut l'utilisateur
     * 
     * @Route("/hello/{nom}",name="hello")
     * @Route("/profil",name="hello-base")
     * @Route("/profil/{nom}/access/{access}",name="hello-profil")
     * @return void
     */

    public function hello($nom="anonyme",$access="visiteur"){
        return $this->render('hello.html.twig',['title'=>'Page de profil','nom'=>$nom,'access'=>$access]);
    }

}