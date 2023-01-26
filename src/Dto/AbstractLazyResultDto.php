<?php

namespace VolodymyrKlymniuk\ElasticBundle\Dto;

use VolodymyrKlymniuk\LazyResultLib\Dto\FilterableInterface;
use VolodymyrKlymniuk\LazyResultLib\Dto\PaginatableInterface;
use VolodymyrKlymniuk\LazyResultLib\Dto\PaginatableTrait;
use VolodymyrKlymniuk\LazyResultLib\Dto\SortableInterface;
use VolodymyrKlymniuk\LazyResultLib\Dto\SortableTrait;

abstract class AbstractLazyResultDto
{
    use SortableTrait, PaginatableTrait;
}