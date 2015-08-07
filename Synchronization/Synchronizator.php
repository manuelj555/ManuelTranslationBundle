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
     * @param DoctrineLoader        $doctrineLoader
     * @param TranslationRepository $translationRepository
     * @param Filesystem            $filesystem
     * @param                       $locales
     * @param                       $backupDir
     */
    public function __construct(DoctrineLoader $doctrineLoader, TranslationRepository $translationRepository, Filesystem $filesystem, $locales, $backupDir)
    {
        $this->doctrineLoader = $doctrineLoader;
        $this->translationRepository = $translationRepository;
        $this->filesystem = $filesystem;
        $this->locales = $locales;
        $this->backupDir = $backupDir;
    }

    public function generateFiles()
    {
        $path = rtrim($this->backupDir, '/') . '/%s.php';

        foreach ($this->locales as $locale) {
            $bdMessages = $this->doctrineLoader->load(null, $locale);

            $output = "<?php\n\nreturn " . var_export($bdMessages->all(), true) . ";\n";

            $this->filesystem->dumpFile(sprintf($path, $locale), $output);
        }
    }

    public function sync()
    {
        $fileTranslations = $this->createTranslationsFromFiles();
        $dbTranslations = $this->getTranslationsFromDatabase();

        foreach ($fileTranslations as $domain => $translations) {
            /**
             * @var string $code
             * @var Translation $t
             */
            foreach ($translations as $code => $t) {
                if(isset($dbTranslations[$domain][$code])){
                    //debemos verificar si tienen data distinta
                }else{
                    //La creamos de una vez
                }
            }

        }

        dump($fileTranslations, $dbTranslations);
    }

    protected function createTranslationsFromFiles()
    {
        $path = $this->backupDir;
        $translations = array();

        $files = Finder::create()
            ->in($path)
            ->files()
            ->name(sprintf('/(%s).php/', join('|', $this->locales)));

        if (0 === count($files)) {
            throw new \LogicException(sprintf("The dir %s not contains any php file", $path));
        }

        /* @var $file SplFileInfo */
        foreach ($files as $filename => $file) {
            list($locale, $ext) = explode('.', $file->getFilename());

            $data = require (string) $file;

            foreach ($data as $domain => $values) {
                foreach ($values as $code => $value) {
                    if (!isset($translations[$domain][$code])) {
                        $translations[$domain][$code] = new Translation($code, $domain);
                    }

                    $translations[$domain][$code]->setValue($locale, $value);
                }
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
}
