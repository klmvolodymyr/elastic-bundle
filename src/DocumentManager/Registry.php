<?php

namespace VolodymyrKlymniuk\ElasticBundle\DocumentManager;

class Registry
{
    /**
     * @var DocumentManager[]
     */
    private $managers = [];

    /**
     * @param string $name
     *
     * @return DocumentManager
     */
    public function getManager(string $name): DocumentManager
    {
        if (!array_key_exists($name, $this->managers)) {
            throw new NotFoundException(sprintf('Document manager: "%s" was not found', $name));
        }

        return $this->managers[$name];
    }

    /**
     * @param string          $name
     * @param DocumentManager $manager
     *
     * @return Registry
     */
    public function addManager(string $name, DocumentManager $manager): Registry
    {
        $this->managers[$name] = $manager;

        return $this;
    }

    /**
     * @return DocumentManager[]
     */
    public function getAll(): array
    {
        return $this->managers;
    }
}