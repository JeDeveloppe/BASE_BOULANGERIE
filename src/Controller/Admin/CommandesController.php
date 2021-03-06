<?php

namespace App\Controller\Admin;

use App\Repository\DocumentRepository;
use App\Repository\HorairesEboutiqueRepository;
use App\Repository\ProduitRepository;
use DateTimeZone;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReservationDetailsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("admin/commandes/detail-preparations", name="admin_commandes_details_preparations")
     */
    public function commandesDetailsPreparations(
        ReservationRepository $reservationRepository,
        ProduitRepository $produitRepository,
        HorairesEboutiqueRepository $horairesEboutiqueRepository): Response
    {

        $date = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));

        //on recupere les parametres de la date
        $d = $date->format('d');
        $m = $date->format('m');
        $Y = $date->format('Y');

        //on recupere le "nom du jour"
        $day = $date->format('l');
        //transforme anglais -> francais
        switch($day){
            case 'Monday':
                $jour = 'LUNDI';
                break;
            case 'Tuesday':
                $jour = 'MARDI';
                break;
            case 'Wednesday':
                $jour = 'MERCREDI';
                break;
            case 'Thursday':
                $jour = 'JEUDI';
                break;
            case 'Friday':
                $jour = 'VENDREDI';
                break;
            case 'Saturday':
                $jour = 'SAMEDI';
                break;
            case 'Sunday':
                $jour = 'DIMANCHE';
                break;
        }

        //on recupere les horaires du jour dans la base
        $horaireEboutique = $horairesEboutiqueRepository->findBy(['day' => $jour]);

        $start = new DateTimeImmutable( $Y.'-'.$m.'-'.$d.' '.$horaireEboutique[0]->getOuvertureMatin());
        $end = new DateTimeImmutable( $Y.'-'.$m.'-'.$d.' '.$horaireEboutique[0]->getFermetureSoir());
        
        //on cherche les reservations pour la journ??e
        $tableauDesReservations = $reservationRepository->findReservations($start,$end);

        $details = [];

        foreach($tableauDesReservations as $reservation){
            
            $produits = $reservation->getReservationDetails();
  
                foreach($produits as $produit){
 
                    if(isset($details[$produit->getProduit()->getId()])){
                        $details[$produit->getProduit()->getId()] += $produit->getQuantity();
                    }else{
                        $details[$produit->getProduit()->getId()] = $produit->getQuantity();
                    }
                }
        }

        $listes = [];

        foreach($details as $id => $quantity){
            $listes[] = [
                'produit'  => $produitRepository->find($id),
                'quantity' => $quantity
            ];
        }

        return $this->render('admin/commandes/preparations.html.twig', [
            'listes' => $listes,
            'date'   => $date
        ]);
    }

    /**
     * @Route("/admin/commandes/a-emportees/", name="admin_commandes_du_jour")
     */
    public function listeDesCommandesClientDuJour(
        ReservationRepository $reservationRepository,
        ReservationDetailsRepository $reservationDetailsRepository,
        HorairesEboutiqueRepository $horairesEboutiqueRepository): Response
    {

        $dateMinuit = new DateTimeImmutable('today midnight', new DateTimeZone('Europe/Paris'));
        $date = new DateTimeImmutable('now', new DateTimeZone('Europe/Paris'));

        //on recupere les parametres de la date
        $d = $date->format('d');
        $m = $date->format('m');
        $Y = $date->format('Y');

        //on recupere le "nom du jour"
        $day = $date->format('l');
        //transforme anglais -> francais
        switch($day){
            case 'Monday':
                $jour = 'LUNDI';
                break;
            case 'Tuesday':
                $jour = 'MARDI';
                break;
            case 'Wednesday':
                $jour = 'MERCREDI';
                break;
            case 'Thursday':
                $jour = 'JEUDI';
                break;
            case 'Friday':
                $jour = 'VENDREDI';
                break;
            case 'Saturday':
                $jour = 'SAMEDI';
                break;
            case 'Sunday':
                $jour = 'DIMANCHE';
                break;
        }

        //on recupere les horaires du jour dans la base
        $horaireEboutique = $horairesEboutiqueRepository->findBy(['day' => $jour]);

        $start = new DateTimeImmutable( $Y.'-'.$m.'-'.$d.' '.$horaireEboutique[0]->getOuvertureMatin());
        $end = new DateTimeImmutable( $Y.'-'.$m.'-'.$d.' '.$horaireEboutique[0]->getFermetureSoir());
        
        //on cherche les vieilles reservations non emportees
        $oldReservations = $reservationRepository->findOldReservationsNonEmportees($dateMinuit);
        //on cherche les reservations pour la journ??e
        $reservations = $reservationRepository->findReservations($start,$end);

        $detailsOldReservations = [];
        $detailsReservations = [];

        foreach($oldReservations as $oldReservation){
            $detailsOldReservations[] = $reservationDetailsRepository->findBy(['reservation' => $oldReservation]);
        }
        foreach($reservations as $reservation){
            $detailsReservations[] = $reservationDetailsRepository->findBy(['reservation' => $reservation]);
        }

        return $this->render('admin/commandes/commandes_du_jour.html.twig', [
            'oldReservations'        =>  $oldReservations,
            'reservations'           => $reservations,
            'oldDetailsReservations' => $detailsOldReservations,
            'detailsReservations'    => $detailsReservations
        ]);
    }

    /**
     * @Route("/admin/commandes/a-emportees/statut-paiement/{id}/{token}", name="admin_changement_statut_paiement")
     */
    public function changementStatutReservation(
        ReservationRepository $reservationRepository,
        EntityManagerInterface $em,
        $id,
        $token
        )
    {

        $reservation = $reservationRepository->findOneBy(['id' => $id, 'token' => $token]);

        if(empty($reservation)){

            $this->addFlash('danger', 'R??servation non trouv??e!');

        }else{
            if($reservation->getStatutReservation() == "EMPORTEE"){
                $reservation->setStatutReservation('PAS_EMPORTEE');
            }else{
                $reservation->setStatutReservation('EMPORTEE');
            }

            $this->addFlash('success', 'R??servation mise ?? jour!');
            //on met a jour le statut
     
            $em->persist($reservation);
            $em->flush();
            
        }

        return $this->redirectToRoute('admin_commandes_du_jour', ['_fragment' => $reservation->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
    * @Route("/admin/commandes/recherche", name="admin_commandes_recherche")
    */
    public function commandeSearch(Request $request,DocumentRepository $documentRepository, UserRepository $userRepository): Response
    {
        $resultats = [];
        $numCommande = $request->request->get('numCommande');
        $nomClient = $request->request->get('nomClient');
        $submited = $request->request->get('submited');

        //si on vient de soumettre le formulaire
        if($submited){
            if($numCommande && !$nomClient){
                $resultats['commande'] = true;
                $resultats['resultats'] = $documentRepository->rechercheDocumentParNumeroCommande($numCommande);
            }else if(!$numCommande && $nomClient){
                $resultats['client'] = true;
                $resultats['resultats'] = $userRepository->rechercheDocumentParNomClient($nomClient);
            }else{
                dd("STOP");
            }
        }
 

        return $this->render('admin/commandes/recherche.html.twig', [
            'resultats' => $resultats
        ]);
    }
}
