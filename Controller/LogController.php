<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Controller;

use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationLogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ autor Manuel Aguirre <programador.manuel@gmail.com>
 * @Route("/logs")
 */
class LogController extends AbstractController
{
    /**
     * @Route("/list/{id}", name="manuel_translation_log_list")
     */
    public function listAction(
        Translation $translation,
        TranslationLogRepository $repository
    ) {
        $logs = $repository->getAllByTranslation($translation);

        return $this->render('@ManuelTranslation/Log/list.html.twig', array(
            'logs' => $logs,
            'translation' => $translation,
        ));
    }
}