<?php

namespace App\Controller\Site;

use App\Entity\Reservation;
use App\Repository\InfosLegalesRepository;
use App\Repository\ProduitRepository;
use App\Repository\ReservationRepository;
use App\Service\ReservationService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(
        SessionInterface $session,
        ProduitRepository $produitRepository,
        InfosLegalesRepository $infosLegalesRepository,
        ReservationService $reservationService): Response
    {

        $panier = $session->get('panier', []);

        //si panier vide
        if(count($panier) < 1){
             //message flash
            $this->addFlash('warning', 'Votre panier est vide!');
            return $this->redirectToRoute('eboutique_accueil', [], Response::HTTP_SEE_OTHER);
        }

        $panierWithData = [];

  
        foreach($panier as $id => $quantity){
            $panierWithData[] = [
                'produit'  => $produitRepository->find($id),
                'quantity' => $quantity
            ];
        }

        //on fait le total du panier en HT
        $totalHT = 0;

        foreach($panierWithData as $item){
            $totalItem = $item['produit']->getPrix() * $item['quantity'];
            $totalHT += $totalItem;
        }

        $infoLegales = $infosLegalesRepository->findAll();
        $multiplicateurTva = $infoLegales[0]->getTva();
        $tva = ($multiplicateurTva -1) * 100;

        $totaux = [];
        $totaux['tauxTVA'] = $tva;
        $totaux['totalHT'] = $totalHT;

        $totalTTC = $totalHT * $multiplicateurTva;
        $totaux['totalTTC'] = $totalTTC;

        $totalTVA = $totalTTC - $totalHT;
        $totaux['totalTVA'] = $totalTVA;
        $session->set('totaux', $totaux);

        //vérification si jour fermé (fermé ou férié)
        //création des tranches horaires et verification si pas deja de reservation
        $taillePeriode = $_ENV['TAILLE_DE_RESERVATION_DANS_PERIODE'];
        $response = $reservationService->calculCreneauxDisponiblePourReservation($taillePeriode);

    
        return $this->render('site/panier/index.html.twig', [
            'items'         => $panierWithData,
            'totaux'        => $totaux,
            'response'      => $response,
            'infosLegales'  => $infoLegales
        ]);
    }

    /**
     * @Route("/panier/add/{id}/", name="panier_add")
     */
    public function add($id, Request $request): Response
    {

        $qte = $request->request->get('qte');

        if(!$qte){
            return $this->redirectToRoute('eboutique_accueil', [], Response::HTTP_SEE_OTHER);
        }

        $session = $request->getSession();

        $panier = $session->get('panier',[]);

        if(!empty($panier[$id])){
            $panier[$id] += $qte;
        }else{
            $panier[$id] = $qte;
        }

        $session->set('panier', $panier);

        //message flash
        $this->addFlash('success', 'Produit mis dans le panier!');
        //on retourne à la page précèdante
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

     /**
     * @Route("/panier/remove/{id}/", name="panier_remove")
     */
    public function remove($id, Request $request): Response
    {
        $session = $request->getSession();
        $panier = $session->get('panier',[]);

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

         //message flash
         $this->addFlash('success', 'Ligne supprimée du panier!');
        return $this->redirectToRoute('panier');
    }
}
