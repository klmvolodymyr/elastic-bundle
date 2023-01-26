<?php

namespace VolodymyrKlymniuk\ElasticBundle\Command;

use VolodymyrKlymniuk\ElasticBundle\DocumentManager\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param null|string $name
     * @param Registry    $registry
     */
    public function __construct(?string $name = null, Registry $registry)
    {
        parent::__construct($name);
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->doExecute();
            $output->writeln('<info>Done</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

    /**
     * @return void
     */
    abstract protected function doExecute(): void;
}