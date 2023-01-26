<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\LazyResult;

use Elastica\ResultSet;
use Elastica\Search;
use VolodymyrKlymniuk\ElasticBundle\Exception\NonUniqueResultException;
use VolodymyrKlymniuk\ElasticBundle\Exception\NoResultException;
use VolodymyrKlymniuk\LazyResultLib\AbstractLazyResult;
use VolodymyrKlymniuk\LazyResultLib\ResultTransformer\ResultTransformerInterface;

class LazyResult extends AbstractLazyResult
{
    /**
     * @var Search
     */
    private $search;

    /**
     * @param Search                     $search
     * @param ResultTransformerInterface $resultTransformer
     */
    public function __construct(Search $search, ResultTransformerInterface $resultTransformer)
    {
        $this->search = $search;
        parent::__construct($resultTransformer);
    }

    /**
     * @return Search
     */
    public function getSearch(): Search
    {
        return $this->search;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function setQueryParam(string $key, $value): self
    {
        $this->search->getQuery()->setParam($key, $value);

        return $this;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        $this->initialize();

        return $this->response->getTotalHits();
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        $this->initialize();

        return $this->response->getTotalTime();
    }

    /**
     * @return array
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getSingleResult(): array
    {
        switch ($this->count()) {
            case 1:
                return $this->toArray()[0];
            case 0:
                throw new NoResultException('Document was not found');
            default:
                throw new NonUniqueResultException('It was found more then one result');
        }
    }

    /**
     * @return ResultSet
     */
    protected function getResponse(): ResultSet
    {
        return $this->search->search();
    }
}
