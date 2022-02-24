<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Translation\Loader;

use ManuelAguirre\Bundle\TranslationBundle\TranslationRepository;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
#[AutoconfigureTag("translation.loader", ['alias' => 'doctrine'])]
class DoctrineLoader implements LoaderInterface
{
    function __construct(
        private TranslationRepository $translationRepository,
    ) {
    }

    public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
    {
        $translations = $this->translationRepository->getActiveTranslations();

        $catalogue = new MessageCatalogue($locale);

        foreach ($translations as $translation) {
            if (array_key_exists($locale, $translation['values'])) {
                $code = trim($translation['code']);
                $catalogue->set($code, $translation['values'][$locale], $translation['domain']);
            }
        }

        $catalogue->addResource(new DirectoryResource($resource));

        return $catalogue;
    }
}