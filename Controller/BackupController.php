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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * @Route("/backup")
 */
class BackupController extends Controller
{
    /**
     * @Route("/generate-backup", name="manuel_translation_generate_backup")
     */
    public function generateBackupAction()
    {
        $this->get('manuel_translation.backup_manager')->generateBackup();

        $this->addFlash('success', $this->get('translator')
            ->trans('flash.database_backup_comeplete', array(), 'ManuelTranslationBundle'));

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/list", name="manuel_translation_backup_list")
     */
    public function listBackupsAction()
    {
        $backups = $this->get('manuel_translation.backup_manager')->listBackups();

        return $this->render('@ManuelTranslation/Backup/list.html.twig', array(
            'backups' => $backups,
        ));
    }

    /**
     * @Route("/restore/{backup}", name="manuel_translation_backup_restore")
     */
    public function restoreBackupAction($backup)
    {
        $backups = $this->get('manuel_translation.backup_manager')->restore($backup);

        $this->addFlash('success', $this->get('translator')
            ->trans('flash.restore_backup_comeplete', array(), 'ManuelTranslationBundle'));

        return $this->redirectToRoute('manuel_translation_list');
    }
}