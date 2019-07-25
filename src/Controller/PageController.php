<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PageController extends AbstractController
{
    //pageaaastart
    /**
     * @Route("/", name="pageaaa")
     */
    public function PageAaa()
    {
        $classConst = 'App\Entity\PageAaa';
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($classConst);
        $entity = $repo->findByPublished(true);
        $entity = $entity[0];

        return $this->render('theme/pages/pageaaa.html.twig', [
            'page' => $entity,
        ]);
    }//pageaaaend

}