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

class InstallController extends AbstractController
{
    /**
     * @Route("/install", name="install")
     */
    public function index()
    {
        return $this->render('install/index.html.twig', [
            'controller_name' => 'InstallController',
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ConsoleController $cC): Response
    {
        if ($request->isMethod('POST')){

            $entityManager = $this->getDoctrine()->getManager();

        	//Database creation
            if(isset($_POST['dbUrl']) && $_POST['dbUrl'] != ''){
            	$env = file_get_contents('../.env');
		        $env = preg_replace('#DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name#', 'DATABASE_URL='.$_POST['dbUrl'], $env);
		        file_put_contents('../.env', $env);
            }
            $cC->createDatabase();
		    
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

        return $this->redirectToRoute('install');
    }
}
