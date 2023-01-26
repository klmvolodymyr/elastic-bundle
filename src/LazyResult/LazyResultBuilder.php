<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\LazyResult;

use VolodymyrKlymniuk\ElasticBundle\DocumentManager\DocumentManager;
use VolodymyrKlymniuk\ElasticBundle\Filter\AbstractFilter;
use VolodymyrKlymniuk\ElasticBundle\ResultTransformer\ElasticResultTransformer;
use VolodymyrKlymniuk\ElasticBundle\Script\ScriptFieldsInterface;
use VolodymyrKlymniuk\ElasticBundle\Source\SourceInterface;
use VolodymyrKlymniuk\LazyResultLib\Dto\FilterableInterface;
use VolodymyrKlymniuk\LazyResultLib\Dto\PaginatableInterface;
use VolodymyrKlymniuk\LazyResultLib\Dto\SortableInterface;
use VolodymyrKlymniuk\LazyResultLib\LazyResultBuilder\AbstractLazyResultBuilder;
use VolodymyrKlymniuk\LazyResultLib\ResultTransformer\ResultTransformerInterface;

/**
 * @property LazyResult $lazyResult
 */
class LazyResultBuilder extends AbstractLazyResultBuilder
{
    /**
     * @var DocumentManager
     */
    private $manager;

    /**
     * @var AbstractFilter
     */
    private $filter;

    /**
     * @param DocumentManager            $manager
     * @param ResultTransformerInterface $rt
     */
    public function __construct(DocumentManager $manager, ResultTransformerInterface $rt = null)
    {
        $this->manager = $manager;

        $rt = null === $rt ? $manager->getResultTransformer() : $rt;
        $this->lazyResult = new LazyResult($manager->getSearcher()->createSearch(), $rt);
    }

    /**
     * @param AbstractFilter $filter
     *
     * @return self
     */
    public function setFilter(AbstractFilter $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @param object $dto
     *
     * @return LazyResult
     */
    public function build($dto)
    {
        parent::build($dto);

        if ($dto instanceof ScriptFieldsInterface) {
            $this->addScriptFields($dto);
        }

        if ($dto instanceof SourceInterface) {
            $this->addSource($dto);
        }

        return $this->lazyResult;
    }

    /**
     * @param FilterableInterface $dto
     *
     * @return self
     */
    public function filter(FilterableInterface $dto)
    {
        $search = $this->lazyResult->getSearch();

        $query = $this
            ->filter
            ->buildQuery($dto);

        $search->setQuery($query);

        return $this;
    }

    /**
     * @param SortableInterface $dto
     *
     * @return self
     */
    public function sort(SortableInterface $dto)
    {
        $this->lazyResult->setQueryParam('sort', $dto->getSort());

        return $this;
    }

    /**
     * @param PaginatableInterface $dto
     *
     * @return self
     */
    public function paginate(PaginatableInterface $dto)
    {
        $this->lazyResult->setQueryParam('from', $dto->getOffset());
        $this->lazyResult->setQueryParam('size', $dto->getLimit());

        return $this;
    }

    /**
     * @param ScriptFieldsInterface $dto
     *
     * @return self
     */
    public function addScriptFields(ScriptFieldsInterface $dto)
    {
        $this->lazyResult->setQueryParam('script_fields', $dto->getScripts());

        return $this;
    }

    /**
     * @param SourceInterface $dto
     *
     * @return self
     */
    public function addSource(SourceInterface $dto)
    {
        $this->lazyResult->setQueryParam('_source', $dto->getSource());

        return $this;
    }
}