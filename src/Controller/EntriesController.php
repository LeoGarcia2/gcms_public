<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EntriesController extends AbstractController
{
    /**
     * @Route("/{contentType}/{id}", name="entry", requirements={"id"="\d+"})
     */
    public function entry(Request $request, $contentType, $id)
    {
        return new Response($contentType." ".$id);
    }
}
