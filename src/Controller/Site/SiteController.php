<?php

namespace App\Controller\Site;

use App\Service\CallApiService;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    /**
     * @Route("/eboutique", name="eboutique_accueil")
     */
    public function eboutiqueAccueil(
        CategorieRepository $categorieRepository,
        ProduitRepository $produitRepository,
        Request $request,
        PaginatorInterface $paginator
        ): Response
    {
        $categories = $categorieRepository->findAll();
        $donnees = $produitRepository->findBy(['isOnLine' => true]);

        $produits = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            12 // Nombre de résultats par page
        );

        //on va stocker les images
        $images = [];
        foreach ($produits as $key => $produit) {
            $images[$key] = stream_get_contents($produit->getImageBlob());
        }

        return $this->render('site/eboutique/index.html.twig', [
            'controller_name' => 'EboutiqueController',
            'categories'      => $categories,
            'produits'        => $produits,
            'images'          => $images
        ]);
    }

    /**
     * @Route("/eboutique/{slugCategorie}/", name="eboutique_categorie_liste_produits")
     */
    public function eboutiqueCategorieListeProduits(
        CategorieRepository $categorieRepository,
        ProduitRepository $produitRepository,
        $slugCategorie,
        Request $request,
        PaginatorInterface $paginator): Response
    {
        $categories = $categorieRepository->findAll();
        $categorie = $categorieRepository->findOneBy(['slug' => $slugCategorie]);
        $donnees = $produitRepository->findBy(['isOnLine' => true, 'categorie' => $categorie]);

        $produits = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            12 // Nombre de résultats par page
        );

        //on va stocker les images
        $images = [];
        foreach ($produits as $key => $produit) {
            $images[$key] = stream_get_contents($produit->getImageBlob());
        }

        return $this->render('site/eboutique/index.html.twig', [
            'controller_name' => 'EboutiqueController',
            'categories'      => $categories,
            'produits'        => $produits,
            'images'          => $images
        ]);
    }

    /**
     * @Route("/notre-personnel", name="personnel")
     */
    public function affichagePersonnel(CallApiService $callApiService): Response
    {
        $datas = $callApiService->getPersonnes();
        
        return $this->render('site/personnel/index.html.twig', [
            'personnes' => $datas['results'],
        ]);
    }
}
