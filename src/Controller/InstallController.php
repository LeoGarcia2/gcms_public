<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Controller\ConsoleController;
use Symfony\Component\HttpKernel\KernelInterface;

class InstallController extends AbstractController
{
    /**
     * @Route("/install", name="install")
     */
    public function index()
    {
        if ($request->isMethod('POST')){
            //Database creation
            if(isset($_POST['dbUrl']) && $_POST['dbUrl'] != ''){
                $env = file_get_contents('../.env');
                $env = preg_replace('#DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name#', 'DATABASE_URL='.$_POST['dbUrl'], $env);
                file_put_contents('../.env', $env);
            }

            //Update site config
            if(isset($_POST['sitename']) && $_POST['sitename'] != '' && isset($_POST['slogan']) && $_POST['slogan'] != ''){
                $siteConfig = file_get_contents('./assets/site_config/site_config.yml');
                $siteConfig = preg_replace('#MyAwesomeWebsite#', $_POST['sitename'], $siteConfig);
                $siteConfig = preg_replace('#The best website ever!#', $_POST['slogan'], $siteConfig);
                file_put_contents('./assets/site_config/site_config.yml', $siteConfig);
            }

            if(isset($_FILES['logo']) && isset($_FILES['favicon'])){
                move_uploaded_file($_FILES['logo']['tmp_name'], './assets/site_config/images/logo.png');
                move_uploaded_file($_FILES['favicon']['tmp_name'], './assets/site_config/images/favicon.png');
            }

            return $this->redirectToRoute('update_db', ['stage' => '1'])
        }

        return $this->render('install/index.html.twig', [
            'controller_name' => 'InstallController',
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($request->isMethod('POST')){

            $entityManager = $this->getDoctrine()->getManager();
		    
        	//User creation
        	$user = new User();
        	$user->setUsername($_POST['username']);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $_POST['password']
                )
            );
            $entityManager->persist($user);

            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('app_register');
    }

    /**
     * @Route("/updateDb/{stage}", name="update_db")
     */
    public function updateDb(ConsoleController $cC, KernelInterface $kernel, $stage)
    {
        if($stage == '0'){
            $cC->createDatabase($kernel);
            return $this->redirectToRoute('update_db', ['stage' => '1'])
        }else{
            $cC->migrateDatabase($kernel);
            return $this->render('install/register.html.twig');
        }
    }
}
