<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Controller\ConsoleController;
use App\Controller\TaxonomyController;
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
     * @Route("/admin/clearcache/{lastRoute}", name="admin_cacheclear")
     */
    public function cacheClear(ConsoleController $cC, KernelInterface $kernel, $lastRoute)
    {
        $cC->clearCache($kernel);
        return $this->redirectToRoute($lastRoute);
    }

    /**
     * @Route("/admin/logs", name="admin_logs")
     */
    public function logs()
    {
        $logFile = fopen('../var/log/dev.log','r');
        $logs = [];
  
        while(!feof($logFile)){
            $logLine = fgets($logFile);
            $logs[] = $logLine;
        }
        fclose($logFile);

        $logs = array_reverse($logs);
        $logs = array_slice($logs, 0, 100);

        return $this->render('admin/logs.html.twig', [
            'logs' => $logs
        ]);
    }

    /**
     * @Route("/admin/database", name="admin_database")
     */
    public function database(Request $request, ConsoleController $cC, KernelInterface $kernel)
    {
        $databaseFile = fopen('../.env','r');

        $databaseConf = '';
        $databaseContent = [];
  
        while(!feof($databaseFile)){
            $databaseLine = fgets($databaseFile);
            $databaseContent[] = $databaseLine;
            if(strpos($databaseLine, 'DATABASE_URL=') !== false){
                $databaseConf = $databaseLine;
            }
        }
        fclose($databaseFile);

        if($request->isMethod('post')){
            $newDatabaseConf = $_POST['databaseConf'];

            for($i = 0; $i < count($databaseContent); $i++){
                if(strpos($databaseContent[$i], 'DATABASE_URL=') !== false){
                    $databaseContent[$i] = $newDatabaseConf."\n";
                }
            }
            $newDatabaseConf = implode('', $databaseContent);
            file_put_contents('../.env', $newDatabaseConf);
            $cC->createDatabase($kernel);
            $cC->fullMigration($kernel);

            return $this->redirectToRoute('admin_database');
        }

        return $this->render('admin/database.html.twig', [
            'databaseConf' => $databaseConf
        ]);
    }

    /**
     * @Route("/admin/siteconf", name="admin_siteconf")
     */
    public function siteconf(Request $request)
    {
        $confFile = fopen('../config/packages/twig.yaml','r');

        $nameConf = '';
        $sloganConf = '';
        $localeConf = '';
        $confContent = [];
  
        while(!feof($confFile)){
            $confLine = fgets($confFile);
            $confContent[] = $confLine;
            if(strpos($confLine, 'site_name:') !== false){
                $nameConf = $confLine;
            }
            if(strpos($confLine, 'site_slogan:') !== false){
                $sloganConf = $confLine;
            }
            if(strpos($confLine, 'site_locale:') !== false){
                $localeConf = $confLine;
            }
        }
        fclose($confFile);

        $nameConf = substr($nameConf, 19, strlen($nameConf));
        $sloganConf = substr($sloganConf, 21, strlen($sloganConf));
        $localeConf = substr($localeConf, 21, strlen($localeConf));

        if($request->isMethod('post')){
            $newNameConf = '        site_name: '.$_POST['nameConf'];
            $newSloganConf = '        site_slogan: '.$_POST['sloganConf'];
            $newLocaleConf = '        site_locale: '.$_POST['localeConf'];

            for($i = 0; $i < count($confContent); $i++){
                if(strpos($confContent[$i], 'site_name:') !== false){
                    $confContent[$i] = $newNameConf."\n";
                }
                if(strpos($confContent[$i], 'site_slogan:') !== false){
                    $confContent[$i] = $newSloganConf."\n";
                }
                if(strpos($confContent[$i], 'site_locale:') !== false){
                    $confContent[$i] = $newLocaleConf."\n";
                }
            }
            $newConf = implode('', $confContent);
            file_put_contents('../config/packages/twig.yaml', $newConf);

            return $this->redirectToRoute('admin_siteconf');
        }

        return $this->render('admin/siteconf.html.twig', [
            'nameConf' => $nameConf,
            'sloganConf' => $sloganConf,
            'localeConf' => $localeConf
        ]);
    }

    /**
     * @Route("/admin/files", name="admin_files")
     */
    public function files()
    {
        if(isset($_GET['delete'])){
            unlink('../public/assets/images/'.$_GET['delete']);
        }

        $filesTmp = scandir('../public/assets/images');
        $files = [];

        for($i = 0; $i < count($filesTmp); $i++){
            if($filesTmp[$i] != '.' && $filesTmp[$i] != '..' && $filesTmp[$i] != 'readme.md'){
                array_push($files, $filesTmp[$i]);
            }
        }

        return $this->render('admin/files.html.twig', [
            'files' => $files
        ]);
    }

    /**
     * @Route("/admin/taxonomy", name="admin_taxonomy")
     */
    public function taxonomy(TaxonomyController $tC)
    {
        $taxonomy = get_class_vars(get_class($tC));

        return $this->render('admin/taxonomy.html.twig', [
            'taxonomy' => $taxonomy
        ]);
    }

    /**
     * @Route("/admin/pages", name="admin_pages")
     */
    public function pages(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->isMethod('post')){
            $entitiesToUpdate = [];
            $rawNames = [];
            foreach($_POST['pages'] as $pageToControl)
            {
                if($_POST['controls'] != 'delete'){
                    $repoForControl = $em->getRepository('App\Entity\\'.$pageToControl);
                    $entitiesToUpdate[] = $repoForControl->findAll()[0];
                }else{
                    $rawNames[] = $pageToControl;
                }
            }
            switch($_POST['controls']){
                case 'publish':
                    foreach($entitiesToUpdate as $entityToControl)
                    {
                        $entityToControl->setPublished(true);
                        $em->persist($entityToControl);
                    }
                    break;
                case 'unpublish':
                    foreach($entitiesToUpdate as $entityToControl)
                    {
                        $entityToControl->setPublished(false);
                        $em->persist($entityToControl);
                    }
                    break;
                case 'delete':
                    foreach($rawNames as $rawName){
                        unlink('../src/Entity/'.$rawName.'.php');
                        unlink('../src/Form/'.$rawName.'Type.php');
                        unlink('../src/Repository/'.$rawName.'Repository.php');
                        unlink('../templates/theme/pages/'.strtolower($rawName).'.html.twig');
                        $pageController = file_get_contents('../src/Controller/PageController.php');
                        $pageController = preg_replace("#\/\/".strtolower($rawName)."start([\s\S]*)\/\/".strtolower($rawName)."end#", "", $pageController);
                        file_put_contents('../src/Controller/PageController.php', $pageController);
                    }
                    break;
            }
            $em->flush();
        }

        $pages = [];
    	$entitiesPage = [];
    	$entities = scandir('../src/Entity');
    	foreach($entities as $entity){
    		if(preg_match_all('#^(Page)(.)*#', $entity)){
    			$entity = substr($entity, 0, strlen($entity) - 4);
    			$entitiesPage[] = $entity;
    		}
    	}
        foreach($entitiesPage as $entityPage){
            $classConst = 'App\Entity\\'.$entityPage;
            $repo = $em->getRepository($classConst);
            $pages[] = $repo->findAll()[0];
        }
        return $this->render('admin/pages.html.twig', [
            'pages' => $pages,
            'pagesE' => $entitiesPage,
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

            $template = file_get_contents('../templates/theme/pages/gcms_default.html.twig');
            $pageFields = '';

            foreach($_POST['pageFields'] as $field){
                $pageFields .= "<section>{{ page.".$field." }}</section>\n    ";
            }

            $template = preg_replace('#fieldsHere#', $pageFields, $template);
            file_put_contents('../templates/theme/pages/'.strtolower($pageName).'.html.twig', $template);

            $route = file_get_contents('../src/Controller/gcms_default_route');
            $pageController = file_get_contents('../src/Controller/PageController.php');

            if(isset($_POST['pageRoute']) && $_POST['pageRoute'] != ''){
                $routePath = strtolower($_POST['pageRoute']);
            }else{
                $routePath = strtolower($pageName);
            }
            $route = preg_replace('#pageroutedefault#', $routePath, $route);
            $route = preg_replace('#pagenamelowercase#', strtolower($pageName), $route);
            $route = preg_replace('#pagenameuppercase#', ucfirst($pageName), $route);

            $pageController = preg_replace("#AbstractController
{#", "AbstractController
{".$route, $pageController);
            file_put_contents('../src/Controller/PageController.php', $pageController);

            $formFile = file_get_contents('../src/Form/'.$pageName.'Type.php');
            $formFile = substr($formFile, 5);
            $formFile = preg_replace("#namespace App\\\Form;#", "namespace App\\\Form;\n\nuse Vich\\\UploaderBundle\\\Form\\\Type\\\VichFileType;", $formFile);


            if(isset($_POST['imageFields'])){
                foreach($_POST['imageFields'] as $field){
                    $formFile = preg_replace("#->add\('".$field."'\)#", "->add('".$field."File', VichFileType::class)", $formFile);
                }
            }                

            $formFile = preg_replace("#'data_class' => ".$pageName."::class,#", "'data_class' => ".$pageName."::class,\n            'allow_extra_fields' => true", $formFile);
            file_put_contents('../src/Form/'.$pageName.'Type.php', '<?php'.$formFile);

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

        $entity->setPublished(true);

        $form = $this->createForm($formConst, $entity);
        $form->add('Save', SubmitType::class);
        $form->remove('author');
        $form->remove('updatedAt');

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entity = $form->getData();
            $entity->setAuthor($this->getUser()->getUsername());
            $entity->setUpdatedAt(new \DateTime());
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('admin_pages');
        }

        return $this->render('admin/form.html.twig', [
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

        return $this->render('admin/page.html.twig', [
            'pageName' => $page,
            'entity' => $entity,
        ]);
    }
}