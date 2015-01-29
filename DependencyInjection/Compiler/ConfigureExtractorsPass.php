<?php


namespace ManuelAguirre\Bundle\TranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigureExtractorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('kernel.environment') == 'prod') {
            return;
        }

        if ($container->hasDefinition('twig.translation.extractor')) {
            $container->getDefinition('twig.translation.extractor')
                ->setClass('ManuelAguirre\Bundle\TranslationBundle\Translation\Extractor\TwigExtractor');
        }

        if ($container->hasDefinition('translation.extractor.php')) {
            $container->getDefinition('translation.extractor.php')
                ->setClass('ManuelAguirre\Bundle\TranslationBundle\Translation\Extractor\PhpExtractor');
        }
    }
}
