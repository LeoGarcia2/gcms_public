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
     * @Route("/admin/doc", name="admin_doc")
     */
    public function doc()
    {
        return $this->render('admin/doc.html.twig');
    }

    /**
     * @Route("/admin/routes", name="admin_routes")
     */
    public function routes()
    {
        $pagesRoutes = [];
        $pagesPath = [];
        $entriesRoutes = [];
        $listingsPath = [];
        $entriesPath = [];

        $pageController = file_get_contents('../src/Controller/PageController.php');
        preg_match_all('#\* @Route\(\"(.*)",#', $pageController, $pagesRoutesTmp);
        preg_match_all('#, name=\"(.*)\"\)#', $pageController, $pagesPathTmp);

        foreach($pagesRoutesTmp[0] as $prTmp){
            $pagesRoutes[] = substr($prTmp, 10, -2);
        }        
        foreach($pagesPathTmp[0] as $prTmp){
            $pagesPath[] = substr($prTmp, 8, -2);
        }

        $entities = scandir('../src/Entity');

        foreach($entities as $entity){
            if(preg_match_all('#^(CT)(.)*#', $entity)){
                $entity = substr($entity, 2, -4);
                $entriesRoutes[] = $entity;
                $entriesPath[] = 'entry\', {ctWithoutCt: '.$entity.', id: {id}}';
                $listingsPath[] = 'listing\', {ctWithoutCt: '.$entity.'}';
            }
        }
        
        return $this->render('admin/routes.html.twig', [
            'pagesRoutes' => $pagesRoutes,
            'pagesPath' => $pagesPath,
            'entriesRoutes' => $entriesRoutes,
            'listingsPath' => $listingsPath,
            'entriesPath' => $entriesPath,
        ]);
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
    public function taxonomy(Request $request, TaxonomyController $tC, ConsoleController $cC, KernelInterface $kernel)
    {
        $em = $this->getDoctrine()->getManager();

        $taxonomyController = file_get_contents('../src/Controller/TaxonomyController.php');

        if($request->isMethod('post')){            
            if(isset($_POST['nameOfNewTaxonomy'])){                
                $taxonomyController = preg_replace("#}#", "    public $".$_POST['nameOfNewTaxonomy']." = [];\n}", $taxonomyController);

                file_put_contents('../src/Controller/TaxonomyController.php', $taxonomyController);

                return $this->redirectToRoute('admin_taxonomy');
            }
            if(isset($_POST['category']) && isset($_POST['toAdd'])){               
                preg_match("#public \\$".$_POST['category'].".*;#", $taxonomyController, $lineOfCategory);
                $lineOfCategory = $lineOfCategory[0];
                $lineOfCategory = preg_replace("#]#", "'".$_POST['toAdd']."',]", $lineOfCategory);
                $taxonomyController = preg_replace("#public \\$".$_POST['category'].".*;#", $lineOfCategory, $taxonomyController);

                file_put_contents('../src/Controller/TaxonomyController.php', $taxonomyController);

                return $this->redirectToRoute('admin_taxonomy');
            }
        }

        if(isset($_GET['from']) || isset($_GET['delete'])){            
            $entitiesToCheck = [];
            $entities = scandir('../src/Entity');

            foreach($entities as $entity){
                if(preg_match_all('#^(Page)(.)*#', $entity) || preg_match_all('#^(CT)(.)*#', $entity)){
                    $entity = substr($entity, 0, -4);
                    $entitiesToCheck[] = $entity;
                }
            }
        }

        if(isset($_GET['from'])){
            preg_match("#public \\$".$_GET['from'].".*;#", $taxonomyController, $lineOfCategory);
            $lineOfCategory = $lineOfCategory[0];
            $lineOfCategory = preg_replace("#'".$_GET['delete']."'\,#", "", $lineOfCategory);
            $taxonomyController = preg_replace("#public \\$".$_GET['from'].".*;#", $lineOfCategory, $taxonomyController);
            file_put_contents('../src/Controller/TaxonomyController.php', $taxonomyController);

            foreach($entitiesToCheck as $entityToCheck){
                $repo = $em->getRepository('App\Entity\\'.$entityToCheck);
                $entitiesInDb = $repo->findAll();

                foreach($entitiesInDb as $entityInDb){
                    if(method_exists($entityInDb, 'getTaxo'.$_GET['from'])){
                        $getter = 'getTaxo'.$_GET['from'];
                        $setter = 'setTaxo'.$_GET['from'];
                        $taxo = $entityInDb->$getter();

                        if(in_array($_GET['delete'], $taxo)){
                            $key = array_search($_GET['delete'], $taxo);
                            unset($taxo[$key]);

                            $entityInDb->$setter($taxo, 1);
                            $em->persist($entityInDb);
                        }
                    }
                }
            }

            $em->flush();

            return $this->redirectToRoute('admin_taxonomy');
        }else{
            if(isset($_GET['delete'])){
                $taxonomyController = preg_replace("#public \\$".$_GET['delete'].".*;#", "", $taxonomyController);
                $taxonomyController = preg_replace("#    \n#", "", $taxonomyController);
                file_put_contents('../src/Controller/TaxonomyController.php', $taxonomyController);

                foreach($entitiesToCheck as $entityToCheck){
                    $entity = file_get_contents('../src/Entity/'.$entityToCheck.'.php');
                    $form = file_get_contents('../src/Form/'.$entityToCheck.'Type.php');
                    $entity = preg_replace("#\/\*\*([\s\S]){10,50}private \\\$taxo".ucfirst($_GET['delete']).";([\s\S]){6}#", "", $entity);
                    $entity = preg_replace("#public function getTaxo".ucfirst($_GET['delete'])."([\s\S]){50,100}public function setTaxo".ucfirst($_GET['delete'])."([\s\S]){50,100}return \\\$this;([\s\S]){12}#", "", $entity);
                    $form = preg_replace("#->add\(\'taxo".ucfirst($_GET['delete'])."\'([\s\S]){100,300}\]\)([\s\S]){13}#", "", $form);

                    file_put_contents('../src/Entity/'.$entityToCheck.'.php', $entity);
                    file_put_contents('../src/Form/'.$entityToCheck.'Type.php', $form);
                }

                $cC->fullMigration($kernel);       

                return $this->redirectToRoute('admin_taxonomy');
            }
        }

        $taxonomy = get_class_vars(get_class($tC));

        return $this->render('admin/taxonomy.html.twig', [
            'taxonomy' => $taxonomy
        ]);
    }

    /**
     * @Route("/admin/components", name="admin_components")
     */
    public function components(Request $request){
        $components = [];
        $componentsController = file_get_contents('../src/Controller/ComponentsController.php');

        preg_match_all('#function(.*)#', $componentsController, $componentsFunctions);

        foreach($componentsFunctions[0] as $componentFunction){            
            $components[] = substr($componentFunction, 9, -2);
        }

        return $this->render('admin/components.html.twig', [
            'components' => $components
        ]);
    }

    /**
     * @Route("/admin/pages", name="admin_pages")
     */
    public function pages(Request $request, ConsoleController $cC, KernelInterface $kernel)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->isMethod('post')){
            $entitiesToUpdate = [];
            $rawNames = [];

            foreach($_POST['pages'] as $pageToControl){
                if($_POST['controls'] != 'delete'){
                    $repoForControl = $em->getRepository('App\Entity\\'.$pageToControl);
                    $entitiesToUpdate[] = $repoForControl->findAll()[0];
                }else{
                    $entityToDelete = $em->getRepository('App\Entity\\'.$pageToControl)->findAll()[0];
                    $em->remove($entityToDelete);
                    $rawNames[] = $pageToControl;
                }
            }
            switch($_POST['controls']){
                case 'publish':
                    foreach($entitiesToUpdate as $entityToControl){
                        $entityToControl->setPublished(true);
                        $em->persist($entityToControl);
                    }
                    break;
                case 'unpublish':
                    foreach($entitiesToUpdate as $entityToControl){
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
                        $pageController = preg_replace("#([\s\S]){5}\/\/".strtolower($rawName)."start([\s\S]*)\/\/".strtolower($rawName)."end([\s\S]){6}#", "", $pageController);
                        file_put_contents('../src/Controller/PageController.php', $pageController);
                    }

                    $cC->fullMigration($kernel);
                    break;
            }
            if($_POST['controls'] != 'delete'){
                $em->flush();
            }
        }

        $pages = [];
        $entitiesPage = [];
        $entities = scandir('../src/Entity');

        foreach($entities as $entity){
            if(preg_match_all('#^(Page)(.)*#', $entity)){
                $entity = substr($entity, 0, -4);
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

            return $this->redirectToRoute('fields_page', ['page' => $entity_name]);
        }else{
            return $this->redirectToRoute('admin_pages');
        }
    }

    /**
     * @Route("/admin/pages/fields/{page}", name="fields_page")
     */
    public function fields_page(Request $request, TaxonomyController $tC, ConsoleController $cC, KernelInterface $kernel, $page)
    {
        $taxonomyGroups = [];
        $reflection = new \ReflectionClass($tC);
        $taxos = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach($taxos as $taxo){
            $taxonomyGroups[] = $taxo->name;
        }

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
                if($_POST['pageRoute'] == '/'){
                    $routePath = '';
                }else{
                    $routePath = strtolower($_POST['pageRoute']);
                }                
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
            $formFile = preg_replace("#namespace App\\\Form;#", "namespace App\\\Form;\n\nuse Vich\\\UploaderBundle\\\Form\\\Type\\\VichFileType;\nuse App\\\Controller\\\TaxonomyController;\nuse Symfony\\\Component\\\Form\Extension\\\Core\\\Type\\\ChoiceType;", $formFile);

            $formFile = preg_replace("#        \\\$builder#", "        \$tC = new TaxonomyController();\n        \$builder", $formFile);

            $formFile = preg_replace_callback("#->add\('taxo(.*)'\)#", function($matches){
                return "->add('taxo".$matches[1]."', ChoiceType::class, [\n                'choices' => \$tC->".strtolower($matches[1]).",\n                'multiple' => true,\n                'choice_label'  => function(\$choice, \$key, \$value){\n                    return ucfirst(\$choice);\n                }\n            ])";
            }, $formFile);

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
            'taxoGroups' => $taxonomyGroups,
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
        $form->remove('published');
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

    /**
     * @Route("/admin/contenttypes", name="admin_contenttypes")
     */
    public function contenttypes()
    {
        $entitiesCT = [];
        $entities = scandir('../src/Entity');

        foreach($entities as $entity){
            if(preg_match_all('#^(CT)(.)*#', $entity)){
                $entity = substr($entity, 0, -4);
                $entitiesCT[] = $entity;
            }
        }

        return $this->render('admin/contenttypes.html.twig', [
            'ctE' => $entitiesCT,
        ]);
    }

    public function list_contenttypes(){
        $entitiesCT = [];
        $entities = scandir('../src/Entity');

        foreach($entities as $entity){
            if(preg_match_all('#^(CT)(.)*#', $entity)){
                $entity = substr($entity, 0, strlen($entity) - 4);
                $entitiesCT[] = $entity;
            }
        }

        return $this->render('admin/list_contenttypes.html.twig', [
            'cts' => $entitiesCT,
        ]);
    }

    /**
     * @Route("/admin/contenttypes/new", name="admin_new_contenttype")
     */
    public function new_contenttype(ConsoleController $cC, KernelInterface $kernel)
    {
        if(isset($_POST['entity_name']) && $_POST['entity_name'] != ''){
            $entity_name = 'CT'.ucfirst($_POST['entity_name']);
            $cC->createEntity($kernel, $entity_name);

            return $this->redirectToRoute('fields_contenttype', ['contenttype' => $entity_name]);
        }else{
            return $this->redirectToRoute('admin_contenttypes');
        }
    }

    /**
     * @Route("/admin/contenttypes/{ct}", name="admin_contenttype")
     */
    public function contenttype(Request $request, $ct)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('App\Entity\\'.$ct);

        if($request->isMethod('post')){
            $entitiesToUpdate = [];
            foreach($_POST['entries'] as $entry){
                $entitiesToUpdate[] = $repo->findOneById($entry);
            }
            switch($_POST['controls']){
                case 'publish':
                    foreach($entitiesToUpdate as $entityToControl){
                        $entityToControl->setPublished(true);
                        $em->persist($entityToControl);
                    }
                    break;
                case 'unpublish':
                    foreach($entitiesToUpdate as $entityToControl){
                        $entityToControl->setPublished(false);
                        $em->persist($entityToControl);
                    }
                    break;
                case 'delete':
                    foreach($entitiesToUpdate as $entityToControl){
                        $em->remove($entityToControl);
                    }
                    break;
            }
            $em->flush();
        }

        $entries = $repo->findAll();

        return $this->render('admin/contenttype.html.twig', [
            'ct' => $ct,
            'entries' => $entries,
        ]);
    }

    /**
     * @Route("/admin/contenttypes/delete/{ct}", name="admin_delete_contenttype")
     */
    public function delete_contenttype($ct, ConsoleController $cC, KernelInterface $kernel){
        $em = $this->getDoctrine()->getManager();

        $entitiesToDelete = $em->getRepository('App\Entity\\'.$ct)->findAll();

        foreach($entitiesToDelete as $entityToDelete){
            $em->remove($entityToDelete);
        }

        unlink('../src/Entity/'.$ct.'.php');
        unlink('../src/Form/'.$ct.'Type.php');
        unlink('../src/Repository/'.$ct.'Repository.php');
        unlink('../templates/theme/entries/'.strtolower($ct).'/entry.html.twig');
        unlink('../templates/theme/entries/'.strtolower($ct).'/listing.html.twig');
        rmdir('../templates/theme/entries/'.strtolower($ct));

        $cC->fullMigration($kernel);

        return $this->redirectToRoute('admin_contenttypes');
    }

    /**
     * @Route("/admin/contenttypes/fields/{contenttype}", name="fields_contenttype")
     */
    public function fields_contenttype(Request $request, TaxonomyController $tC, ConsoleController $cC, KernelInterface $kernel, $contenttype)
    {
        $taxonomyGroups = [];
        $reflection = new \ReflectionClass($tC);
        $taxos = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach($taxos as $taxo){
            $taxonomyGroups[] = $taxo->name;
        }

        $contenttypeName = $contenttype;

        if($request->isMethod('post')){
            file_put_contents('../src/Entity/'.$contenttypeName.'.php', $_POST['contenttypeArea']);

            $cC->regenerateEntity($kernel, $contenttypeName);
            $cC->createEntityForm($kernel, $contenttypeName);
            $cC->fullMigration($kernel);

            $template = file_get_contents('../templates/theme/entries/gcms_default.html.twig');
            $contenttypeFields = '';

            foreach($_POST['contenttypeFields'] as $field){
                $contenttypeFields .= "<section>{{ entry.".$field." }}</section>\n    ";
            }

            $template = preg_replace('#fieldsHere#', $contenttypeFields, $template);
            mkdir('../templates/theme/entries/'.strtolower($contenttypeName));
            file_put_contents('../templates/theme/entries/'.strtolower($contenttypeName).'/entry.html.twig', $template);

            file_put_contents('../templates/theme/entries/'.strtolower($contenttypeName).'/listing.html.twig', file_get_contents('../templates/theme/entries/list_default.html.twig'));

            $formFile = file_get_contents('../src/Form/'.$contenttypeName.'Type.php');
            $formFile = substr($formFile, 5);
            $formFile = preg_replace("#namespace App\\\Form;#", "namespace App\\\Form;\n\nuse Vich\\\UploaderBundle\\\Form\\\Type\\\VichFileType;\nuse App\\\Controller\\\TaxonomyController;\nuse Symfony\\\Component\\\Form\Extension\\\Core\\\Type\\\ChoiceType;", $formFile);

            $formFile = preg_replace("#        \\\$builder#", "        \$tC = new TaxonomyController();\n        \$builder", $formFile);

            $formFile = preg_replace_callback("#->add\('taxo(.*)'\)#", function($matches){
                return "->add('taxo".$matches[1]."', ChoiceType::class, [\n                'choices' => \$tC->".strtolower($matches[1]).",\n                'multiple' => true,\n                'choice_label'  => function(\$choice, \$key, \$value){\n                    return ucfirst(\$choice);\n                }\n            ])";
            }, $formFile);

            if(isset($_POST['imageFields'])){
                foreach($_POST['imageFields'] as $field){
                    $formFile = preg_replace("#->add\('".$field."'\)#", "->add('".$field."File', VichFileType::class)", $formFile);
                }
            }                

            $formFile = preg_replace("#'data_class' => ".$contenttypeName."::class,#", "'data_class' => ".$contenttypeName."::class,\n            'allow_extra_fields' => true", $formFile);
            file_put_contents('../src/Form/'.$contenttypeName.'Type.php', '<?php'.$formFile);

            return $this->redirectToRoute('admin_contenttype', [
                'ct' => $contenttypeName,
            ]);
        }

        $contenttype = file_get_contents('../src/Entity/'.$contenttypeName.'.php');

        return $this->render('admin/ct_fields.html.twig', [
            'ctName' => $contenttypeName,
            'ct' => $contenttype,
            'taxoGroups' => $taxonomyGroups,
        ]);
    }

    /**
     * @Route("/admin/{ct}/entries/new/{id}", name="admin_new_entry")
     */
    public function new_entry(Request $request, $ct, $id = null)
    {
        $classConst = 'App\Entity\\'.$ct;
        $formConst = 'App\Form\\'.$ct.'Type';
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($classConst);

        if($id === null){
            $entity = new $classConst();
            $entity->setPublished(true);
        }else{
            $entity = $repo->findOneById($id);
        }

        $form = $this->createForm($formConst, $entity);
        $form->add('Save', SubmitType::class);
        $form->remove('author');
        $form->remove('published');
        $form->remove('updatedAt');

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entity = $form->getData();
            $entity->setAuthor($this->getUser()->getUsername());
            $entity->setUpdatedAt(new \DateTime());
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('admin_contenttype', [
                'ct' => $ct,
            ]);
        }

        return $this->render('admin/form.html.twig', [
            'form' => $form->createView(),
        ]);
        
    }

}