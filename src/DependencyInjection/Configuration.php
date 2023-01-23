<?php

namespace VolodymyrKlymniuk\ElasticBundle\DependencyInjection;

use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('elastic');

        $rootNode
            ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('defaults')
                    ->isRequired()
                    ->children()
                        ->arrayNode('connection')
                            ->isRequired()
                            ->children()
                                ->scalarNode('host')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('port')
                                    ->cannotBeEmpty()
                                    ->defaultValue(9200)
                                ->end()
                                ->scalarNode('path')
                                    ->defaultValue(null)
                                ->end()
                            ->end()
                    ->end()
                    ->scalarNode('logger')
                        ->cannotBeEmpty()
                        ->defaultValue(LoggerInterface::class)
                    ->end()
                    ->arrayNode('schema')
                        ->isRequired()
                        ->children()
                            ->scalarNode('dir')
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('options')
                        ->children()
                            ->scalarNode('refresh')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('indecies')
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('document_manager')
                            ->cannotBeEmpty()
                            ->defaultValue(DocumentManager::class)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}