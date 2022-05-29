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

use ManuelAguirre\Bundle\TranslationBundle\Util\TranslationsUtil;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddTranslatorResourcesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('translator.default')) {
            return;
        }

        $translator = $container->findDefinition('translator.default');
        $path = $container->getParameter('manuel_translation.catalogues_path');

        foreach ($container->getParameter('manuel_translation.locales') as $locale) {
            $resource = TranslationsUtil::buildResourcePath($path, $locale);

            $translator->addMethodCall('addResource', [
                'doctrine',
                $resource,
                $locale,
            ]);
        }
    }
}
