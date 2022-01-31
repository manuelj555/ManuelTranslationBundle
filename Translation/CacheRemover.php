<?php
/**
 * Date: 27/07/2018
 * Time: 4:18 PM
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Translation;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CacheRemover
 *
 * @author Manuel Aguirre maguirre@optimeconsulting.com
 */
class CacheRemover
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    private $cacheDir;

    public function __construct(Filesystem $filesystem, $cacheDir)
    {
        $this->filesystem = $filesystem;
        $this->cacheDir = $cacheDir;
    }

    public function clear()
    {
        $path = $this->getPath();

        if (!$this->filesystem->exists($path)) {
            return;
        }

        try {
            $this->filesystem->remove($path);
        } catch (IOException $ex) {
            // no hacer nada
            return false;
        }

        return true;
    }

    private function getPath()
    {
        return rtrim($this->cacheDir, '/').'/translations/';
    }
}