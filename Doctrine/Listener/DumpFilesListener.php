<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Doctrine\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author maguirre <maguirre@developerplace.com>
 */
class DumpFilesListener
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var array
     */
    private $locales;

    /**
     * @var string
     */
    private $filenameTemplate;

    /**
     * @var bool
     * @internal
     */
    private $translationChanged = false;

    /**
     * DumpFilesListener constructor.
     *
     * @param Filesystem $filesystem
     * @param array $locales
     * @param string $filenameTemplate
     */
    public function __construct(Filesystem $filesystem, array $locales, $filenameTemplate)
    {
        $this->filesystem = $filesystem;
        $this->locales = $locales;
        $this->filenameTemplate = $filenameTemplate;
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->onTranslationSaved($event);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->onTranslationSaved($event);
    }

    public function onTranslationSaved(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof Translation) {
            $this->translationChanged = true;
        }
    }

    public function postFlush()
    {
        if ($this->translationChanged) {
            foreach ($this->locales as $locale) {
                $filename = sprintf($this->filenameTemplate, $locale);
                $this->filesystem->dumpFile($filename, time());
            }
            
            $this->translationChanged = false;
        }
    }
}