<?php

namespace App\Service;

use App\Entity\Document;
use App\Repository\ProduitRepository;
use App\Repository\DocumentRepository;
use App\Repository\InfosLegalesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class DocumentService
{
    public function __construct(
        DocumentRepository $documentRepository,
        EntityManagerInterface $entityManager,
        ProduitRepository $produitRepository,
        Security $security,
        SessionInterface $session,
        InfosLegalesRepository $infosLegalesRepository)
    {
        $this->documentRepository = $documentRepository;
        $this->produitRepository = $produitRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->session = $session;
        $this->infosLegalesRepository = $infosLegalesRepository;
    }

    public function creationNouveauDocument($statut,$reservation)
    {
        
        $panier = $this->session->get('panier');
        $panierWithData = [];
        $user = $this->security->getUser();

        foreach($panier as $id => $quantity){
            $panierWithData[] = [
                'produit'  => $this->produitRepository->find($id),
                'quantity' => $quantity
            ];
        }
        //on fait le total du panier en HT
        $totalHT = 0;

        foreach($panierWithData as $item){
            $totalItem = $item['produit']->getPrix() * $item['quantity'];
            $totalHT += $totalItem;
        }

        $infoLegales = $this->infosLegalesRepository->findAll();
        $multiplicateurTva = $infoLegales[0]->getTva();

        $totalTTC = $totalHT * $multiplicateurTva;
        $totalTVA = $totalTTC - $totalHT;

        $now = new \DateTimeImmutable('now');
        $year = $now->format('Y');
        $m = $now->format('m');

        //on cherche le dernier document avec le statut demander de l'annee demandee
        $lastDocument = $this->documentRepository->findBy(['statut' => $statut, 'year' => $year],['id' => 'DESC'],1);
        
        //format document: AAAAMM00000  soit 99999 possible
        //pas encore d'enregistrement
        if(empty($lastDocument)){  
            $numero = $year.$m.'00001';
        }else{
            foreach($lastDocument as $doc){
                $numero = $doc->getNumeroDevis();
                $numero += 1;   //on incremente de 1
            }
        
        }

        $document = new Document();
        $document->setToken($this->generateToken())
                 ->setTotalHT($totalHT)
                 ->setTotalTVA($totalTVA)
                 ->setTotalTTC($totalTTC)
                 ->setReservation($reservation)
                 ->setUser($user)
                 ->setStatut('DEV')
                 ->setCreatedAt($now)
                 ->setNumeroDevis($numero)
                 ->setYear($year);

        
                 $this->entityManager->persist($document);
                 $this->entityManager->flush();
                
        return $document;
    }

    public function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }


 
}
