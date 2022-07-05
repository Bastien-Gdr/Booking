<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{

    // Permet d'afficher une liste d'annonce
    #[Route('/ads', name: 'ads_list')]

    public function index(AdRepository $repo): Response
    {

        // via $repo, on va aller chercher toutes les réponses via la méthode findAll()

        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Nos annonces',
            'ads' => $ads
        ]);
    }

    
    // Permet de créer une annonce

    #[Route("ads/new", name: "ads_create")]

    /**     
     * @return responce
     */
    public function create(Request $request, EntityManagerInterface $manager){

        // fabricant de formulaire : FORMBUILDER
        $ad = new Ad();
        $images = new Image();


        
        // On lance la fabrication et la ocnfiguration de notre formulaire
        $form = $this->createForm(AnnonceType::class,$ad);

        // Récupération des données du formulaire
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            // Si le formulaire est soumis ET si le formulaire est valide, on demande à doctrine de sauvegarder ces donnnées dans la bdd dans l'objet Manager

            // Pour chaque image suplémentaire ajoutée
            foreach($ad->getImages() as $image){
                // On relie l'image à l'annonce et on modifie l'annonce
                $image->setAd($ad);

                // On sauvegarde les images
                $manager->persist($image);
            }

            $manager->persist($ad);

            $manager->flush();

            $this->addFlash('success',"L'annonce <strong>{$ad->getTitle()}</strong> a été créée avec succès");

            return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
        }

        return $this->render('ad/new.html.twig',['form' => $form->createView()]);
    }

    

    // Permet d'afficher une seule annonce

    #[Route("ads/{slug}", name: "ads_single")]
    /**
     * @return Response
     */

    public function show($slug,Ad $ad){

        // Je récupère l'annonce qui correspond au slug (allias)
        // X = 1 champ de la table, à préciser à la place de X
        // findByX = renvoi un tableau d'annonces (plusieurs éléments)
        // findOneByX renvoi un seul élément

        // $ad = $repo -> findOneBySlug($slug);

        return $this->render('ad/show.html.twig',['ad'=>$ad]);

    }


    #[Route("/ads/{slug}/edit", name:"ads_edit")]
    /**
     * Permet d'éditer un article
     *
     * @return Response
     */
    public function edit(Ad $ad,Request $request,EntityManagerInterface $manager){

        $form = $this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            foreach($ad->getImages() as $image){

                $image->setAd($ad);

                $manager->persist($image);

            }

            $manager->persist($ad);
            $manager->flush();
            
            $this->addFlash("succes","Les modifications ont été apportées à votre article");

            return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
        }

        return $this->render('ad/edit.html.twig',['form'=>$form->createView(),'ad'=>$ad]);
    }




}
