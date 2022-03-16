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
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * @author maguirre <maguirre@developerplace.com>
 */
#[When("dev")]
class DumpFilesListener
{
    /**
     * @internal
     */
    private bool $translationChanged = false;

    public function __construct(private CataloguesDumper $cataloguesDumper)
    {
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