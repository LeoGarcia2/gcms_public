<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends AbstractController
{
    // /**
    //  * @Route("/nompage", name="nompage")
    //  */
    // public function nomPage(Request $request)
    // {
    //     $classConst = 'App\Entity\nomPage';
    //     $em = $this->getDoctrine()->getManager();
    //     $repo = $em->getRepository($classConst);
    //     $entity = $repo->findAll();
    //     $entity = $entity[0];

    //     return $this->render('theme/pages/nomPage.html.twig', [
    //         'page' => $entity,
    //     ]);
    // }
}
