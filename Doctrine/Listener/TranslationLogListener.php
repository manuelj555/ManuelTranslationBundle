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
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationLog;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationLogListener
{
    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($event->getEntity() instanceof Translation) {
            $log = new TranslationLog();
            $log->setTranslation($event->getEntity());

            $event->getEntityManager()->persist($log);
            $event->getEntityManager()->flush($log);
        }
    }
}