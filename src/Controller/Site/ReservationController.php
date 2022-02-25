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
use App\Service\ReservationService;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ReservationController extends AbstractController
{
/**
      * @Route("/nouvelle-reservation/{horaire}/", name="reservation_add")
     */
    public function reservationAdd($horaire, ReservationService $reservationService)
    {

        $response = $reservationService->addReservation($horaire);

        if($response['response'] == true){

            return $this->redirectToRoute($response['route'], ['token' => $response['token']]);

        }else{

            $this->addFlash($response['label'], $response['message']);
            return $this->redirectToRoute($response['route']);

        }
    }

    /**
     * @Route("/supprimer-reservation/{token}/", name="reservation_remove")
     */
    public function reservationRemove($token, ReservationService $reservationService)
    {

        $response = $reservationService->removeReservation($token);

        $this->addFlash($response['label'], $response['message']);
        return $this->redirectToRoute($response['route']);

    }

}
