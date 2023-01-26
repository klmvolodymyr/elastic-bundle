<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\RelationLoader;

use VolodymyrKlymniuk\ElasticBundle\DocumentManager\Registry;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractRelationLoader
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }
}
