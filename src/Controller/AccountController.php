<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AccountController extends AbstractController
{
    // Permet d'afficher une page connection
    #[Route('/login', name: 'account_login')]


    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();

        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig',[
                'hasError'=>$error!==null,
                'username'=>$username
        ]);
    }


    // Permet de se déco
    #[Route('/logout', name: 'account_logout')]
    public function logout(){
        // tout se passe via le security.yalm
    }

    // Afficher la page s'enregistrer
    #[Route("/register", name:"account_register")]
    /**
     * @return Response
     */

    public function register(Request $request,UserPasswordHasherInterface $encoder,EntityManagerInterface $manager){
        $user = new User();

        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //var_dump($user->getHash());exit;
            $hash = $encoder->hashPassword($user,$user->getHash());

            // On modifie le mdp avec les setter

            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success","Votre compte a bien été créé !");

            return $this->redirectToRoute("account_login");
        }

        return $this->render('account/register.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    //Modification du profil utilisateur
    #[Route('/account/profile',name:'account_profile')]
    /**
    *@IsGranted("ROLE_USER")
    *@return Response
    */
    public function profile(Request $request,EntityManagerInterface $manager){

        $user = $this->getUser();

        $form = $this->createForm(AccountType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success","Les informations ont bien été modifiées");
        }

        return $this->render('account/profile.html.twig',[
            'form'=>$form->createView()
        ]);
    }


    // Permet la modification du mot de passe
    #[Route('/account/update-password',name:'account_password')]
    //@IsGranted("ROLE_USER")
    //@return Response

    public function updatePassword(Request $request,UserPasswordHasherInterface $encoder,EntityManagerInterface $manager){

        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class,$passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Mot de passe actuel n'est pas correct
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash())){
                // Message d'erreur
               // $this->addFlash("warning","Votre ancien mot de passe est incorrect");
               $form->get('oldPassword')->addError(new FormError('Le mot de passe actuel est incorrect'));

            }else{

                // On récupère le nouveau mot de passe
                $newPassword = $passwordUpdate->getNewPassword();
    
                // On crypte le nouveau mot de passe
                $hash = $encoder->hashPassword($user,$newPassword);
    
                // On modifie le au mot de passe
                $user->setHash($hash);
    
                // On enregistre
                $manager->persist($user);
                $manager->flush();
    
                // On ajoute un message
                $this->addFlash('success','Votre nouveau mot de passe a bien été enregistré');
    
                // Redirige
                return $this->redirectToRoute('account_profile');

            }

        }

        return $this->render('account/password.html.twig',[
            'form'=>$form->createView()
        ]);
    }


    // Permet d'afficher la page 'mon compte'
    #[Route('/account',name:'account_home')]
    //@IsGranted("ROLE_USER")
    //@return Response
    public function myAccount(){

        return $this->render("user/index.html.twig", ['user'=>$this->getUser()]);
    }

}
