<?php
/**
 * Created by PhpStorm.
 * User: BPEREZ
 * Date: 22/08/2018
 * Time: 4:33 PM
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Translation\Dumper;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

#[AutoconfigureTag("kernel.cache_warmer", ['priority' => 1000])]
class CataloguesDumper implements CacheWarmerInterface
{
    public function __construct(
        private Filesystem $filesystem,
        private string $filenameTemplate,
        private array $locales,
    ) {
    }

    public function dump(): void
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

    /**
     * @return bool
     */
    public function isOptional()
    {
        return false;
    }

    public function warmUp($cacheDir)
    {
        $this->dump();
    }
}