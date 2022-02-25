<?php

namespace App\Controller\Site;

use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request, CategorieRepository $categorieRepository): Response
    {
        $hostname = $request->getSchemeAndHttpHost();
        $categories = $categorieRepository->findAll();

        //tableau vide
        $urls = [];

        //liste des urls directes à completer
        $urls[] = [
                'loc'        => $this->generateUrl('accueil'),
                'changefreq' => "monthly", //monthly,daily
                'priority'   => 0.8
                ];
        $urls[] = [
            'loc'        => $this->generateUrl('personnel'),
            'changefreq' => "monthly", //monthly,daily
            'priority'   => 0.8
            ];

        //liste des urls de la eboutique
        $urls[] = [
                'loc'        => $this->generateUrl('eboutique_accueil'),
                'changefreq' => "monthly",
                'priority'   => 0.8
                ];
        foreach($categories as $categorie){
            $urls[] = [
                'loc'     => $this->generateUrl('eboutique_categorie_liste_produits', ['slugCategorie' => $categorie->getSlug()]),
                'lastmod' => $categorie->getCreatedAt()->format('Y-m-d'),
                'changefreq' => "monthly",
			    'priority' => 0.8
            ];
        }

        $response = new Response(
            $this->renderView('site/sitemap/index.html.twig', [
                'urls'     => $urls,
                'hostname' => $hostname
            ]),
            200
        );

        $response->headers->set('Content-type', 'text/xml');
        
        return $response;
    }
}
