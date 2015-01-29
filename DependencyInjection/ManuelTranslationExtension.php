<?php

namespace ManuelAguirre\Bundle\TranslationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

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

        if ($container->getParameter('kernel.environment') !== 'prod') {
            $loader->load('services_dev.yml');
        }

        if ($this->isConfigEnabled($container, $config['client'])) {
            $loader->load('sync_services.yml');
        }

        $container->setParameter('manuel_translation.locales', $locales = array('en', 'es', 'pt'));
        $container->setParameter('manuel_translation.filename_template',
            $container->getParameter('kernel.root_dir') . '/Resources/translations/messages.%s.doctrine');
        $container->setParameter('manuel_translation.translations_backup_dir', $config['backup_dir']);

        $this->registerTranslatorResources($config, $container);
        $this->registerClientConfig($config['client'], $container);
        $this->registerServerConfig($config['server'], $container);
    }

    private function registerServerConfig($config, ContainerBuilder $container)
    {
        if (!$this->isConfigEnabled($container, $config)) {
            return;
        }

        $container->setParameter('manuel_translation.server.api_key', $config['api_key']);
    }

    private function registerClientConfig($config, ContainerBuilder $container)
    {
        if (!$this->isConfigEnabled($container, $config)) {
            return;
        }

        $container->setParameter('manuel_translation.client.api_key', $config['api_key']);
        $container->setParameter('manuel_translation.client.server_url', $config['server_url']);
    }

    private function registerTranslatorResources($config, ContainerBuilder $container)
    {
        // Discover translation directories
        $filesPrefixs = array($container->getParameter('kernel.root_dir') . '/Resources/views' => 'views');
        $extractDirs = array($container->getParameter('kernel.root_dir') . '/Resources/views');
        $translationFilesDirs = array($container->getParameter('kernel.root_dir') . '/Resources/translations');

        $overrideViewsPath = $container->getParameter('kernel.root_dir') . '/Resources/%s/views';
        $overrideTransPath = $container->getParameter('kernel.root_dir') . '/Resources/%s/translations';

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

            if (is_dir($d = sprintf($overrideTransPath, $bundle))) {
                $translationFilesDirs[] = $d;
            }
        }

        $container->setParameter('manuel_translation.extract_dirs', $extractDirs);
        $container->setParameter('manuel_translation.translations_files_dirs', $translationFilesDirs);
        $container->setParameter('manuel_translation.files_prefix', $filesPrefixs);
    }
}
