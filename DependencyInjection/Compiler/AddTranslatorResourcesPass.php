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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddTranslatorResourcesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('translator.default')
            OR $container->getParameter('kernel.environment') == 'prod'
        ) {
            return;
        }

        $translator = $container->findDefinition('translator.default');
        $resourceTemplate = $container->getParameter('manuel_translation.filename_template');

        foreach ($container->getParameter('manuel_translation.locales') as $locale) {
            $resource = sprintf($resourceTemplate, $locale);

            $translator->addMethodCall('addResource', [
                'doctrine',
                $resource,
                $locale,
            ]);
        }
    }
}
