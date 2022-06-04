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
use function dd;

class ModifyTranslationsProfileTemplatePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('data_collector.templates')) {
            return;
        }

        $templates = $container->getParameter('data_collector.templates');

        if (!isset($templates['data_collector.translation'])) {
            return;
        }

        $config = $templates['data_collector.translation'];
        $templates['data_collector.translation'] = [
            $config[0],
            '@ManuelTranslation/DataCollector/translations.html.twig',
        ];

        $container->setParameter('data_collector.templates', $templates);
    }
}
