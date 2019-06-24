<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/admin/pages/forms/{page}", name="generic_form")
     */
    public function generic_form($page)
    {
    	$classConst = $page.'Type::class';
    	$em = $this->getDoctrine()->getManager();
    	$repo = $em->getRepository($classConst);

    	$entity = $repo->findAll();

    	if(!isset($entity[0])){
    		$entity = new $page();
    	}

    	$form = $this->createForm($classConst, $entity);
        return $this->render('page/index.html.twig', [
            'form' => $form,
        ]);
    }
}
