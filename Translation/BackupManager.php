<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Translation;

use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;
use ManuelAguirre\Bundle\TranslationBundle\Translation\Dumper\DoctrineDumper;
use ManuelAguirre\Bundle\TranslationBundle\Translation\Loader\DoctrineLoader;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Catalogue\DiffOperation;
use Symfony\Component\Translation\Catalogue\MergeOperation;
use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class BackupManager
{
    /**
     * @var DoctrineLoader
     */
    private $doctrineLoader;
    /**
     * @var DoctrineDumper
     */
    private $doctrineDumper;
    private $locales;

    function __construct($doctrineLoader, $doctrineDumper, $locales)
    {
        $this->doctrineLoader = $doctrineLoader;
        $this->doctrineDumper = $doctrineDumper;
        $this->locales = $locales;
    }

    /**
     * @param mixed $backupDir
     */
    public function setBackupDir($backupDir)
    {
        $this->backupDir = $backupDir;
    }

    /**
     * @param mixed $backupDumper
     */
    public function setBackupDumper($backupDumper)
    {
        $this->backupDumper = $backupDumper;
    }

    public function generateBackup()
    {
        $path = rtrim($this->backupDir, '/') . '/' . time() . '/%s.php';

        $filesystem = new Filesystem();

        foreach ($this->locales as $locale) {
            $bdMessages = $this->doctrineLoader->load(null, $locale);

            $output = "<?php\n\nreturn " . var_export($bdMessages->all(), true) . ";\n";

            $filesystem->dumpFile(sprintf($path, $locale), $output);
        }
    }

    public function listBackups()
    {
        $dirs = Finder::create()
            ->in($this->backupDir)
            ->directories()
            ->sortByModifiedTime();

        $result = array();
        /* @var $dir SplFileInfo */
        foreach ($dirs as $path => $dir) {
            $result[$dir->getFilename()] = date('Y-m-d H:i:s', $dir->getFilename());
        }

        return array_reverse($result, true);
    }

    public function restore($name)
    {
        $path = rtrim($this->backupDir, '/') . '/' . $name;

        $files = Finder::create()
            ->in($path)
            ->files()
            ->name('/(.+?).php/');

        if(0 === count($files)){
            throw new \LogicException(sprintf("The dir %s not contains any php file", $path));
        }

        /* @var $file SplFileInfo */
        foreach ($files as $filename => $file) {
            list($locale, $ext) = explode('.', $file->getFilename());

            $data = require (string) $file;
            $catalogue = new MessageCatalogue($locale, $data);

            $this->doctrineDumper->dump($catalogue, array('restoring' => true));
        }
    }
}