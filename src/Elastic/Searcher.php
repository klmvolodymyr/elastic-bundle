<?php

namespace VolodymyrKlymniuk\ElasticBundle\Elastic;

use Elastica\Query;
use Elastica\Search;
use VolodymyrKlymniuk\ElasticBundle\Exception\NonUniqueResultException;
use VolodymyrKlymniuk\ElasticBundle\Exception\NoResultException;
use VolodymyrKlymniuk\ElasticBundle\LazyResult\LazyResult;
use VolodymyrKlymniuk\LazyResultLib\ResultTransformer\ResultTransformerInterface;

class Searcher
{
    const MAX_LIMIT = 10000;

    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var ResultTransformerInterface
     */
    private $resultTransformer;

    /**
     * @param Connection                 $conn
     * @param ResultTransformerInterface $resultTransformer
     */
    public function __construct(Connection $conn, ResultTransformerInterface $resultTransformer)
    {
        $this->conn = $conn;
        $this->resultTransformer = $resultTransformer;
    }

    /**
     * @return Search
     */
    public function createSearch(): Search
    {
        return (new Search($this->conn->getClient()))
            ->addIndex($this->conn->getIndex());
//            ->addIndex($this->conn->getIndex())
//            ->addType($this->conn->getType());
    }

    /**
     * @param Query\AbstractQuery $query
     *
     * @return LazyResult
     */
    public function search(Query\AbstractQuery $query): LazyResult
    {
        $search = $this
            ->createSearch()
            ->setQuery($query);

        $lazyResult = new LazyResult($search, $this->resultTransformer);
        $lazyResult->setQueryParam('size', self::MAX_LIMIT);

        return $lazyResult;
    }

    /**
     * @param string $id
     *
     * @return array
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findOne(string $id): array
    {
        return $this->find([$id])->getSingleResult();
    }

    /**
     * @param array $ids
     *
     * @return LazyResult
     */
    public function find(array $ids)
    {
        $query = new Query\BoolQuery();
        foreach ($ids as $id) {
            $query->addShould(new Query\MatchQuery('id', $id));
//            $query->addShould(new Query\Match('id', $id));
        }

        return $this->search($query);
    }
}