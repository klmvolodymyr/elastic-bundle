<?php

namespace VolodymyrKlymniuk\ElasticBundle;

interface SourceInterface
{
    /**
     * Response fields list
     *
     * @return array
     */
    public function getSource(): array;
}