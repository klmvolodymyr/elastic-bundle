<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\RelationLoader;

use Elastica\Query;

class OneToMany extends AbstractRelationLoader
{
    /**
     * @var string
     */
    private $primaryKeyPath = '[id]';

    /**
     * @param string $primaryKeyPath
     *
     * @return static
     */
    public function setPrimaryKeyPath(string $primaryKeyPath): self
    {
        $this->primaryKeyPath = $primaryKeyPath;

        return $this;
    }

    /**
     * @param iterable $list
     * @param string   $destinationPath
     * @param string   $relationIndex
     * @param string   $foreignField
     */
    public function load(iterable $list, string $destinationPath, string $relationIndex, string $foreignField): void
    {
        if (0 === count($list)) {
            return;
        }
        $keys = $this->collectKeys($list);
        if (!empty($keys)) {
            $result = $this->findDocuments($relationIndex, $foreignField, $keys);
            $this->populateList($list, $destinationPath, $result);
        }
    }

    /**
     * @param iterable $list
     *
     * @return array
     */
    private function collectKeys(iterable $list): array
    {
        $keys = [];
        foreach ($list as $item) {
            $primaryKey =  $this->accessor->getValue($item, $this->primaryKeyPath);
            $keys[$primaryKey] = [];
        }

        return $keys;
    }

    /**
     * @param string $relationIndex
     * @param string $foreignField
     * @param array  $keys
     *
     * @return array
     */
    private function findDocuments(string $relationIndex, string $foreignField, array $keys): array
    {
        $query = new Query\BoolQuery();
        foreach (array_keys($keys) as $key) {
            $query->addShould(new Query\MatchQuery($foreignField, $key));
            // $query->addShould(new Query\Match($foreignField, $key));
        }

        $result = $this
            ->registry
            ->getManager($relationIndex)
            ->getSearcher()
            ->search($query);

        foreach ($result as $item) {
            $keys[$item[$foreignField]][] = $item;
        }

        return $keys;
    }

    /**
     * @param iterable $list
     * @param string   $destinationPath
     * @param array    $foreignDocuments
     */
    private function populateList(iterable $list, string $destinationPath, array $foreignDocuments): void
    {
        foreach ($list as $key => $item) {
            $primaryKey =  $this->accessor->getValue($item, $this->primaryKeyPath);
            $this->accessor->setValue($item, $destinationPath, $foreignDocuments[$primaryKey]);
            $list[$key] = $item;
        }
    }
}
