<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReservationDetailsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandesController extends AbstractController
{
    /**
     * @Route("admin/commandes", name="admin_commandes")
     */
    public function index(): Response
    {
        return $this->render('commandes/index.html.twig', [
            'controller_name' => 'CommandesController',
        ]);
    }


    /**
     * @Route("/admin/commandes/du-jour/", name="admin_commandes_du_jour")
     */
    public function listeDesCommandesClientDuJour(ReservationRepository $reservationRepository, ReservationDetailsRepository $reservationDetailsRepository): Response
    {

        $reservations = $reservationRepository->findBy(['statutPaiement' => 'FACTURE_OK'], ['createdAt' => 'ASC']);

        $detailsReservations = [];

        foreach($reservations as $reservation){
            $detailsReservations[] = $reservationDetailsRepository->findBy(['reservation' => $reservation]);
        }

        return $this->render('admin/commandes/commandes_du_jour.html.twig', [
            'reservations'        => $reservations,
            'detailsReservations' => $detailsReservations
        ]);
    }

    /**
     * @Route("/admin/commandes/du-jour/statut-paiement/{id}/{token}", name="admin_changement_statut_paiement")
     */
    public function changementStatutReservation(
        ReservationRepository $reservationRepository,
        ReservationDetailsRepository $reservationDetailsRepository,
        EntityManagerInterface $em,
        $id,
        $token
        )
    {

        $reservation = $reservationRepository->findOneBy(['id' => $id, 'token' => $token]);

        if(empty($reservation)){

            $this->addFlash('danger', 'Réservation non trouvée!');

        }else{
            if($reservation->getStatutReservation() == "EMPORTEE"){
                $reservation->setStatutReservation('PAS_EMPORTEE');
            }else{
                $reservation->setStatutReservation('EMPORTEE');
            }

            $this->addFlash('success', 'Réservation mise à jour!');
            //on met a jour le statut
     
            $em->persist($reservation);
            $em->flush();
            
        }

        return $this->redirectToRoute('admin_commandes_du_jour', ['_fragment' => $reservation->getId()], Response::HTTP_SEE_OTHER);
    }
}
