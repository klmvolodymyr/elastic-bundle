<?php

namespace VolodymyrKlymniuk\ElasticBundle\Command;

use VolodymyrKlymniuk\ElasticBundle\Fixture\FixtureLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadFixturesCommand extends Command
{
    /**
     * @var FixtureLoader
     */
    protected $loader;

    /**
     * @param null|string   $name
     * @param FixtureLoader $loader
     */
    public function __construct(?string $name = null, FixtureLoader $loader)
    {
        parent::__construct($name);
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('elastic:fixture:load')
            ->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'Delay after command execution');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->loader->load();
            if ($delay = $input->getOption('delay')) {
                sleep($delay);
            }
            $output->writeln('<info>Done</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}