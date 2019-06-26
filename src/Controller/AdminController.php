<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Controller\ConsoleController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/pages", name="admin_pages")
     */
    public function pages()
    {
    	$pages = [];
    	$entities = scandir('../src/Entity');
    	foreach($entities as $entity){
    		if(preg_match_all('#^(Page)(.)*#', $entity)){
    			$entity = substr($entity, 0, strlen($entity) - 4);
    			$pages[] = $entity;
    		}
    	}
        return $this->render('admin/pages.html.twig', [
        	'pages' => $pages
        ]);
    }

    /**
     * @Route("/admin/pages/new", name="admin_new_page")
     */
    public function new_page(ConsoleController $cC, KernelInterface $kernel)
    {
    	if(isset($_POST['entity_name']) && $_POST['entity_name'] != ''){
    		$entity_name = 'Page'.ucfirst($_POST['entity_name']);
    		$cC->createEntity($kernel, $entity_name);
    	}
        return $this->redirectToRoute('fields_page', ['page' => $entity_name]);
    }

    /**
     * @Route("/admin/pages/fields/{page}", name="fields_page")
     */
    public function fields_page(Request $request, ConsoleController $cC, KernelInterface $kernel, $page)
    {
    	$pageName = $page;

    	if($request->isMethod('post')){
            file_put_contents('../src/Entity/'.$pageName.'.php', $_POST['pageArea']);

    		$cC->regenerateEntity($kernel, $pageName);
    		$cC->createEntityForm($kernel, $pageName);
            $cC->fullMigration($kernel);

            //générer template et route

            $formFile = file_get_contents('../src/Form/'.$pageName.'Type.php');
            $formFile = preg_replace("#'data_class' => ".$pageName."::class,#", "'data_class' => ".$pageName."::class,\n            'allow_extra_fields' => true", $formFile);
            file_put_contents('../src/Form/'.$pageName.'Type.php', $formFile);

    		return $this->redirectToRoute('generic_form', [ 'page' => $pageName ]);
    	}

    	$page = file_get_contents('../src/Entity/'.$pageName.'.php');

    	return $this->render('admin/page_fields.html.twig', [
    		'pageName' => $pageName,
        	'page' => $page,
        ]);
    }

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

        return $this->render('page/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/pages/preview/{page}", name="generic_page")
     */
    public function generic_page(Request $request, $page)
    {
        $classConst = 'App\Entity\\'.$page;
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($classConst);

        $entity = $repo->findAll();

        $entity = (array) $entity[0];

        foreach($entity as $name => $field){
            $oldName = $name;
            $name = str_replace($classConst, '', $name);
            $entity[$name] = $entity[$oldName];
            unset($entity[$oldName]);
        }

        return $this->render('page/page.html.twig', [
            'pageName' => $page,
            'entity' => $entity,
        ]);
    }
}