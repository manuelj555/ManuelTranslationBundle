<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Translation;

use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 */
#[When("dev")]
class DebugTranslator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    private $missingTranslations = array();

    /**
     * @param Translator $translator
     */
    public function __construct($translator)
    {
        if (!$translator instanceof TranslatorInterface) {
            throw new \InvalidArgumentException(sprintf('The Translator "%s" must implements TranslatorInterface',
                get_class($translator)));
        }

        $this->translator = $translator;
    }

    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        $trans = $this->translator->trans($id, $parameters, $domain, $locale);
        $this->debug($id, $domain, $locale);

        return $trans;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function setLocale(string $locale)
    {
        if (!$this->translator instanceof LocaleAwareInterface) {
            throw new \LogicException("Se esperaba que el traductor implementara 'LocaleAwareInterface'");
        }

        $this->translator->setLocale($locale);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getLocale(): string
    {
        if (!$this->translator instanceof LocaleAwareInterface) {
            throw new \LogicException("Se esperaba que el traductor implementara 'LocaleAwareInterface'");
        }

        return $this->translator->getLocale();
    }

    /**
     * Passes through all unknown calls onto the translator object.
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->translator, $method), $args);
    }

    /**
     * Logs for missing translations.
     *
     * @param string $id
     * @param string|null $domain
     * @param string|null $locale
     */
    private function debug($id, $domain, $locale)
    {
        if (null === $locale) {
            $locale = $this->getLocale();
        }

        if (null === $domain) {
            $domain = 'messages';
        }

        $id = (string)$id;
        $catalogue = $this->getCatalogue($locale);
        if ($catalogue->defines($id, $domain)) {
            return;
        }

        if (!$catalogue->has($id, $domain)) {
            if (!isset($this->missingTranslations[$domain]) or !in_array($id, $this->missingTranslations[$domain])) {
                $this->missingTranslations[$domain][] = $id;
            }
        }
    }

    /**
     * @return array
     */
    public function getMissingTranslations()
    {
        return $this->missingTranslations;
    }

    public function getCatalogue(string $locale = null): MessageCatalogueInterface
    {
        if (!$this->translator instanceof TranslatorBagInterface) {
            throw new \LogicException("Se esperaba que el traductor implementara 'TranslatorBagInterface'");
        }

        return $this->translator->getCatalogue($locale);
    }

    public function getCatalogues(): array
    {
        if (!$this->translator instanceof TranslatorBagInterface) {
            throw new \LogicException("Se esperaba que el traductor implementara 'TranslatorBagInterface'");
        }

        return $this->translator->getCatalogues();
    }
}