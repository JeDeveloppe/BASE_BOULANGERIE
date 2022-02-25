<?php

namespace App\Controller\Site;

use DateTime;
use DateInterval;
use DateTimeZone;
use DateTimeImmutable;
use App\Entity\Reservation;
use App\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use App\Service\DocumentService;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{
/**
      * @Route("/nouvelle-reservation/{horaire}/", name="reservation_add")
     */
    public function reservationAdd(
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        Security $security,
        ReservationRepository $reservationRepository,
        DocumentService $documentService,
        $horaire
        ): Response
    {
        $panier = $session->get('panier', []);
        $user = $security->getUser();
        
        //si panier vide ou utilisateur non loguer ou crenau inexacte
        if(count($panier) < 1 || empty($user)){
             //message flash
             $this->addFlash('warning', 'Vous n\'êtes pas identifié(e)');
             return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        
        $reservation = new Reservation();

        //si bien identifie on enregistre et on redirige vers le paiement
        //décalage pour les réservations
        $decalageJour = $_ENV['NBRE_DE_JOUR_DECALAGE_RESERVATION'];
        $date = new DateTimeImmutable('now'.$horaire.':00', new DateTimeZone('Europe/Paris'));
        $date = $date->add(new DateInterval('P'.$decalageJour.'D'));

        $nbrReservationMaxParPeriode = $_ENV['NBRE_DE_RESERVATION_MAX_PAR_PERIODE'];

        //on verifié si le créneau est toujours disponible
        $nbreReservation = $reservationRepository->findBy(['createdAt' => $date]);
        if($nbrReservationMaxParPeriode - count($nbreReservation) > 0){

                $reservation->setUser($user)
                            ->setStatut(0) // non payé
                            ->setCreatedAt($date);

                $entityManager->persist($reservation);
                $entityManager->flush();

                //une fois créneau réservé, on recupere le dernier enregistrement est on crée la commande;
                //$newDocument = $documentService->creationNouveauDocument('DEV', $reservation);

               //a se stade on peut se diriger vers le paiement on a toutes les infos du document dans $newDocument

            
                //message flash
                $this->addFlash('success', 'Réservation faite !');
                return $this->redirectToRoute('panier');
        }else{

            //message flash
            $this->addFlash('warning', 'Dernier créneau réservé à l\'instant, désolé...');
            return $this->redirectToRoute('panier', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @Route("/supprimer-reservation/{id}/", name="reservation_remove")
     */
    public function reservationRemove(
        SessionInterface $session,
        EntityManagerInterface $em,
        Security $security,
        ReservationRepository $reservationRepository,
        $id
        ): Response
    {
        $panier = $session->get('panier', []);
        $user = $security->getUser();

        //si panier vide ou utilisateur non loguer
        if(count($panier) < 1 || empty($user)){
             //message flash
            $this->addFlash('warning', 'Vous n\'êtes pas identifié(e)');
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }else{

            //on cherche la reservation pour la supprimer
            $reservation = $reservationRepository->findOneBy(['id' => $id, 'user' => $user]);

            if(!empty($reservation)){
                $em->remove($reservation);
                $em->flush();
    
                //message flash
                $this->addFlash('success', 'Réservation annulée');
                return $this->redirectToRoute('panier');
            }else{
                //message flash
                $this->addFlash('warning', 'Réservation non trouvée');
                return $this->redirectToRoute('panier');
            }
           
        }
    }
}
