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
        $treeBuilder = new TreeBuilder('manuel_translation');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('locales')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('bundles')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('backup_dir')->defaultValue('%kernel.project_dir%/translations/backup/')->end()
                ->scalarNode('filename_sync')->defaultValue('%kernel.project_dir%/translations/translations.txt')->end()
                //->scalarNode('catalogues_path')->defaultValue('%kernel.root_dir%/Resources/translations/')->end()
                ->scalarNode('catalogues_path')->defaultValue('%kernel.project_dir%/var/translations/')->end()
                ->booleanNode('use_database')->defaultTrue()->end()
            ->end();

        return $treeBuilder;
    }
}
