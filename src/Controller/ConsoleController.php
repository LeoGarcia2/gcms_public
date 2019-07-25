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
    public function clearCache(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'cache:clear'
        ]);

        $output = new NullOutput();
        $application->run($input, $output);
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

    public function migrateDatabase(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'make:migration'
        ]);

        $output = new NullOutput();
        $application->run($input, $output);
    }
    
    public function migrateDatabase2(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
            '--no-interaction' => ''
        ]);

        $output = new NullOutput();
        $application->run($input, $output);
    }

    public function fullMigration(KernelInterface $kernel){
        $this->migrateDatabase($kernel);
        $this->migrateDatabase2($kernel);
    }

    public function createEntity(KernelInterface $kernel, $entity_name)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'make:entity',
            'name' => $entity_name,
            '--no-interaction' => ''
        ]);

        $output = new NullOutput();
        $application->run($input, $output);
    }

    public function regenerateEntity(KernelInterface $kernel, $entity_name)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'make:entity',
            'name' => 'App\Entity\\'.$entity_name,
            '--regenerate' => true,
            '--overwrite' => true,
        ]);

        $output = new NullOutput();
        $application->run($input, $output);
    }

    public function createEntityForm(KernelInterface $kernel, $entity_name)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'make:form',
            'name' => $entity_name,
            'bound-class' => $entity_name
        ]);

        $output = new NullOutput();
        $application->run($input, $output);
    }
}
