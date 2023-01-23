<?php

namespace VolodymyrKlymniuk\ElasticBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSchemaCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('elastic:schema:create');
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(): void
    {
        foreach ($this->registry->getAll() as $manager) {
            $manager->createSchema();
        }
    }
}
