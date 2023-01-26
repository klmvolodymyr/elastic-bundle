<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\RelationLoader;

use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;

class ManyToOne extends AbstractRelationLoader
{
    /**
     * @var string
     */
    private $destinationPath;

    /**
     * @param string $destinationPath
     *
     * @return static
     */
    public function setDestinationPath(string $destinationPath): self
    {
        $this->destinationPath = $destinationPath;

        return $this;
    }

    /**
     * @param iterable $list
     * @param string   $foreignKeyPath
     * @param string   $relationIndex
     *
     * @throws \TypeError
     */
    public function load(iterable $list, string $foreignKeyPath, string $relationIndex): void
    {
        if (null === $this->destinationPath) {
            $this->destinationPath = $foreignKeyPath;
        }
        $keys = $this->collectKeys($list, $foreignKeyPath);
        if (!empty($keys)) {
            $documents = $this->findDocuments($relationIndex, $keys);
            $this->populateList($list, $foreignKeyPath, $documents);
        }
    }

    /**
     * @param iterable $list
     * @param string   $foreignKeyPath
     *
     * @return array
     */
    private function collectKeys(iterable $list, string $foreignKeyPath): array
    {
        $keys = [];
        foreach ($list as $item) {
            try {
                $foreignKeys = $this->accessor->getValue($item, $foreignKeyPath);
            } catch (UnexpectedTypeException $ex) {
                continue;
            }
            if (!is_array($foreignKeys)) {
                $foreignKeys = [$foreignKeys];
            }
            foreach ($foreignKeys as $key) {
                if (is_int($key) || is_string($key)) {
                    $keys[$key] = null;
                }
            }
        }

        return $keys;
    }

    /**
     * @param string $relationIndex
     * @param array  $keys
     *
     * @return array
     */
    private function findDocuments(string $relationIndex, array $keys): array
    {
        $documents = $this
            ->registry
            ->getManager($relationIndex)
            ->getSearcher()
            ->find(array_keys($keys));

        foreach ($documents as $item) {
            $keys[$item['id']] = $item;
        }

        return $keys;
    }

    /**
     * @param iterable $list
     * @param string   $foreignKeyPath
     * @param array    $documents
     */
    private function populateList(iterable $list, string $foreignKeyPath, array $documents): void
    {
        foreach ($list as $key => $item) {
            try {
                $foreignKeys = $this->accessor->getValue($item, $foreignKeyPath);
            } catch (UnexpectedTypeException $ex) {
                continue;
            }
            if (is_array($foreignKeys)) {
                $array = [];
                foreach ($foreignKeys as $key) {
                    if (array_key_exists($key, $documents) && null !== $documents[$key]) {
                        $array[] = $documents[$key];
                    }
                }
                $this->accessor->setValue($item, $this->destinationPath, $array);
            } else {
                if (array_key_exists($foreignKeys, $documents)) {
                    $this->accessor->setValue($item, $this->destinationPath, $documents[$foreignKeys]);
                }
            }
            $list[$key] = $item;
        }
    }
}
