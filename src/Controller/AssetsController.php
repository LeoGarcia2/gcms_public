<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AssetsController extends AbstractController
{
    /**
     * @Route("/assets", name="assets")
     */
    public function index()
    {
        return $this->redirectToRoute('admin_home');
    }
}
