<?php
/**
 * Created by PhpStorm.
 * User: BPEREZ
 * Date: 22/08/2018
 * Time: 4:33 PM
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Translation\Dumper;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Class CataloguesDumper
 * @package ManuelAguirre\Bundle\TranslationBundle\Translation\Dumper
 */
class CataloguesDumper implements CacheWarmerInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $filenameTemplate;

    /**
     * @var array
     */
    private $locales;

    public function __construct(Filesystem $filesystem, $fileTemplate, $locales)
    {
        $this->filesystem = $filesystem;
        $this->filenameTemplate = $fileTemplate;
        $this->locales = $locales;
    }

    public function dump()
    {
        try {
            foreach ($this->locales as $locale) {
                $filename = sprintf($this->filenameTemplate, $locale);
                $this->filesystem->dumpFile($filename, time());
            }
        } catch (IOException $e) {
            // nada por ahora
        }
    }

    public function isOptional()
    {
        return false;
    }

    public function warmUp($cacheDir)
    {
        $this->dump();
    }
}