<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\Fixture;

interface FixtureInterface
{
    /**
     * @param Registry $registry
     */
    public function loadFixtures(Registry $registry): void;
}