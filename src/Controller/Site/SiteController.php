<?php

namespace App\Controller\Site;

use App\Repository\BlogRepository;
use App\Service\CallApiService;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use App\Repository\InfosLegalesRepository;
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
    public function index(
        ProduitRepository $produitRepository,
        InfosLegalesRepository $infosLegalesRepository,
        BlogRepository $blogRepository): Response
    {
 
        $produits = $produitRepository->getRandomProducts(4);
        $imagesProduits = [];
        foreach ($produits as $key => $produit) {
            $imagesProduits[$key] = stream_get_contents($produit->getImageBlob());
        }

        $blogs = $blogRepository->findBy([],['createdAt' => 'DESC'], 2);
        $imagesBlogs = [];
        foreach ($blogs as $key => $blog) {
            $imagesBlogs[$key] = stream_get_contents($blog->getImageBlob());
        }

        return $this->render('site/index.html.twig', [
            'produits' => $produits,
            'imagesProduits'   => $imagesProduits,
            'blogs' => $blogs,
            'imagesBlogs'   => $imagesBlogs,
            'infosLegales' => $infosLegalesRepository->findAll()
        ]);
    }

    /**
     * @Route("/eboutique", name="eboutique_accueil")
     */
    public function eboutiqueAccueil(
        CategorieRepository $categorieRepository,
        ProduitRepository $produitRepository,
        Request $request,
        PaginatorInterface $paginator,
        InfosLegalesRepository $infosLegalesRepository
        ): Response
    {
        $categories = $categorieRepository->findAll();
        $donnees = $produitRepository->findBy(['isOnLine' => true]);
 

        $produits = $paginator->paginate(
            $donnees, // Requ??te contenant les donn??es ?? paginer (ici nos articles)
            $request->query->getInt('page', 1), // Num??ro de la page en cours, pass?? dans l'URL, 1 si aucune page
            12 // Nombre de r??sultats par page
        );

        $images = [];
        foreach ($produits as $key => $produit) {
            $images[$key] = stream_get_contents($produit->getImageBlob());
        }

        return $this->render('site/eboutique/index.html.twig', [
            'controller_name' => 'EboutiqueController',
            'categories'      => $categories,
            'produits'        => $produits,
            'images'          => $images,
            'infosLegales' => $infosLegalesRepository->findAll()
        ]);
    }

    /**
     * @Route("/eboutique/{slugCategorie}/", name="eboutique_categorie_liste_produits")
     */
    public function eboutiqueCategorieListeProduits(
        CategorieRepository $categorieRepository,
        ProduitRepository $produitRepository,
        InfosLegalesRepository $infosLegalesRepository,
        $slugCategorie,
        Request $request,
        PaginatorInterface $paginator): Response
    {
        $categories = $categorieRepository->findAll();
        $categorie = $categorieRepository->findOneBy(['slug' => $slugCategorie]);
        $donnees = $produitRepository->findBy(['isOnLine' => true, 'categorie' => $categorie]);

        $produits = $paginator->paginate(
            $donnees, // Requ??te contenant les donn??es ?? paginer (ici nos articles)
            $request->query->getInt('page', 1), // Num??ro de la page en cours, pass?? dans l'URL, 1 si aucune page
            12 // Nombre de r??sultats par page
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
            'images'          => $images,
            'infosLegales' => $infosLegalesRepository->findAll()
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

      /**
     * @Route("/conditions-generale-de-vente", name="cgv")
     */
    public function cgv(InfosLegalesRepository $infosLegalesRepository): Response
    {

        return $this->render('site/informations/cgv.html.twig', [
            'informationsLegales' =>  $infosLegalesRepository->findAll()
        ]);
    }

    /**
     * @Route("/mentions-legales", name="mentions-legales")
     */
    public function mentionsLegales(InfosLegalesRepository $infosLegalesRepository): Response
    {

        return $this->render('site/informations/mentions_legales.html.twig', [
            'informationsLegales' =>  $infosLegalesRepository->findAll()
        ]);
    }
}
