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
use ManuelAguirre\Bundle\TranslationBundle\Synchronization\Synchronizator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Catalogue\DiffOperation;
use Symfony\Component\Translation\Catalogue\MergeOperation;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * @Route("/batch-process")
 */
class BatchOperationController extends Controller
{
    /**
     * La idea es pasar las traducciones de los archivos a la base de datos
     *
     * @Route("/files-to-bd", name="manuel_translation_transfer_files_to_bd")
     */
    public function transferFilesToBdAction()
    {
        $this->get('manuel_translation.translation_manager')->extractToDatabase();

        $this->addFlash('success', 'Database Loaded!!!');

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/synchronize/up", name="manuel_translation_synchronize_up")
     */
    public function synchronizeUpAction()
    {
        $result = $this->get('manuel_translation.synchronizator')->up($updated);

        if ($result == Synchronizator::STATUS_CONFLICT OR
            $this->get('manuel_translation.translations_repository')->hasConflicts()
        ) {
            return $this->redirectToRoute('manuel_translation_show_conflicts');
        }

        $this->addFlash('success', $this->get('translator')
            ->trans('flash.sync_complete', array('%updated%' => $updated), 'ManuelTranslationBundle'));

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/synchronize/down", name="manuel_translation_synchronize_down")
     */
    public function synchronizeDownAction()
    {
        $result = $this->get('manuel_translation.synchronizator')->down($updated);

        if ($result == Synchronizator::STATUS_CONFLICT OR
            $this->get('manuel_translation.translations_repository')->hasConflicts()
        ) {
            return $this->redirectToRoute('manuel_translation_show_conflicts');
        }

        $this->addFlash('success', $this->get('translator')
            ->trans('flash.sync_complete', array('%updated%' => $updated), 'ManuelTranslationBundle'));

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/synchronize/edit-conflicts", name="manuel_translation_show_conflicts")
     */
    public function editConflictsAction()
    {
        $translations = $this->get('manuel_translation.translations_repository')
            ->getAllWithConflicts();

        if (!count($translations)) {
            $this->addFlash('info', $this->get('translator')
                ->trans('flash.no_conflicts', array(), 'ManuelTranslationBundle'));

            return $this->redirectToRoute('manuel_translation_list');
        }

        $serverTranslations = $this->get('manuel_translation.server_sync')->findAll();

        return $this->render('@ManuelTranslation/Default/edit_conflicts.html.twig', array(
            'translations' => $translations,
            'server_translations' => $serverTranslations,
        ));
    }

    /**
     * @Route("/inactive-unused-translations", name="manuel_translation_inactive_unused")
     */
    public function inactiveUnusedTranslationsAction()
    {
        $this->get('manuel_translation.translation_manager')->inactiveUnused();

        $this->addFlash('success', 'Database Purged!!!');

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/generate-backup", name="manuel_translation_generate_backup")
     */
    public function generateBackupAction()
    {
        $this->get('manuel_translation.translation_manager')->generateBackup();

        $this->addFlash('success', 'Database backup Complete!!!');

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/resolve-conflict/{id}-{use}",
     *  name="manuel_translation_resolve_conflict",
     *  requirements={"use" = "local|server"}
     * )
     */
    public function resolveConflictAction(Translation $translation, $use)
    {
        if ($use === 'local') {
            $this->get('manuel_translation.synchronizator')->resolveConflictUsingLocal($translation);
        } else {
            $this->get('manuel_translation.synchronizator')->resolveConflictUsingServer($translation);
        }

        return new Response('Ok');
    }
}