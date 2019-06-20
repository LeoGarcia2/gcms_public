<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class ConsoleController extends AbstractController
{
    /**
     * @Route("/console", name="console")
     */
    public function index()
    {
        return $this->render('console/index.html.twig', [
            'controller_name' => 'ConsoleController',
        ]);
    }
    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        return new Response('ok');
    }

    /**
     * @Route("/console/test", name="consoletest")
     */
    public function consoletest(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'make:controller',
            'controller-class' => 'testController'
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return new Response($content);
    }

    public function createDatabase(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:database:create'
        ]);

        $output = new NullOutput();
        $application->run($input, $output);
    }
}
