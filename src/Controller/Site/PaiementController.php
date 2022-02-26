<?php

namespace App\Controller\Site;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Service\StripeService;
use App\Service\DocumentService;
use App\Service\ReservationService;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            $reservation = $reservationRepository->findOneBy(['token' => $token, 'statutPaiement' => 'EN_ATTENTE_DE_PAIEMENT']);

            //reservation non trouvée (inexistante ou deja payée)
            if(empty($reservation)){
                $response['titre'] = 'Erreur';
                $response['message'] = 'Réservation inexistante ou déja payée!';

            }else{
                //tout est bon on cré la facture
                $documentService->creationNouveauDocument('FAC',$reservation);
                //on met a jour la reservation
                $reservationService->updateReservationFacturationOk($reservation);

                $response['titre'] = 'Paiement accepté';
                $response['message'] = 'Merci pour votre commande!';
            }
        }

        return $this->render('site/paiement/success.html.twig', [
            'response' => $response,
        ]);
    }

    /**
     * @Route("/paiement/cancel/{token}", name="paiement_cancel")
     */
    public function paiementCancel(Security $security, $token, ReservationRepository $reservationRepository): Response
    {
        $user = $security->getUser();

        $reservation = $reservationRepository->findOneBy(['user' => $user, 'token' => $token, 'statutPaiement' => 'EN_ATTENTE_DE_PAIEMENT']);

        if(empty($reservation)){

            throw new NotFoundHttpException("Page not found");
            
        }else{

            return $this->render('site/paiement/cancel.html.twig', [
                'reservation' => $reservation,
            ]);
        }
    }
}
