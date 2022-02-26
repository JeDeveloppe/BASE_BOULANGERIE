<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class StripeService
{
    public function __construct(Security $security, RouterInterface $route, SessionInterface $session)
    {
        $this->security = $security;
        $this->route = $route;
        $this->session = $session;
    }

    public function checkout($token){
          // This is your test secret API key.
          Stripe::setApiKey($_ENV['STRIPE_KEY']);
        
          $user = $this->security->getUser();
          $totaux = $this->session->get('totaux');
  
          $response = [];

          $checkout_session = Session::create([
              'line_items' => [[
                'price_data' => [
                  'currency' => 'eur',
                  'product_data' => [
                    'name' => $_ENV['STRIPE_NAME_BOUTIQUE_IN_CHECKOUTPAGE'],
                  ],
                  'unit_amount' => $totaux['totalTTC'],
                ],
                'quantity' => 1,
              ]],
              'customer_email' => $user->getEmail(),
              'mode' => 'payment',
              'success_url' => $this->route->generate('paiement_success' , ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
              'cancel_url' => $this->route->generate('paiement_cancel' , [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
          
            $response['route'] = $checkout_session->url;
            $response['code'] = 303;
            //return $response->withHeader('Location', $session->url)->withStatus(303);
          return $response;
    }
}