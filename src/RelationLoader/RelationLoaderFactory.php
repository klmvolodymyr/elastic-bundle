<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\RelationLoader;

use VolodymyrKlymniuk\ElasticBundle\DocumentManager\Registry;

class RelationLoaderFactory
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return OneToMany
     */
    public function oneToMany(): OneToMany
    {
        return new OneToMany($this->registry);
    }

    /**
     * @return ManyToOne
     */
    public function manyToOne(): ManyToOne
    {
        return new ManyToOne($this->registry);
    }

    /**
     * @return ManyToMany
     */
    public function manyToMany(): ManyToMany
    {
        return new ManyToMany($this->registry);
    }
}