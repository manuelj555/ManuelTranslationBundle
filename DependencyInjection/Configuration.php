<?php

namespace ManuelAguirre\Bundle\TranslationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use function rtrim;
use function str_starts_with;

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
                ->scalarNode('backup_dir')->defaultValue('%kernel.project_dir%/translations/backup/')->end()
                ->scalarNode('catalogues_path')
                    ->defaultValue('%kernel.project_dir%/var/translations/')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(static fn($v) => rtrim($v, '/') . '/messages.%s.doctrine')
                    ->end()
                ->end()
                ->booleanNode('use_database')->defaultTrue()->end()
                ->scalarNode('tables_prefix')->end()
                ->scalarNode('security_role')
                    ->defaultValue("ROLE_SUPER_ADMIN")
                    ->validate()
                        ->ifTrue(static fn($v) => !str_starts_with($v, 'ROLE_'))
                        ->thenInvalid('Must be a valid string starting with "ROLE_XXX..."')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
