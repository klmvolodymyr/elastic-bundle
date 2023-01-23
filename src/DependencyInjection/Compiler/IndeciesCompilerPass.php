<?php

namespace VolodymyrKlymniuk\ElasticBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class IndeciesCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter(ElasticExtension::CONFIG_KEY);
        $registry = $container->getDefinition(Registry::class);

        foreach ($config['indecies'] as $index => $params) {
            $params = array_merge_recursive($params, $config['defaults']);

            $conn = (new Definition(Connection::class))
                ->addArgument($params['connection'])
                ->addArgument(new Reference($params['logger']))
                ->addArgument($index);

            $schemaPath = $config['defaults']['schema']['dir'] . str_replace('.', '/', $index) . '.json';
            $schema = (new Definition(IndexSchema::class))
                ->addArgument(json_decode(file_get_contents($schemaPath), true));

            $manager = (new Definition($params['document_manager']))
                ->addArgument($conn)
                ->addArgument($schema)
                ->addArgument($config['defaults']['options'] ?? []);

            $registry->addMethodCall('addManager', [$index, $manager]);
        }
    }
}