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
use ManuelAguirre\Bundle\TranslationBundle\Translation\BackupManager;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class LocalSynchronizator
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;
    const STATUS_CONFLICT = 3;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var BackupManager
     */
    protected $backupManager;
    protected $file;

    /**
     * @var TranslationRepository
     */
    protected $translationRepository;

    function __construct($em, $translationRepository, $backupManager, $filesystem, $file)
    {
        $this->em = $em;
        $this->translationRepository = $translationRepository;
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->backupManager = $backupManager;
    }

    public function toFile()
    {
        //La idea es sincronizar contra un archivo local que contenga los objetos serializados
        $translations = $this->translationRepository->getAllWithoutConflicts();

        //si no existe solo creamos todas las traducciones.
        $data = array();

        /** @var Translation $t */
        foreach ($translations as $t) {
            $data[$t->getDomain()][$t->getCode()] = $t;
        }

        if (file_exists($this->file)) {
            copy($this->file, $this->file . '~');
        }

        $this->filesystem->dumpFile($this->file, serialize($data));

        return $this->file;
    }

    public function fromFile()
    {
        if (file_exists($this->file)) {

            $backupName = $this->backupManager->generateBackup();

            $data = unserialize(file_get_contents($this->file));
//            dump($data);die;

            foreach ($this->translationRepository->getAllEntities() as $item) {
                $localTranslations[$item->getDomain()][$item->getCode()] = $item;
            }

            /** @var Translation $t */
            foreach ($data as $domain => $translations) {
                foreach ($translations as $code => $t) {
                    if ($this->existsInLocal($localTranslations, $t)) {
                        $local = $localTranslations[$domain][$code];
                        $this->updateLocal($local, $t);
                    } else {
                        $this->createInLocal($t);
                    }
                }
            }

            $this->em->flush();

            return $backupName;
        }
    }

    protected function updateLocal(Translation $local, Translation $fileTrans)
    {
        $local->setLocalEditions($fileTrans->getLocalEditions());
        $local->setConflicts($fileTrans->getConflicts());
        $local->setVersion($fileTrans->getVersion());
        $local->setServerEditions($fileTrans->getServerEditions());
        $local->setIsChanged($fileTrans->getIsChanged());
        $local->setFiles($fileTrans->getFiles());
        $local->setValues($fileTrans->getValues());
    }

    protected function createInLocal(Translation $trans)
    {
        $this->em->persist($trans);
    }

    protected function existsInLocal($localTranslations, Translation $fileTrans)
    {
        return isset($localTranslations[$fileTrans->getDomain()][$fileTrans->getCode()]);
    }

}