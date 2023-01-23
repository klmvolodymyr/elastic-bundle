<?php

namespace VolodymyrKlymniuk\ElasticBundle\DocumentManager;

class DocumentManager
{
    const RETRY_ON_CONFLICT = 3;

    /**
     * @var Connection
     */
    protected $conn;

    /**
     * @var IndexSchema
     */
    protected $schema;

    /**
     * @var array
     */
    protected $defaultOptions;

    /**
     * @var Searcher
     */
    protected $searcher;

    /**
     * @param Connection  $conn
     * @param IndexSchema $schema
     * @param array       $defaultOptions
     */
    public function __construct(Connection $conn, IndexSchema $schema, array $defaultOptions)
    {
        $this->conn = $conn;
        $this->schema = $schema;
        $this->defaultOptions = $defaultOptions;
        $this->searcher = new Searcher($conn, $this->getResultTransformer());
    }

    /**
     * @return ResultTransformerInterface
     */
    public function getResultTransformer(): ResultTransformerInterface
    {
        return new ElasticResultTransformer();
    }

    /**
     * @return Searcher
     */
    public function getSearcher(): Searcher
    {
        return $this->searcher;
    }

    /**
     * Creating schema for type
     */
    public function createSchema(): void
    {
        if (false === $this->conn->getIndex()->exists()) {
            $this->conn->getIndex()->create($this->schema->getSchema());
        }
    }

    /**
     * @param array $data
     */
    public function create(array $data): void
    {
        $this
            ->conn
            ->getType()
            ->addDocument($this->createDocument($data));
    }

    /**
     * @param array $data
     * @param array $options
     */
    public function update(array $data, array $options = []): void
    {
        $this
            ->conn
            ->getType()
            ->updateDocument($this->createDocument($data), $this->enrichOptions($options));
    }

    /**
     * @param array $data
     * @param array $options
     */
    public function upsert(array $data, array $options = []): void
    {
        $doc = $this
            ->createDocument($data)
            ->setDocAsUpsert(true)
            ->setRetryOnConflict(self::RETRY_ON_CONFLICT);

        $this
            ->conn
            ->getType()
            ->updateDocument($doc, $this->enrichOptions($options));
    }

    /**
     * @param array $data
     * @param array $options
     */
    public function put(array $data, array $options = []): void
    {
        $doc = $this->createDocument($data);

        $endpoint = (new Index())
            ->setID($doc->getId())
            ->setBody($doc->getData())
            ->setParams($this->enrichOptions($options));

        $this
            ->conn
            ->getType()
            ->requestEndpoint($endpoint);
    }

    /**
     * @param string $id
     * @param array  $options
     */
    public function deleteById(string $id, array $options = []): void
    {
        $this
            ->conn
            ->getType()
            ->deleteById($id, $this->enrichOptions($options));
    }

    /**
     * @param array $data
     *
     * @return Document
     */
    protected function createDocument(array $data): Document
    {
        $usefulData = $this->arrayIntersectKeyRecursive($data, $this->schema->getSchema()['mappings']['doc']['properties']);
        if (empty($usefulData['id'])) {
            throw new \DomainException(sprintf('"%s" expects data will contains id', get_class($this)));
        }
        ksort($usefulData);

        return new Document($usefulData['id'], $usefulData);
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    private function arrayIntersectKeyRecursive(array $array1, array $array2): array
    {
        $array1 = array_intersect_key($array1, $array2);
        foreach ($array1 as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if (is_array($value) && array_key_exists('properties', $array2[$key])) {
                if (isset($array2[$key]['type']) && 'nested' === $array2[$key]['type']) {
                    $array1[$key] = [];
                    foreach ($value as $i => $item) {
                        $array1[$key][$i] = $this->arrayIntersectKeyRecursive($item, $array2[$key]['properties']);
                    }
                } else {
                    $array1[$key] = $this->arrayIntersectKeyRecursive($value, $array2[$key]['properties']);
                }

            }
        }

        return $array1;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function enrichOptions(array $options): array
    {
        return array_merge($this->defaultOptions, $options);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->conn->getType()->deleteByQuery(new MatchAll());
    }
}