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
use ManuelAguirre\Bundle\TranslationBundle\Translation\Dumper\CataloguesDumper;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author maguirre <maguirre@developerplace.com>
 */
class DumpFilesListener
{
    /**
     * @var bool
     * @internal
     */
    private $translationChanged = false;

    /**
     * @var CataloguesDumper
     */
    private $cataloguesDumper;

    /**
     * DumpFilesListener constructor.
     *
     * @param Filesystem $filesystem
     * @param array $locales
     * @param string $filenameTemplate
     */
    public function __construct(CataloguesDumper $cataloguesDumper)
    {
        $this->cataloguesDumper = $cataloguesDumper;
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
            $this->cataloguesDumper->dump();

            $this->translationChanged = false;
        }
    }
}