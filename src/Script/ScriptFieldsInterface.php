<?php

namespace VolodymyrKlymniuk\ElasticBundle\Script;

/**
 * Default query behaviour is to display all doc fields
 * When we use custom script fields, response fields show only script_fields
 * Just set empty array to return all doc fields or customize your request
 */
interface ScriptFieldsInterface
{
    const RESULT_KEY = 'fields';

    /**
     * @return array
     */
    public function getScripts(): array;
}