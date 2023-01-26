<?php
declare(strict_types=1);

namespace VolodymyrKlymniuk\ElasticBundle\ResultTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ElasticResultTransformer implements ResultTransformerInterface
{
    /**
     * @param Document[]|iterable $result
     *
     * @return Collection
     */
    public function transform(iterable $result): Collection
    {
        $collection = new ArrayCollection();
        foreach ($result as $document) {
            $fields = [];

            if ($document->hasParam(ScriptFieldsInterface::RESULT_KEY)) {
                //Add custom script fields to response
                $fields = $document->getParam(ScriptFieldsInterface::RESULT_KEY);
            }

            $collection[] = array_merge($document->getData(), $fields);
        }

        return $collection;
    }
}
