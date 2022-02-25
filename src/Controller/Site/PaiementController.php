<?php

namespace App\Controller\Site;

use App\Repository\ReservationRepository;
use App\Service\DocumentService;
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
    public function checkout(Request $request, SessionInterface $session, Security $security, $token)
    {
        // This is your test secret API key.
        Stripe::setApiKey($_ENV['STRIPE_KEY']);
        
        $user = $security->getUser();
        $totaux = $session->get('totaux');

        $checkout_session = Session::create([
            'line_items' => [[
              'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                  'name' => 'T-shirt',
                ],
                'unit_amount' => $totaux['totalTTC'],
              ],
              'quantity' => 1,
            ]],
            'customer_email' => $user->getEmail(),
            'mode' => 'payment',
            'success_url' => $this->generateUrl('paiement_success' , ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('paiement_cancel' , [], UrlGeneratorInterface::ABSOLUTE_URL),
          ]);
        
        //return $response->withHeader('Location', $session->url)->withStatus(303);
        return $this->redirect($checkout_session->url,303);
    }

    /**
     * @Route("/paiement/success/{token}", name="paiement_success")
     */
    public function paiementSucess(
        DocumentService $documentService,
        $token,
        ReservationRepository $reservationRepository
        ): Response
    {
        //ici on verifie qu'il y a un token et qu'il est dans la base non paye
        //si ok on met a jour la bdd => reservation a 1
        //on cree le document avec incrementation
   
        if(empty($token)){
            dd('STOP A CREER');
        }else{
            $reservation = $reservationRepository->findOneBy(['token' => $token, 'statut' => 0]);

            $newDocument = $documentService->creationNouveauDocument('FAC',$reservation);

            dd($newDocument);
            //reste à mettre à jour la reservation avec le statut payer
            
            return $this->render('site/paiement/index.html.twig', [
                'controller_name' => 'PaiementController',
            ]);

        }
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
