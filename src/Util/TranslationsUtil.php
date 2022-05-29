<?php
/**
 * @author Manuel Aguirre
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Util;


/**
 * @author Manuel Aguirre
 */
class TranslationsUtil
{
    public static function buildResourcePath(string $cataloguesPath, string $locale): string
    {
        return sprintf(
            '%s/messages.%s.doctrine',
            rtrim($cataloguesPath, '/'),
            $locale,
        );
    }
}