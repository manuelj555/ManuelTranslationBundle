<?php
/**
 * @author Manuel Aguirre
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Twig\Extension;

use ManuelAguirre\Bundle\TranslationBundle\Twig\Extension\Runtime\TranslationExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Manuel Aguirre
 */
class TranslationExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'get_translations_by_domain',
                [TranslationExtensionRuntime::class, 'getTranslationsByDomain']
            ),
        ];
    }
}