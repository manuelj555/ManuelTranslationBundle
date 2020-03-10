<?php
/**
 * Optime Consulting
 * User: maguirre@optimeconsulting.com
 * Date: 10/03/2020
 * Time: 10:17 AM
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Provider;

use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Manuel Aguirre
 */
class TranslationsProvider
{
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var TranslationRepository
     */
    private $repository;

    public function __construct(
        TranslatorInterface $translator,
        TranslationRepository $repository
    ) {
        $this->translator = $translator;
        $this->repository = $repository;
    }

    public function byLocalAndDomain($locale, $domain): array
    {
        if ($this->translator instanceof TranslatorBagInterface) {
            return $this->translator->getCatalogue($locale)->all($domain);
        }

        $items = $this->repository->getAll(null, $domain);
        $translations = [];

        foreach ($items as $item) {
            $translations[$item['code']] = $item['values'][$locale] ?? '';
        }

        return $translations;
    }
}