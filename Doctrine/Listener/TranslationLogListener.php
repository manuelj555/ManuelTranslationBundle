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

use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationLog;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationLogListener
{
    private $activeLoggin = true;
    private $needFlush = false;

    public function preUpdate(PreUpdateEventArgs $event)
    {
        if ($this->activeLoggin AND $event->getObject() instanceof Translation) {

            if ($event->hasChangedField('files') OR $event->hasChangedField('values')
                OR $event->hasChangedField('active') OR $event->hasChangedField('code')
                OR $event->hasChangedField('domain')
            ) {

                dump($event->getEntityChangeSet());

                $log = new TranslationLog();
                $log->setTranslation($event->getObject());

                if ($event->hasChangedField('files')) {
                    $log->setFiles($event->getOldValue('files'));
                }
                if ($event->hasChangedField('values')) {
                    $log->setValues($event->getOldValue('values'));
                }
                if ($event->hasChangedField('active')) {
                    $log->setActive($event->getOldValue('active'));
                }
                if ($event->hasChangedField('code')) {
                    $log->setCode($event->getOldValue('code'));
                }
                if ($event->hasChangedField('domain')) {
                    $log->setDomain($event->getOldValue('domain'));
                }

                $event->getEntityManager()->persist($log);
                $this->needFlush = true;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if ($this->needFlush) {
            $this->needFlush = false;
            $event->getEntityManager()->flush();
        }
    }

    /**
     * @param boolean $activeLoggin
     */
    public function setActiveLoggin($activeLoggin)
    {
        $this->activeLoggin = $activeLoggin;
    }
}