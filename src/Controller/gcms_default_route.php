
    /**
     * @Route("/pagenamelowercase", name="pagenamelowercase")
     */
    public function pagenameuppercase()
    {
        $classConst = 'App\Entity\pagenameuppercase';
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository($classConst);
        $entity = $repo->findAll();
        $entity = $entity[0];

        return $this->render('theme/pages/pagenamelowercase.html.twig', [
            'page' => $entity,
        ]);
    }
    