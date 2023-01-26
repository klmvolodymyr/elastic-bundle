<?php

namespace VolodymyrKlymniuk\ElasticBundle\DependencyInjection\Compiler;

use VolodymyrKlymniuk\ElasticBundle\Fixture\FixtureLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FixturesCompilerPass implements CompilerPassInterface
{
    const FIXTURE_TAG = 'elastic.fixture';

    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(FixtureLoader::class);
        $taggedServices = $container->findTaggedServiceIds(self::FIXTURE_TAG);

        foreach ($taggedServices as $serviceId => $tags) {
            $definition->addMethodCall('addFixture', [new Reference($serviceId)]);
        }
    }
}