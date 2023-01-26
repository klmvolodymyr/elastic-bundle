<?php

namespace VolodymyrKlymniuk\ElasticBundle\Elastic;

use Elastica\Client;
use Elastica\Index;
//use Elastica\Type;
use Psr\Log\LoggerInterface;

class Connection
{
    const TYPE = 'doc';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $index;

    /**
     *
     * @param array           $config
     * @param LoggerInterface $logger
     * @param string          $index
     */
    public function __construct(array $config, LoggerInterface $logger, string $index)
    {
        $this->client = new Client($config);
        $this->client->setLogger($logger);

        $this->index = $index;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getIndex(): Index
    {
        return $this->client->getIndex($this->index);
    }

    public function getType(): string
    {
        return self::TYPE;
//        return $this->getIndex()->getType(self::TYPE);
    }
}