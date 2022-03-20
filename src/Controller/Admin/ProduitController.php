<?php

namespace App\Controller\Admin;

use App\Entity\InfosLegales;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\InfosLegalesRepository;
use App\Repository\ProduitRepository;
use App\Service\Calculs;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin/produit", name="admin_")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository, InfosLegalesRepository $infosLegalesRepository): Response
    {
        //on recupere tous les produits
        $produits = $produitRepository->findAll();

        //on va stocker les images
        $images = [];
        foreach ($produits as $key => $produit) {
            $images[$key] = stream_get_contents($produit->getImageBlob());
        }

        return $this->render('admin/produit/index.html.twig', [
            'produits' => $produits,
            'images' => $images,
            'infosLegales' => $infosLegalesRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="produit_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageSend = $form->get('imageBlob')->getData();

            if(!$imageSend){
                //message flash
                $this->addFlash('danger', 'L\'image du produit est obligatoire !');
                 //on retourne à la page précèdante
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            }else{
                $imageBase64 = base64_encode(file_get_contents($imageSend));
            }

            $produit->setImageBlob($imageBase64)
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setSlug($slugger->slug($produit->getSlug()));
     
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('admin_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'image' => null
        ]);
    }

    /**
     * @Route("/{id}", name="produit_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render('admin/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="produit_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request,
            Produit $produit,
            EntityManagerInterface $entityManager,
            SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageSend = $form->get('imageBlob')->getData();
            if($imageSend){
                $imageBase64 = base64_encode(file_get_contents($imageSend));
                $produit->setImageBlob($imageBase64);
            }
       
            $produit->setSlug($slugger->slug($produit->getSlug()));
                  
            $entityManager->flush();

            return $this->redirectToRoute('admin_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'image' => stream_get_contents($produit->getImageBlob())
        ]);
    }

    /**
     * @Route("/{id}", name="produit_delete", methods={"POST"})
     */
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
    }

}