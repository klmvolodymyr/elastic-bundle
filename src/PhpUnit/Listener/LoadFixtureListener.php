<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\PhpUnit;

use VolodymyrKlymniuk\ElasticBundle\Fixture\FixtureLoader;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;

class LoadFixtureListener implements TestListener
{
    use TestListenerDefaultImplementation;

    /**
     * @var FixtureLoader
     */
    private $loader;

    /**
     * @var bool
     */
    private $wasCalled = false;

    /**
     * @param FixtureLoader $loader
     */
    public function __construct(FixtureLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param TestSuite $suite
     */
    public function startTestSuite(TestSuite $suite): void
    {
        if ($this->wasCalled) {
            return;
        }
        $this->wasCalled = true;

        $this->loader->load();
    }
}