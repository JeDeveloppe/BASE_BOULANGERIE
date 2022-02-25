<?php

namespace App\Service;

use DateTime;
use DatePeriod;
use DateInterval;
use DateTimeZone;
use DateTimeImmutable;
use App\Entity\Reservation;
use App\Entity\HorairesEboutique;
use App\Entity\ReservationDetails;
use App\Repository\ReservationRepository;
use Symfony\Component\Security\Core\Security;
use App\Repository\HorairesEboutiqueRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ReservationService
{

    public function __construct(
        ReservationRepository $reservationRepository,
        Security $security,
        HorairesEboutiqueRepository $horairesEboutiqueRepository,
        SessionInterface $session,
        FlashBagInterface $flashBag,
        EntityManagerInterface $em,
        ProduitRepository $produitRepository
        )
    {
        $this->reservationRepository = $reservationRepository;
        $this->security = $security;
        $this->horairesEboutiqueRepository = $horairesEboutiqueRepository;
        $this->session = $session;
        $this->flashBag = $flashBag;
        $this->entityManager = $em;
        $this->produitRepository = $produitRepository;
    }

    public function addReservation($horaire)
    {
        $panier = $this->session->get('panier', []);
        $user = $this->security->getUser();
        
        //on va mettre tout dans une variable response
        $response = [];

        //si panier vide ou utilisateur non loguer ou crenau inexacte
        if(count($panier) < 1 || empty($user)){

            $response['response'] = false;
            $response['label'] = "warning";
            $response['message'] = "Vous n'êtes pas identifié(e)";
            $response['route'] = "app_login";

        }else{

            $reservation = new Reservation();

            //si bien identifie on enregistre et on redirige vers le paiement
            //décalage pour les réservations
            $decalageJour = $_ENV['NBRE_DE_JOUR_DECALAGE_RESERVATION'];
            $date = new DateTimeImmutable('now'.$horaire.':00', new DateTimeZone('Europe/Paris'));
            $date = $date->add(new DateInterval('P'.$decalageJour.'D'));
    
            $nbrReservationMaxParPeriode = $_ENV['NBRE_DE_RESERVATION_MAX_PAR_PERIODE'];
    
            //on verifié si le créneau est toujours disponible
            $nbreReservation = $this->reservationRepository->findBy(['createdAt' => $date]);
            if($nbrReservationMaxParPeriode - count($nbreReservation) > 0){
    
                    $reservation->setUser($user)
                                ->setToken($this->generateToken())
                                ->setStatut(0) // non payé
                                ->setCreatedAt($date);
    
                    $this->entityManager->persist($reservation);
                    $this->entityManager->flush();

                    //pour chaque produit dans le panier on met dans le detail réservation
                    foreach($panier as $id => $quantity){
                        $reservationDetails = new ReservationDetails();
                        $produit = $this->produitRepository->find($id);

                        $reservationDetails->setReservation($reservation)
                                           ->setQuantity($quantity)
                                           ->setProduit($produit)
                                           ->setPrice($produit->getPrix())
                                           ->setTotal($quantity * $produit->getPrix());

                        $this->entityManager->persist($reservationDetails);
                        $this->entityManager->flush();            
                    }
    
                    //on supprime le panier
                    $this->session->remove('panier');

                    //et dit que tout est ok ET on dirige vers le paiement
                    $response['response'] = true;
                    $response['route'] = "paiement_checkout";
                    $response['token'] = $reservation->getToken();
  
            }else{
    
                $response['response'] = false;
                $response['label'] = "warning";
                $response['message'] = "Dernier créneau réservé à l'instant, désolé...";
                $response['route'] = "panier";
 
            }
        }

        return $response;
    }

    public function removeReservation(string $token)
    {
        $panier = $this->session->get('panier', []);
        $user = $this->security->getUser();

        //on va mettre tout dans une variable response
        $response = [];

        //si panier vide ou utilisateur non loguer ou crenau inexacte
        if(count($panier) < 1 || empty($user)){

            $response['label'] = "warning";
            $response['message'] = "Vous n'êtes pas identifié(e)";
            $response['route'] = "app_login";

        }else{

            //on cherche la reservation pour la supprimer
            $reservation = $this->reservationRepository->findOneBy(['token' => $token, 'user' => $user, 'statut' => 0]);


            if(!empty($reservation)){
                $this->entityManager->remove($reservation);
                $this->entityManager->flush();
    
                $response['label'] = "success";
                $response['message'] = "Réservation annulée";
                $response['route'] = "panier";

            }else{
 
                $response['label'] = "warning";
                $response['message'] = "Réservation non trouvée!";
                $response['route'] = "accueil";
         
            }
        }

        return $response;
    }

    public function calculCreneauxDisponiblePourReservation(int $delai)
    {
        
        $decalageJour = $_ENV['NBRE_DE_JOUR_DECALAGE_RESERVATION'];
        $date = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $date->modify('+'.$decalageJour.' day');

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
        $horaireEboutique = $this->horairesEboutiqueRepository->findBy(['day' => $jour]);

        $start = new DateTimeImmutable( $Y.'-'.$m.'-'.$d.' '.$horaireEboutique[0]->getOuvertureMatin());
        $end = new DateTimeImmutable( $Y.'-'.$m.'-'.$d.' '.$horaireEboutique[0]->getFermetureSoir());

        //on vérifie si le jour est férié
        $timestamp = $date->getTimestamp();
        $isFerie = $this->calculeJourFerie($timestamp);

        //on mettra la reponse dans un tableau
        $response = [];
        //il faudra la date du jour (avec le décalage)
        $response['date'] = $date;

        //si c'est un jour férié OU FERMER
        if($isFerie == 1 || $horaireEboutique[0]->getOuvertureMatin() == 'FERMER'){
 
            $response['closed'] = true;

        }else{

            $user = $this->security->getUser();
    
            // on cherche si l'utilisateur a une reservation non payé en cours
            $reservation = $this->reservationRepository->verifReservationNonPayeeExiste($user, $start, $end);
    
            if(empty($reservation)){
    
                $interval = new DateInterval('PT'.$delai.'M'); //tous les X min param de la function
                $daterange = new DatePeriod($start, $interval ,$end);
                
 

                //on va mettre les periodes libres dans un tableau
                $freeRanges = [];
                $freeRangesCount = [];
                $nbrReservationMaxParPeriode = $_ENV['NBRE_DE_RESERVATION_MAX_PAR_PERIODE'];

                //pour chaque période on va vérifié s'il en reste de libre
                foreach($daterange as $range){
                    $nbreReservation = $this->reservationRepository->findBy(['createdAt' => $range]);

                    if(count($nbreReservation) < $nbrReservationMaxParPeriode){
                        $resteRange = $nbrReservationMaxParPeriode - count($nbreReservation);
                        $freeRanges[] = $range;
                        $freeRangesCount[] = $resteRange;
                    }
                }

                $response['reservations'] = false;
                $response['jourDelaSemaine'] = $jour;
                $response['ranges']['ranges'] = $freeRanges;
                $response['ranges']['rangesCount'] = $freeRangesCount;
    
            }else{
                $response['jourDelaSemaine'] = $jour;
                $response['reservations'] = $reservation;
            }
        }

        //on retourne la reponse final
        return $response; 
    }

    public function calculeJourFerie($timestamp)
    {
        $jour = date("d", $timestamp);
        $mois = date("m", $timestamp);
        $annee = date("Y", $timestamp);
        $EstFerie = 0;
        // dates fériées fixes
    
        if($jour == 1 && $mois == 1) $EstFerie = 1; // 1er janvier
        if($jour == 1 && $mois == 5) $EstFerie = 1; // 1er mai
        if($jour == 8 && $mois == 5) $EstFerie = 1; // 8 mai
        if($jour == 14 && $mois == 7) $EstFerie = 1; // 14 juillet
        if($jour == 15 && $mois == 8) $EstFerie = 1; // 15 aout
        if($jour == 1 && $mois == 11) $EstFerie = 1; // 1 novembre
        if($jour == 11 && $mois == 11) $EstFerie = 1; // 11 novembre
        if($jour == 25 && $mois == 12) $EstFerie = 1; // 25 décembre
        // fetes religieuses mobiles
        $pak = easter_date($annee);
        $jp = date("d", $pak);
        $mp = date("m", $pak);
        if($jp == $jour && $mp == $mois){ $EstFerie = 1;} // Pâques
        $lpk = mktime(date("H", $pak), date("i", $pak), date("s", $pak), date("m", $pak)
        , date("d", $pak) +1, date("Y", $pak) );
        $jp = date("d", $lpk);
        $mp = date("m", $lpk);
        if($jp == $jour && $mp == $mois){ $EstFerie = 1; }// Lundi de Pâques
        $asc = mktime(date("H", $pak), date("i", $pak), date("s", $pak), date("m", $pak)
        , date("d", $pak) + 39, date("Y", $pak) );
        $jp = date("d", $asc);
        $mp = date("m", $asc);
        if($jp == $jour && $mp == $mois){ $EstFerie = 1;}//ascension
        $pe = mktime(date("H", $pak), date("i", $pak), date("s", $pak), date("m", $pak),
        date("d", $pak) + 49, date("Y", $pak) );
        $jp = date("d", $pe);
        $mp = date("m", $pe);
        if($jp == $jour && $mp == $mois) {$EstFerie = 1;}// Pentecôte
        $lp = mktime(date("H", $asc), date("i", $pak), date("s", $pak), date("m", $pak),
        date("d", $pak) + 50, date("Y", $pak) );
        $jp = date("d", $lp);
        $mp = date("m", $lp);
        if($jp == $jour && $mp == $mois) {$EstFerie = 1;}// lundi Pentecôte
        // Samedis et dimanches
        //$jour_sem = jddayofweek(unixtojd($timestamp), 0);
        //if($jour_sem == 0 || $jour_sem == 6) $EstFerie = 1;
        // ces deux lignes au dessus sont à retirer si vous ne désirez pas faire
        // apparaitre les
        // samedis et dimanches comme fériés.
        return $EstFerie;
    }
   
    public function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(100)), '+/', '-_'), '=');
    }
}
