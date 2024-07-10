<?php

declare(strict_types=1);

namespace SoftWax\HealthCheck\DependencyInjection;

use SoftWax\HealthCheck\Collector\DbalConnectionCheckCollector;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('softwax_health_check');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('native_check_collectors')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('dbal_connection')
                            ->info(DbalConnectionCheckCollector::class)
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
