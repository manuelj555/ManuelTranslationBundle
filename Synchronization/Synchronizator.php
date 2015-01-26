<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Synchronization;

use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class Synchronizator
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;
    const STATUS_CONFLICT = 3;


    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * @var TranslationRepository
     */
    protected $translationRepository;
    /**
     * @var ServerSync
     */
    protected $serverSync;

    function __construct($em, $translationRepository, $serverSync)
    {
        $this->em = $em;
        $this->translationRepository = $translationRepository;
        $this->serverSync = $serverSync;
    }

    public function up(&$updatedItems = 0)
    {
        $updatedItems = 0;
        $localTranslations = $this->translationRepository->getAllWithoutConflicts();
        $serverTranslations = $this->serverSync->findAll();
        $status = self::STATUS_SUCCESS;

        /* @var $local Translation */
        foreach ($localTranslations as $local) {
            if (!$this->existsInServer($serverTranslations, $local)) {
                //si no existe, lo creamos en el server.
                $this->createInServer($local);
                ++$updatedItems;
            } elseif ($this->isLocalChanged($local)) {
                $server = $serverTranslations[$local->getDomain()][$local->getCode()];

                if (!$this->isServerChanged($server, $local)) {
                    $this->updateServer($server, $local);
                    ++$updatedItems;
                } else {
                    //conflict
                    $status = self::STATUS_CONFLICT;
                    $local->setConflicts(true);
                }
            }
        }

        $this->em->flush();

        return $status;
    }

    public function down(&$updatedItems = 0)
    {
        $updatedItems = 0;
        $status = self::STATUS_SUCCESS;
        $localTranslations = array();
        $serverTranslations = $this->serverSync->findAll();

        foreach ($this->translationRepository->getAllEntities() as $item) {
            $localTranslations[$item->getDomain()][$item->getCode()] = $item;
        }

        foreach ($serverTranslations as $domain => $items) {
            foreach ($items as $code => $server) {
                if ($this->existsInLocal($localTranslations, $server)) {
                    /* @var $local Translation */
                    $local = $localTranslations[$server['domain']][$server['code']];
                    if ($this->isServerChanged($server, $local)) {
                        if ($this->isLocalChanged($local)) {
                            $status = self::STATUS_CONFLICT;
                            $local->setConflicts(true);
                        } else {
                            $this->updateLocal($local, $server);
                            $this->markUpdated($server);
                            ++$updatedItems;
                        }
                    }
                } else {
                    $this->createInLocal($server);
                    $this->markUpdated($server);
                    ++$updatedItems;
                }
            }
        }

        $this->em->flush();

        return $status;
    }

    /**
     * Determina si Local está al día
     *
     * @param Translation $translation
     * @param             $server
     */
    public function isLocalChanged(Translation $translation)
    {
        return $translation->getLocalEditions();
    }

    /**
     * Determina si el server está al día
     *
     * @param Translation $translation
     * @param             $server
     */
    public function isServerChanged($server, Translation $local)
    {
        return $local->getSynchronizations() < $server['synchronizations']
        or $server['localEditions'] > 0;
    }

    public function existsInServer($serverItems, Translation $translation)
    {
        return isset($serverItems[$translation->getDomain()][$translation->getCode()]);
    }

    public function existsInLocal($localTranslations, $server)
    {
        return isset($localTranslations[$server['domain']][$server['code']]);
    }

    public function updateLocal(Translation $translation, array $data)
    {
        $translation->setLocalEditions(0);
        $translation->setConflicts(0);
        $translation->setSynchronizations($data['synchronizations']);
        foreach ($data['values'] as $locale => $transValue) {
            //por cada traduccion creamos una en local.
            $translation->setValue($locale, $transValue['value']);
        }
    }

    public function updateServer($server, Translation $translation)
    {
        $this->serverSync->update($server['code'], $server['domain'], $translation);
    }

    public function createInServer(Translation $translation)
    {
        $this->serverSync->add($translation);
    }

    public function createInLocal($data)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $translation = new Translation($data['code'], $data['domain']);
        $translation->setNew($data['new']);
        $translation->setAutogenerated($data['autogenerated']);
        $translation->setActive($data['active']);

        $return = $this->updateLocal($translation, $data);

        $this->em->persist($translation);

        return $return;
    }

    public function markUpdated($server)
    {
        $this->serverSync->markUpdated($server['code'], $server['domain']);
    }
}