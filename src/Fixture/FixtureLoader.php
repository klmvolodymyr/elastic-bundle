<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\Fixture;

class FixtureLoader
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var FixtureInterface[]
     */
    private $fixtures = [];

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param FixtureInterface $fixture
     *
     * @return self
     */
    public function addFixture(FixtureInterface $fixture): FixtureLoader
    {
        $this->fixtures[] = $fixture;

        return $this;
    }

    /**
     * @return void
     */
    public function load(): void
    {
        foreach ($this->fixtures as $fixture) {
            $fixture->loadFixtures($this->registry);
        }
    }
}