<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function Test()
    {
        $classConst = 'App\Entity\Test';
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($classConst);
        $entity = $repo->findAll();
        $entity = $entity[0];

        return $this->render('theme/pages/test.html.twig', [
            'page' => $entity,
        ]);
    }
    
}
