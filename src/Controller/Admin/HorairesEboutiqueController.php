<?php

namespace App\Controller\Admin;

use App\Entity\HorairesEboutique;
use App\Form\HorairesEboutiqueType;
use App\Repository\HorairesEboutiqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/horaires/eboutique", name="admin_")
 */
class HorairesEboutiqueController extends AbstractController
{
    /**
     * @Route("/", name="horaires_eboutique_index", methods={"GET"})
     */
    public function index(HorairesEboutiqueRepository $horairesEboutiqueRepository): Response
    {
        return $this->render('admin/horaires_eboutique/index.html.twig', [
            'horaires_eboutiques' => $horairesEboutiqueRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="horaires_eboutique_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, HorairesEboutiqueRepository $horairesEboutiqueRepository): Response
    {
        $horairesEboutique = new HorairesEboutique();
        $form = $this->createForm(HorairesEboutiqueType::class, $horairesEboutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $verificationDoublon = $horairesEboutiqueRepository->find($horairesEboutique->getDay());

            if(!empty($verificationDoublon)){
                //message flash
                $this->addFlash('danger', 'Journée déjà existante !');
                return $this->redirectToRoute('admin_horaires_eboutique_index');
            }else{

                $entityManager->persist($horairesEboutique);
                $entityManager->flush();

                return $this->redirectToRoute('admin_horaires_eboutique_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('admin/horaires_eboutique/new.html.twig', [
            'horaires_eboutique' => $horairesEboutique,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="horaires_eboutique_show", methods={"GET"})
     */
    public function show(HorairesEboutique $horairesEboutique): Response
    {
        return $this->render('admin/horaires_eboutique/show.html.twig', [
            'horaires_eboutique' => $horairesEboutique,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="horaires_eboutique_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, HorairesEboutique $horairesEboutique, EntityManagerInterface $entityManager, HorairesEboutiqueRepository $horairesEboutiqueRepository): Response
    {
        $form = $this->createForm(HorairesEboutiqueType::class, $horairesEboutique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $verificationDoublon = $horairesEboutiqueRepository->find($horairesEboutique->getDay());

            if(!empty($verificationDoublon)){
                //message flash
                $this->addFlash('danger', 'Journée déjà existante !');
                return $this->redirectToRoute('admin_horaires_eboutique_index');
            }else{
                $entityManager->flush();
            }

            //message flash
            $this->addFlash('success', 'Journée mise à jour !');
            return $this->redirectToRoute('admin_horaires_eboutique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/horaires_eboutique/edit.html.twig', [
            'horaires_eboutique' => $horairesEboutique,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="horaires_eboutique_delete", methods={"POST"})
     */
    public function delete(Request $request, HorairesEboutique $horairesEboutique, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$horairesEboutique->getId(), $request->request->get('_token'))) {
            $entityManager->remove($horairesEboutique);
            $entityManager->flush();
        }

        return $this->redirectToRoute('horaires_eboutique_index', [], Response::HTTP_SEE_OTHER);
    }
}
