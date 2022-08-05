<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use App\Service\Pagination;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    #[Route('/admin/users/{page<\d+>?1}', name: 'admin_users_list')]
    public function index(UserRepository $repo,Pagination $paginationService,$page): Response
    {
        $paginationService->setEntityClass(User::class)
                          ->setLimit(7)
                          ->setPage($page);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $paginationService,
        ]);
    }


    #[Route('/admin/user/{id}/edit',name:'admin_users_edit')]
    /**
     * Editer un utilisateur par l'admin
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function edit(User $user,EntityManagerInterface $manager,Request $request){

        $form = $this->createForm(UserType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success","Le profil de l'utilisateur a bien été modifié !");
        }

        return $this->render("admin/user/edit.html.twig",[
            'user'=>$user,
            'form'=>$form->createView()
        ]);

    }

    #[Route('/admin/users/{id}/delete',name:"admin_users_delete")]
    /**
     * Supprime un utilisateur par l'admin
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(User $user,EntityManagerInterface $manager){

            $manager->remove($user);
            $manager->flush();
            $this->addFlash("success","L'utilisateur a bien été supprimée !");

        return $this->redirectToRoute('admin_users_list');
    }
}
