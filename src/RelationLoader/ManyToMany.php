<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\RelationLoader;

use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;

class ManyToMany extends AbstractRelationLoader
{
    /**
     * @param iterable $list
     * @param string   $listPath
     * @param string   $propertyPath
     * @param string   $relationIndex
     * @param string   $destinationPath
     *
     * @throws \TypeError
     */
    public function load(iterable $list, string $listPath, string $propertyPath, string $relationIndex, string $destinationPath): void
    {
        if (0 === count($list)) {
            return;
        }
        $keys = $this->collectKeys($list, $listPath, $propertyPath);
        if (!empty($keys)) {
            $documents = $this->findDocuments($relationIndex, $keys);
            $this->populateList($list, $listPath, $propertyPath, $destinationPath, $documents);
        }
    }

    /**
     * @param iterable $list
     * @param string   $listPath
     * @param string   $propertyPath
     *
     * @return array
     */
    private function collectKeys(iterable $list, string $listPath, string $propertyPath): array
    {
        $keys = [];
        foreach ($list as $item) {
            try {
                $subList = $this->accessor->getValue($item, $listPath);
            } catch (UnexpectedTypeException $ex) {
                continue;
            }
            if (!is_array($subList)) {
                continue;
            }
            foreach ($subList as $subItem) {
                $key = $this->accessor->getValue($subItem, $propertyPath);
                $keys[$key] = null;
            }
        }

        return $keys;
    }

    /**
     * @param string $index
     * @param array  $keys
     *
     * @return array
     */
    private function findDocuments(string $index, array $keys): array
    {
        $documents = $this
            ->registry
            ->getManager($index)
            ->getSearcher()
            ->find(array_keys($keys));

        foreach ($documents as $item) {
            $keys[$item['id']] = $item;
        }

        return $keys;
    }

    /**
     * @param iterable $list
     * @param string   $listPath
     * @param string   $propertyPath
     * @param string   $destinationPath
     * @param array    $documents
     */
    private function populateList(iterable $list, string $listPath, string $propertyPath, string $destinationPath, array $documents): void
    {
        foreach ($list as $key => $item) {
            try {
                $subList = $this->accessor->getValue($item, $listPath);
            } catch (UnexpectedTypeException $ex) {
                continue;
            }
            if (!is_array($subList)) {
                continue;
            }

            foreach ($subList as &$subItem) {
                $id = $this->accessor->getValue($subItem, $propertyPath);
                if (array_key_exists($id, $documents) && null !== $documents[$id]) {
                    $this->accessor->setValue($subItem, $destinationPath, $documents[$id]);
                }
            }

            $this->accessor->setValue($item, $listPath, $subList);
            $list[$key] = $item;
        }
    }
}
