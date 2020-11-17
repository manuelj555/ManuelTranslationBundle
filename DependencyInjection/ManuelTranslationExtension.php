<?php

namespace ManuelAguirre\Bundle\TranslationBundle\DependencyInjection;

use ManuelAguirre\Bundle\TranslationBundle\Doctrine\Listener\ChangeTableNameListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ManuelTranslationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('serializer.yml');
        $loader->load('sync_services.yml');

        if ($container->getParameter('kernel.environment') !== 'prod') {
            $loader->load('services_dev.yml');
        }

        $container->setParameter('manuel_translation.locales', $config['locales']);
        $container->setParameter(
            'manuel_translation.filename_template',
            rtrim($config['catalogues_path'], '/') . '/messages.%s.doctrine'
        );
        $container->setParameter('manuel_translation.translations_backup_dir', $config['backup_dir']);
        $container->setParameter('manuel_translation.filename_sync', $config['filename_sync']);

        $this->registerTranslatorResources($config, $container);

        if (!$config['use_database']) {
            $container->findDefinition('manuel_translation.translations_doctrine_loader')
                ->replaceArgument(
                    0,
                    new Reference("manuel_translation.translations_repository.backup")
                );
        }

        if ($config['tables_prefix']) {
            $container->findDefinition(ChangeTableNameListener::class)
                ->replaceArgument(0, $config['tables_prefix']);
        } else {
            $container->removeDefinition(ChangeTableNameListener::class);
        }
    }

    private function registerTranslatorResources($config, ContainerBuilder $container)
    {
        // Discover translation directories
        $filesPrefixs = array($container->getParameter('kernel.project_dir') . '/templates' => 'views');
        $extractDirs = array($container->getParameter('kernel.project_dir') . '/templates');

        if (is_dir($container->getParameter('kernel.project_dir') . '/translations')) {
            $translationFilesDirs = array($container->getParameter('kernel.project_dir') . '/translations');
        } else {
            $translationFilesDirs = array();
        }

        $overrideViewsPath = $container->getParameter('kernel.project_dir') . '/templates/bundles/%s/Resources/views';

        $bundles = $container->getParameter('kernel.bundles');

        foreach ($config['bundles'] as $bundle) {
            if (!isset($bundles[$bundle])) {
                throw new InvalidArgumentException(sprintf('Bundle "%s" Not exists or is not Enabled', $bundle));
            }

            $reflection = new \ReflectionClass($bundles[$bundle]);
            $extractDirs[] = dirname($reflection->getFileName());
            $filesPrefixs[dirname($reflection->getFileName())] = $bundle;

            if (is_dir($bundleDir = sprintf($overrideViewsPath, $bundle))) {
                $extractDirs[] = $bundleDir;
                $filesPrefixs[$bundleDir] = $bundle;
            }

            if (is_dir($d = $bundleDir . 'Resources/translations/')) {
                $translationFilesDirs[] = $d;
            }

            if (is_dir($d = sprintf($overrideViewsPath, $bundle))) {
                $extractDirs[] = $d;
                $filesPrefixs[$d] = $bundle;
            }
        }

        $container->setParameter('manuel_translation.extract_dirs', $extractDirs);
        $container->setParameter('manuel_translation.translations_files_dirs', $translationFilesDirs);
        $container->setParameter('manuel_translation.files_prefix', $filesPrefixs);

        if (!class_exists(Kernel::class)) {
            return;
        }

        if (Kernel::MAJOR_VERSION >= 4 || (Kernel::MAJOR_VERSION == 3 && Kernel::MINOR_VERSION >= 4)) {
            $container->setAlias(
                'ManuelAguirre\Bundle\TranslationBundle\Controller\ApiController',
                'manuel_translation.controller.api'
            )->setPublic(true);
        }
    }
}
