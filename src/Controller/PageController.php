<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PageController extends AbstractController
{
    /**
     * @Route("/admin/pages/forms/{page}", name="generic_form")
     */
    public function generic_form(Request $request, $page)
    {
    	$classConst = 'App\Entity\\'.$page;
    	$formConst = 'App\Form\\'.$page.'Type';
    	$em = $this->getDoctrine()->getManager();
    	$repo = $em->getRepository($classConst);

    	$entity = $repo->findAll();

    	if(!isset($entity[0])){
    		$entity = new $classConst();
    	}else{
    		$entity = $entity[0];
    	}

    	$form = $this->createForm($formConst, $entity);
        $form->add('Save', SubmitType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entity = $form->getData();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('admin_pages');
        }

        return $this->render('page/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
