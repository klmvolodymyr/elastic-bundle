<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\Filter;

use Elastica\Query\BoolQuery;
use VolodymyrKlymniuk\StdLib\Helper\Helper;
use VolodymyrKlymniuk\StdLib\Object\EmptyInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractFilter
{
    /**
     * @param object $dto
     *
     * @return BoolQuery
     */
    public function buildQuery($dto): BoolQuery
    {
        $query = new BoolQuery();
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->getFieldRules() as $field => $callable) {
            $value = $accessor->getValue($dto, Helper::underscoreToCamelCase($field));
            if ($this->applyRule($value)) {
                $callable($query, $value);
            }
        }

        return $query;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function applyRule($value): bool
    {
        switch (true) {
            case $value instanceof EmptyInterface:
                return !$value->isEmpty();
            case is_array($value) || $value instanceof \Traversable:
                return !empty($value);
            default:
                return null !== $value;
        }
    }

    /**
     * @return \Closure[]
     */
    abstract protected function getFieldRules(): array;

    /**
     * @param string $str
     *
     * @return string
     */
    protected function escapeSpecialChars(string $str): string
    {
        $regex = '/[\\+\\-\\=\\&\\|\\!\\(\\)\\{\\}\\[\\]\\^\\\"\\~\\*\\<\\>\\?\\:\\\\\\/\\.\\@]/';

        return preg_replace($regex, addslashes('\\$0'), $str);
    }
}