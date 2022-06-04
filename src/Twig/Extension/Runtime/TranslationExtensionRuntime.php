<?php
/**
 * @author Manuel Aguirre
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Twig\Extension\Runtime;


use ManuelAguirre\Bundle\TranslationBundle\Provider\TranslationsProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @author Manuel Aguirre
 */
class TranslationExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private TranslationsProvider $provider,
        private RequestStack $requestStack,
        private array $locales,
    ) {
    }

    public function getTranslationsByDomain(string $domain, string $locale = null): array
    {
        if (null === $locale) {
            $locale = $this->requestStack->getCurrentRequest()->getLocale();
        }

        return $this->provider->byLocaleAndDomain($locale, $domain);
    }

    public function getLocales(): array
    {
        return $this->locales;
    }
}