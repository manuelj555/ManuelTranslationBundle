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
    /**
     * @var TranslationsProvider
     */
    private $provider;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        TranslationsProvider $provider,
        RequestStack $requestStack
    ) {
        $this->provider = $provider;
        $this->requestStack = $requestStack;
    }

    public function getTranslationsByDomain(string $domain, string $locale = null): array
    {
        if (null === $locale) {
            $locale = $this->requestStack->getCurrentRequest()->getLocale();
        }

        return $this->provider->byLocaleAndDomain($locale, $domain);
    }
}