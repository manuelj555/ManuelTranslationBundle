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

use Doctrine\Common\Persistence\ObjectManager;
use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;
use ManuelAguirre\Bundle\TranslationBundle\Translation\Dumper\DoctrineDumper;
use ManuelAguirre\Bundle\TranslationBundle\Translation\Loader\DoctrineLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class Synchronizator
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;
    const STATUS_CONFLICT = 3;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var DoctrineLoader
     */
    private $doctrineLoader;
    /**
     * @var TranslationRepository
     */
    private $translationRepository;
    /**
     * @var Filesystem
     */
    private $filesystem;
    private $locales;
    private $backupDir;

    /**
     * Synchronizator constructor.
     *
     * @param ObjectManager         $om
     * @param DoctrineLoader        $doctrineLoader
     * @param TranslationRepository $translationRepository
     * @param Filesystem            $filesystem
     * @param                       $locales
     * @param                       $backupDir
     */
    public function __construct(ObjectManager $om, DoctrineLoader $doctrineLoader, TranslationRepository $translationRepository, Filesystem $filesystem, $locales, $backupDir)
    {
        $this->om = $om;
        $this->doctrineLoader = $doctrineLoader;
        $this->translationRepository = $translationRepository;
        $this->filesystem = $filesystem;
        $this->locales = $locales;
        $this->backupDir = $backupDir;
    }

    public function generateFile()
    {
        $path = rtrim($this->backupDir, '/') . '/translations.php';
        $translations = $this->translationRepository->getActiveTranslations();

        $export = array();

        /** @var Translation $translation */
        foreach ($translations as $translation) {
            $export[$translation['domain']][$translation['code']] = array(
                'values' => $translation['values'],
                'files' => $translation['files'],
                'hash' => $translation['hash'],
            );
        }

        $output = "<?php\n\nreturn " . var_export($export, true) . ";\n";

        if (is_file($path)) {
            $this->filesystem->copy($path, $path . '~', true);
        }

        $this->filesystem->dumpFile($path, $output);
    }

    public function sync()
    {
        $fileTranslations = $this->createTranslationsFromFile();
        $dbTranslations = $this->getTranslationsFromDatabase();

        $conflicts = array();
        $numNews = $numUpdates = $inactivated = 0;

        foreach ($fileTranslations as $domain => $translations) {
            /**
             * @var string      $code
             * @var Translation $t
             */
            foreach ($translations as $code => $t) {
                if (isset($dbTranslations[$domain][$code])) {
                    /** @var Translation $dbT */
                    $dbT = $dbTranslations[$domain][$code];
                    if ($dbT->getActive()) {
                        // debemos verificar si tienen data distinta
                        // Si el hash es el mismo, ya estan sincronizados
                        if (!$this->isEqueals($t, $dbT) and $t->getHash() != $dbT->getHash()) {
                            $conflicts[] = array('file' => $t, 'database' => $dbT, 'hash' => $t->getHash());
                        }
                    } else {
                        //Si no está activo, lo actualizamos de una vez sin preguntar.
                        $this->updateTranslation($dbT, $t->getValues(), $t->getFiles(), $t->getHash());
                        ++$numUpdates;
                    }
                    // Removemos la traduccion del arreglo para saber cuales ya no existen en el archivo.
                    // y asi poder determinar que traducciones son para desactivar
                    unset($dbTranslations[$domain][$code]);
                } else {
                    //La creamos de una vez
                    ++$numNews;
                    $this->om->persist($t);
                }
            }
        }

        // Las traducciones que quedan aca son para inactivar
        foreach ($dbTranslations as $items) {
            foreach ($items as $item) {
                if ($item->getActive()) {
                    $item->setActive(false);
                    $this->om->persist($item);
                    ++$inactivated;
                }
            }
        }

        $this->om->flush();

        return new SyncResult($numNews, $numUpdates, $conflicts, $inactivated);
    }

    public function updateTranslation(Translation $translation, $values, $files, $hash)
    {
        foreach ($values as $locale => $value) {
            $translation->setValue($locale, $value);
        }

        $translation->setFiles($files);
        $translation->setHash($hash);

        $this->om->persist($translation);
    }

    protected function isEqueals(Translation $file, Translation $database)
    {
        return $file->getValues() == $database->getValues() and
        $file->getFiles() == $database->getFiles();
    }

    protected function createTranslationsFromFile()
    {
        $path = rtrim($this->backupDir, '/') . '/translations.php';

        $translations = require_once $path;

        foreach ($translations as $domain => $items) {
            foreach ($items as $code => $info) {
                $translations[$domain][$code] = $t = new Translation($code, $domain);

                $t->setValues($info['values']);
                $t->setFiles($info['files']);
                $t->setHash($info['hash']);
            }
        }

        return $translations;
    }

    protected function getTranslationsFromDatabase()
    {
        $translations = array();
        $dbTranslations = $this->translationRepository->getAllEntities();

        /** @var Translation $translation */
        foreach ($dbTranslations as $translation) {
            $translations[$translation->getDomain()][$translation->getCode()] = $translation;
        }

        return $translations;
    }

    protected function getActiveTranslationsFromDatabase()
    {
        $translations = array();
        $dbTranslations = $this->translationRepository->getActiveTranslations();

        /** @var Translation $translation */
        foreach ($dbTranslations as $translation) {
            $translations[$translation->getDomain()][$translation->getCode()] = $translation;
        }

        return $translations;
    }
}
