<?php

namespace ManuelAguirre\Bundle\TranslationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('manuel_translation');

        $rootNode
            ->children()
                ->arrayNode('bundles')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('backup_dir')->defaultValue('%kernel.root_dir%/trans-backup/')->end()
                ->scalarNode('api_key')
                    ->info('Key used for the client for communications with server')
                ->end()
                ->arrayNode('client')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('api_key')
                            ->info('Key used for the client for communications with server')
                        ->end()
                        ->scalarNode('server_url')
                            ->info('Url of Server')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('server')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('api_key')
                            ->info('Key used for the server for communications')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
