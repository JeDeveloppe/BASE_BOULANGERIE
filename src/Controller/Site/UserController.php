<?php

namespace App\Controller\Site;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/espace-client/dashboard", name="user_dashboard")
     */
    public function index(Security $security): Response
    {
        $user = $security->getUser();

        if(!$user){
            //message flash
            $this->addFlash('danger', 'Vous n\'êtes pas identifié(e) !');
            return $this->redirectToRoute('app_login');

        }else{

            return $this->render('site/espace_client/dashboard.html.twig', [
                'user' => $user,
            ]);

        }
    }
}
