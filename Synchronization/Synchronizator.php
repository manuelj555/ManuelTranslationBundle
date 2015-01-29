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
        //los pedimos todos porque puede que no existan arriba
        $localTranslations = $this->translationRepository->getAllWithoutConflicts();
        $serverTranslations = $this->serverSync->findAll();
        $status = self::STATUS_SUCCESS;

        if ($localTranslations) {
            $this->serverSync->generateBackup();
        }

        /* @var $local Translation */
        foreach ($localTranslations as $local) {
            set_time_limit(10);
            if (!$this->existsInServer($serverTranslations, $local)) {
                //si no existe, lo creamos en el server.
                $this->createInServer($local);
                ++$updatedItems;
            } elseif ($this->isLocalChanged($local)) {
                $server = $serverTranslations[$local->getDomain()][$local->getCode()];

                $equals = $this->isEqual($local, $server);
                $serverChanged = $this->isServerChanged($server, $local);

                if (!$serverChanged and !$equals) {
                    $this->updateServer($local);
                    ++$updatedItems;
                } elseif (!$equals) {
                    //conflict
                    $status = self::STATUS_CONFLICT;
                    $local->setConflicts(true);
                } else {
                    $local->setIsChanged(false);
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
                set_time_limit(10);
                if ($this->existsInLocal($localTranslations, $server)) {
                    /* @var $local Translation */
                    $local = $localTranslations[$server['domain']][$server['code']];
                    if ($this->isServerChanged($server, $local)) {
                        if ($this->isLocalChanged($local) and !$this->isEqual($local, $server)) {
                            $status = self::STATUS_CONFLICT;
                            $local->setConflicts(true);
                        } else {
                            $this->updateLocal($local, $server);
//                            $this->markUpdated($server);
                            ++$updatedItems;
                        }
                    }
                } else {
                    $this->createInLocal($server);
//                    $this->markUpdated($server);
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
        return $translation->getIsChanged();
    }

    /**
     * Determina si el server está al día
     *
     * @param Translation $translation
     * @param             $server
     */
    public function isServerChanged($server, Translation $local)
    {
        return $local->getVersion() < $server['version']
        OR $local->getServerEditions() < $server['serverEditions'];
    }

    public function existsInServer($serverItems, Translation $translation)
    {
        return isset($serverItems[$translation->getDomain()][$translation->getCode()]);
    }

    public function existsInLocal($localTranslations, $server)
    {
        return isset($localTranslations[$server['domain']][$server['code']]);
    }

    public function isEqual(Translation $translation, array $server)
    {
        if ($translation->getCode() !== $server['code']) {
            return false;
        }

        if ($translation->getDomain() !== $server['domain']) {
            return false;
        }

        if ($translation->getServerEditions() !== $server['serverEditions']) {
            return false;
        }

        if ($translation->getActive() !== $server['active']) {
            return false;
        }

        if ($translation->getAutogenerated() !== $server['autogenerated']) {
            return false;
        }

        if ($translation->getNew() !== $server['new']) {
            return false;
        }

        if ($translation->getVersion() !== $server['version']) {
            return false;
        }

        if (count($translation->getValues()) !== count($server['values'])) {
            return false;
        }

        foreach ($translation->getValues() as $locale => $tVal) {
            if ($tVal->getValue() !== $server['values'][$locale]['value']) {
                return false;
            }
        }

        if ($translation->getFiles() !== $server['files']) {
            return false;
        }

        return true;
    }

    public function updateLocal(Translation $translation, array $data)
    {
        $translation->setLocalEditions(0);
        $translation->setConflicts(0);
        $translation->setVersion($data['version']);
        $translation->setServerEditions($data['serverEditions']);
        $translation->setIsChanged(false);
        $translation->setFiles($data['files']);
        foreach ($data['values'] as $locale => $transValue) {
            //por cada traduccion creamos una en local.
            $translation->setValue($locale, $transValue['value']);
        }
    }

    public function updateServer(Translation $translation, $force = false)
    {
        $this->serverSync->update($translation, $force);
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

//    public function markUpdated($server)
//    {
//        $this->serverSync->markUpdated($server['code'], $server['domain']);
//    }

    public function resolveConflictUsingLocal(Translation $translation)
    {
        $this->updateServer($translation, true);

        $this->em->flush();
    }

    public function resolveConflictUsingServer(Translation $translation)
    {
        $data = $this->serverSync->find($translation->getCode(), $translation->getDomain());
        $this->updateLocal($translation, $data);

        $this->em->flush();
    }
}