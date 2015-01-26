<?php

namespace ManuelAguirre\Bundle\TranslationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
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

        $container->setParameter('manuel_translation.locales', $locales = array('en', 'es', 'pt'));
        $container->setParameter('manuel_translation.filename_template',
            $container->getParameter('kernel.root_dir') . '/Resources/translations/messages.%s.doctrine');

        $this->registerTranslatorResources($container, $locales);
    }

    private function registerTranslatorResources(ContainerBuilder $container, $locales)
    {
        // Discover translation directories
        $dirs = array();
//        if (class_exists('Symfony\Component\Validator\Validator')) {
//            $r = new \ReflectionClass('Symfony\Component\Validator\Validator');
//
//            $dirs[] = dirname($r->getFilename()) . '/Resources/translations';
//        }
//        if (class_exists('Symfony\Component\Form\Form')) {
//            $r = new \ReflectionClass('Symfony\Component\Form\Form');
//
//            $dirs[] = dirname($r->getFilename()) . '/Resources/translations';
//        }
//        if (class_exists('Symfony\Component\Security\Core\Exception\AuthenticationException')) {
//            $r = new \ReflectionClass('Symfony\Component\Security\Core\Exception\AuthenticationException');
//
//            $dirs[] = dirname($r->getFilename()) . '/../Resources/translations';
//        }
        $overridePath = $container->getParameter('kernel.root_dir') . '/Resources/%s/translations';
//        foreach ($container->getParameter('kernel.bundles') as $bundle => $class) {
//            $reflection = new \ReflectionClass($class);
//            if (is_dir($dir = dirname($reflection->getFilename()) . '/Resources/translations')) {
//                $dirs[] = $dir;
//            }
//            if (is_dir($dir = sprintf($overridePath, $bundle))) {
//                $dirs[] = $dir;
//            }
//        }
        if (is_dir($dir = $container->getParameter('kernel.root_dir') . '/Resources/translations')) {
            $dirs[] = $dir;
        }

//        echo "<pre>";var_dump($dirs);die;

        $container->setParameter('manuel_translation.resources_dirs', $dirs);
    }
}
