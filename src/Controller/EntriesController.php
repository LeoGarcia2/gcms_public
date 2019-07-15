<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;

class EntriesController extends AbstractController
{
    /**
     * @Route("/{ctWithoutCT}", name="listing")
     */
    public function listing(Request $request, $ctWithoutCT, PaginatorInterface $paginator)
    {
        $ct = 'CT'.$ctWithoutCT;
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('App\Entity\\'.$ct);

        $allEntriesQuery = $repo->createQueryBuilder('e')
            ->where('e.published == :published')
            ->setParameter('published', true)
            ->getQuery();

        $entries = $paginator->paginate(
            $allEntriesQuery,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('theme/entries/'.strtolower($ct).'/listing.html.twig', [
            'ct' => $ct,
            'ctWithoutCT' => $ctWithoutCT,
            'entries' => $entries,
        ]);
    }

    /**
     * @Route("/{ctWithoutCT}/{id}", name="entry", requirements={"id"="\d+"})
     */
    public function entry(Request $request, $ctWithoutCT, $id)
    {
        $ct = 'CT'.$ctWithoutCT;
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('App\Entity\\'.$ct);
        $entry = $repo->findOneById($id);

        return $this->render('theme/entries/'.strtolower($ct).'/entry.html.twig', [
            'ct' => $ct,
            'entry' => $entry,
        ]);
    }
}
