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

use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;


/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class DoctrineLoader implements LoaderInterface
{
    /**
     * @var TranslationRepository
     */
    protected $translationRepository;
    protected $fileTemplate;

    function __construct($translationRepository, $fileTemplate)
    {
        $this->translationRepository = $translationRepository;
        $this->fileTemplate = $fileTemplate;
    }

    /**
     * Loads a locale.
     *
     * @param mixed  $resource A resource
     * @param string $locale   A locale
     * @param string $domain   The domain
     *
     * @return MessageCatalogue A MessageCatalogue instance
     *
     * @api
     *
     * @throws NotFoundResourceException when the resource cannot be found
     * @throws InvalidResourceException  when the resource cannot be loaded
     */
    public function load($resource, $locale, $domain = 'messages')
    {
//        if($this->translationRepository->isFresh()){
//            return;
//        }

        //La idea es leer de la base de datos, por el locale, y el dominio

        // @todo: verificar porque por el domino es importante

        $translations = $this->translationRepository->getTranslationsByLocale($locale);

        $catalogue = new MessageCatalogue($locale);

        foreach($translations as $translation){
            $catalogue->set($translation['code'], $translation['value'], $translation['domain']);
        }

        $catalogue->addResource(new FileResource($resource));

        return $catalogue;
    }
}