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
        
        $totaux = $this->session->get('totaux');
        $user = $this->security->getUser();


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
                 ->setTotalHT($totaux['totalHT'])
                 ->setTotalTVA($totaux['totalTVA'])
                 ->setTotalTTC($totaux['totalTTC'])
                 ->setReservation($reservation)
                 ->setUser($user)
                 ->setStatut($statut)
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
