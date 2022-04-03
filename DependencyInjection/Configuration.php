<?php

namespace Mechanic\CqrsKit\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cqrs_kit');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('dispatchers')
                    ->children()
                        ->scalarNode('command')->info('Command bus service name')->end()
                        ->scalarNode('query')->info('Query bus service name')->end()
                        ->scalarNode('event')->info('Event bus service name')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
