<?php

namespace VolodymyrKlymniuk\ElasticBundle\Command;

class ClearIndexCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('elastic:index:clear');
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(): void
    {
        foreach ($this->registry->getAll() as $manager) {
            $manager->clear();
        }
    }
}
