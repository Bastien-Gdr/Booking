<?php

namespace App\Controller;



use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{

    #[Route('/ads/{slug}/book', name: 'booking_create')]
    /**
     * Permet d'afficher le formulaire de réservation
     *
     * @IsGranted("ROLE_USER")
     * @param Ad $ad
     * @return Response
     */
    public function book(Ad $ad,Request $request,EntityManagerInterface $manager): Response
    {

        $booking = new Booking();
        $form = $this->createForm(BookingType::class,$booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){


            $user = $this->getUser(); // getUser donne l'utilisateur connecté
            $booking->setBooker($user)
                    ->setAd($ad);

            // Si les dates ne sont pas disponibles
            if(!$booking->isBookabledays()){
                $this->addFlash("warning","Ces dates ne sont pas disponibles, veuillez les modifier");
            }
            else{
                $manager->persist($booking);
                $manager->flush();
    
                return $this->redirectToRoute("booking_show",['id'=>$booking->getId(),'alert'=>true]);
            }


        }

        return $this->render('booking/book.html.twig', [
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);
    }


    #[Route('/booking/{id}',name:'booking_show')]
    /**
     * Affiche une réservation
     *
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking){

        return $this->render("booking/show.html.twig",['booking'=>$booking]);
    }
}
