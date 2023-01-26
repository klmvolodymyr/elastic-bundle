<?php

namespace VolodymyrKlymniuk\ElasticBundle\Command;

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
