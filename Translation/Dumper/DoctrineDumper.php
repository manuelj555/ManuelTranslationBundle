<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Translation\Dumper;

use Doctrine\ORM\EntityManagerInterface;
use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationValue;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class DoctrineDumper implements DumperInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TranslationRepository $translationRepository,
        private array $locales,
    ) {
    }

    public function dump(MessageCatalogue $messages, array $options = [])
    {
        set_time_limit(10);
        $translations = $this->getExistentTranslations();

        $locale = $messages->getLocale();
        //actualizamos las etiquetas de la BD en base al catálogo
        $translations = $this->setFromCatalogue($messages, $translations, $options);

        foreach ($translations as $domain => $items) {
            foreach ($items as $t) {
                $this->em->persist($t);
            }
        }

        $this->em->flush();
        $this->em->clear();
    }

    public function getExistentTranslations(): array
    {
        $items = $this->translationRepository->findAll();
        $result = [];

        foreach ($items as $e) {
            $result[$e->getDomain()][$e->getCode()] = $e;
        }

        return $result;
    }

    public function dumpCatalogues($catalogues): void
    {
        $translations = $this->getExistentTranslations();

        foreach ($catalogues as $messages) {
            set_time_limit(10);
            $translations = $this->setFromCatalogue($messages, $translations, []);
        }

        foreach ($translations as $domain => $items) {
            foreach ($items as $t) {
                $this->em->persist($t);
            }
        }

        $this->em->flush();
        $this->em->clear();
    }

    /**
     * Actualiza/Crea traducciones en el arreglo $translations.
     *
     * @param MessageCatalogue $catalogue
     * @param                  $translations
     * @param array $options
     *
     * @return mixed
     */
    protected function setFromCatalogue(MessageCatalogue $catalogue, $translations, $options = [])
    {
        $locale = $catalogue->getLocale();

        foreach ($catalogue->all() as $domain => $items) {
            foreach ($items as $code => $value) {
                if (isset($translations[$domain][$code])) {
                    $values = $translations[$domain][$code]->getValues();
                    if (!isset($values[$locale]) or isset($options['restoring'])) {
                        //si no existe el valor de traduccion en el locale actual
                        $translations[$domain][$code]->setValue($locale, $value);
                    }

                    if (!isset($options['restoring'])) {
                        //si se está usando, lo activamos
                        $translations[$domain][$code]->setActive(true);
                    }
                } else {
                    $t = $translations[$domain][$code] = new Translation($code, $domain);

                    $t->setValue($locale, $value);
                    $t->setNew(true);
                    $t->setAutogenerated(true);
                    $t->setActive(true);
                }
            }
        }

        return $translations;
    }
}