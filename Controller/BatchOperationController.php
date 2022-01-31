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

use Doctrine\ORM\EntityManagerInterface;
use ManuelAguirre\Bundle\TranslationBundle\Doctrine\Listener\TranslationLogListener;
use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationLogRepository;
use ManuelAguirre\Bundle\TranslationBundle\Translation\TranslationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/batch-process")
 */
class BatchOperationController extends AbstractController
{
    /**
     * La idea es pasar las traducciones de los archivos a la base de datos
     *
     * @Route("/files-to-bd", name="manuel_translation_transfer_files_to_bd")
     */
    public function transferFilesToBdAction(
        EntityManagerInterface $em,
        TranslationLogListener $translationLogListener,
        TranslationManager $translationManager,
        TranslatorInterface $translator
    ) {
        $translationLogListener->setActiveLoggin(false);

        $em->beginTransaction();
        $translationManager->extractToDatabase();
        $em->flush();
        $em->commit();

        $translationLogListener->setActiveLoggin(false);

        $this->addFlash('success', $translator
            ->trans('flash.database_loaded', array(), 'ManuelTranslationBundle')
        );

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/inactive-unused-translations", name="manuel_translation_inactive_unused")
     */
    public function inactiveUnusedTranslationsAction(
        EntityManagerInterface $em,
        TranslationManager $translationManager,
        TranslatorInterface $translator
    ) {
        $em->beginTransaction();
        $translationManager->inactiveUnused();
        $em->flush();
        $em->commit();

        $this->addFlash('success', $translator
            ->trans('flash.database_purged_complete', array(), 'ManuelTranslationBundle'));

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/change-status/{id}/{status}",
     *  name="manuel_translation_change_status",
     *  requirements={"status" = "active|inactive"}
     * )
     */
    public function changeStatusAction(
        Translation $translation,
        $status,
        TranslationLogRepository $repository
    ) {
        $translation->setActive($status === 'active');
        $repository->saveTranslation($translation);

        return $this->redirectToRoute('manuel_translation_list');
    }
}