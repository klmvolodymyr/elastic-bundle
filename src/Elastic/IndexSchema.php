<?php

namespace VolodymyrKlymniuk\ElasticBundle\Elastic;

class IndexSchema
{
    /**
     * @var array
     */
    private $schema;

    /**
     * @param array $schema
     */
    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return array
     */
    public function getSchema(): array
    {
        return $this->schema;
    }
}