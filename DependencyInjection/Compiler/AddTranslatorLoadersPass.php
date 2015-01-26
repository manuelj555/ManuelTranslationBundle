<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AddTranslatorLoadersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('translator.default')
            OR $container->getParameter('kernel.environment') == 'prod'
        ) {
            return;
        }

        $loaders = array();
        foreach ($container->findTaggedServiceIds('translation.loader') as $id => $attributes) {
            if ($attributes[0]['alias'] !== 'doctrine') {
                $loaders[$id][] = $attributes[0]['alias'];
                if (isset($attributes[0]['legacy-alias']) and $attributes[0]['legacy-alias'] !== 'doctrine') {
                    $loaders[$id][] = $attributes[0]['legacy-alias'];
                }
            }
        }

        if ($container->hasDefinition('manuel_translation.translation_loader')) {
            $definition = $container->getDefinition('manuel_translation.translation_loader');
            foreach ($loaders as $id => $formats) {
                foreach ($formats as $format) {
                    $definition->addMethodCall('addLoader', array($format, new Reference($id)));
                }
            }
        }
    }
}
