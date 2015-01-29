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

    /**
     * @var TranslationRepository
     */
    protected $translationRepository;
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;
    protected $locales;
    protected $existentTranslations;

    function __construct($em, $translationRepository, $locales)
    {
        $this->em = $em;
        $this->translationRepository = $translationRepository;
        $this->locales = $locales;
    }


    /**
     * Dumps the message catalogue.
     *
     * @param MessageCatalogue $messages The message catalogue
     * @param array            $options  Options that are used by the dumper
     */
    public function dump(MessageCatalogue $messages, $options = array())
    {
        set_time_limit(10);
        //la idea es buscar las etiquetas que no existan y crearlas.
        if (null === $this->existentTranslations) {
            $this->existentTranslations = $translations = $this->getExistentTranslations();
        }

        $locale = $messages->getLocale();

        foreach ($messages->all() as $domain => $items) {
            foreach ($items as $code => $value) {

                if (isset($this->existentTranslations[$domain][$code])) {

                    $values = $this->existentTranslations[$domain][$code]->getValues();
                    if (!isset($values[$locale])) {
                        //si no existe el valor de traduccion en el locale actual, lo creamos
                        $this->existentTranslations[$domain][$code]->setValue($locale, $value);
                    }

                    //si se está usando, lo activamos
                    $this->existentTranslations[$domain][$code]->setActive(true);

                } else {
                    $t = $this->existentTranslations[$domain][$code] = new Translation($code);

                    $t->setValue($locale, $value);
                    $t->setNew(true);
                    $t->setAutogenerated(true);
                    $t->setActive(true);
                    $t->setDomain($domain);
                    $t->setIsChanged(true);
                }
            }
        }

        foreach ($this->existentTranslations as $domain => $items) {
            foreach ($items as $t) {
                $this->translationRepository->saveTranslation($t, false);
            }
        }

        $this->em->flush();
    }

    public function getExistentTranslations()
    {
        $items = $this->translationRepository->findAll();

        $translations = array();

        foreach ($items as $e) {
            $translations[$e->getDomain()][$e->getCode()] = $e;
        }

        return $translations;
    }
}