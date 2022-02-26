<?php

namespace App\Controller\Site;

use App\Repository\ReservationRepository;
use App\Service\DocumentService;
use App\Service\ReservationService;
use App\Service\StripeService;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class PaiementController extends AbstractController
{
    /**
     * @Route("/paiement", name="paiement")
     */
    public function index(): Response
    {
        return $this->render('paiement/index.html.twig', [
            'controller_name' => 'PaiementController',
        ]);
    }

    /**
     * @Route("/paiement/checkout/{token}", name="paiement_checkout")
     */
    public function checkout(StripeService $stripeService, $token)
    {
        $response = $stripeService->checkout($token);

        return $this->redirect($response['route'],$response['code']);
    }

    /**
     * @Route("/paiement/success/{token}", name="paiement_success")
     */
    public function paiementSucess(
        DocumentService $documentService,
        ReservationService $reservationService,
        $token,
        ReservationRepository $reservationRepository
        ): Response
    {
        //ici on verifie qu'il y a un token et qu'il est dans la base non paye
        //si ok on met a jour la bdd => reservation a 1
        //on cree le document avec incrementation
        $response = [];

        if(empty($token)){
            $response['titre'] = 'Erreur';
            $response['message'] = '<p>Il manque un paramètre!</p>';
        }else{
            $reservation = $reservationRepository->findOneBy(['token' => $token, 'statut' => 'EN_ATTENTE_DE_PAIEMENT']);

            //reservation non trouvée (inexistante ou deja payée)
            if(empty($reservation)){
                $response['titre'] = 'Erreur';
                $response['message'] = 'Réservation inexistante ou déja payée!';

            }else{
                //tout est bon on cré la facture
                $newDocument = $documentService->creationNouveauDocument('FAC',$reservation);

                $reservationService->updateReservationFacturationOk($reservation);

            }
        }

        return $this->render('site/paiement/success.html.twig', [
            'response' => $response,
        ]);
    }

    /**
     * @Route("/paiement/cancel", name="paiement_cancel")
     */
    public function paiementCancel(): Response
    {
        return $this->render('site/paiement/index.html.twig', [
            'controller_name' => 'PaiementController',
        ]);
    }
}
