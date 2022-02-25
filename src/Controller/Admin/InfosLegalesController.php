<?php

namespace App\Controller\Admin;

use App\Entity\InfosLegales;
use App\Form\InfosLegalesType;
use App\Repository\InfosLegalesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/configuration/infos-legales", name="admin_")
 */
class InfosLegalesController extends AbstractController
{
    /**
     * @Route("/", name="infos_legales_index", methods={"GET"})
     */
    public function index(InfosLegalesRepository $infosLegalesRepository): Response
    {
        $infos = $infosLegalesRepository->findAll();

        return $this->render('admin/infos_legales/index.html.twig', [
            'infos_legales' => $infos,
        ]);
    }

    /**
     * @Route("/new", name="infos_legales_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $infosLegale = new InfosLegales();
        $form = $this->createForm(InfosLegalesType::class, $infosLegale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($infosLegale);
            $entityManager->flush();

            return $this->redirectToRoute('admin_infos_legales_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/infos_legales/new.html.twig', [
            'infos_legale' => $infosLegale,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="infos_legales_show", methods={"GET"})
     */
    public function show(InfosLegales $infosLegale): Response
    {
        return $this->render('admin/infos_legales/show.html.twig', [
            'infos_legale' => $infosLegale,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="infos_legales_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, InfosLegales $infosLegale, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InfosLegalesType::class, $infosLegale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('infos_legales_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/infos_legales/edit.html.twig', [
            'infos_legale' => $infosLegale,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="infos_legales_delete", methods={"POST"})
     */
    public function delete(Request $request, InfosLegales $infosLegale, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$infosLegale->getId(), $request->request->get('_token'))) {
            $entityManager->remove($infosLegale);
            $entityManager->flush();
        }

        return $this->redirectToRoute('infos_legales_index', [], Response::HTTP_SEE_OTHER);
    }
}
