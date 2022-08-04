<?php

namespace App\Controller;

use App\Service\Pagination;
use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    #[Route('/admin/bookings/{page<\d+>?1}', name: 'admin_bookings_list')]
    /**
     * Affiche la liste des réservations
     *
     * @param BookingRepository $repo
     * @return Response
     */
    public function index(BookingRepository $repo,Pagination $paginationService,$page): Response
    {
        $paginationService->setEntityClass(Booking::class)
                          ->setPage($page)
                          //->setRoute('admin_bookins_list')
                          ;

        return $this->render('admin/booking/index.html.twig', [
            'pagination'=>$paginationService
        ]);
    }

    #[Route('/admin/booking/{id}/edit',name:'admin_booking_edit')]
    /**
     * Edition d'une réservation
     *
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Responce
     */
    public function edit(Booking $booking,Request $request,EntityManagerInterface $manager){

        $form = $this->createForm(AdminBookingType::class,$booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           // $booking->setAmount($booking->getAd()->getPrice() * $booking->getDuration());

            $booking->setAmount(0); 

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash('success','La réservarion a bien été modifiée');
        }

        return $this->render('admin/booking/edit.html.twig',[
            'booking'=>$booking,
            'form'=>$form->createView()
        ]);
    }

    #[Route('admin/booking/{id}/delete',name:'admin_booking_delete')]
    /**
     * Suppression d'une réservation
     *
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $manager){

        $manager->remove($booking);
        $manager->flush();

        $this->addFlash('success',"Réservation n° {$booking->getId()} supprimée avec succés !");

        return $this->redirectToRoute('admin_bookings_list');

    }

}
