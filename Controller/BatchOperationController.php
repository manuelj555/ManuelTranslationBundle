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
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $this->get('manuel_translation.doctrine.translation_log_listener')->setActiveLoggin(false);

        $em->beginTransaction();
        $this->get('manuel_translation.translation_manager')->extractToDatabase();
        $em->flush();
        $em->commit();

        $this->get('manuel_translation.doctrine.translation_log_listener')->setActiveLoggin(true);

        $this->addFlash('success', $this->get('translator')
            ->trans('flash.database_loaded', array(), 'ManuelTranslationBundle'));

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/inactive-unused-translations", name="manuel_translation_inactive_unused")
     */
    public function inactiveUnusedTranslationsAction()
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $em->beginTransaction();
        $this->get('manuel_translation.translation_manager')->inactiveUnused();
        $em->flush();
        $em->commit();

        $this->addFlash('success', $this->get('translator')
            ->trans('flash.database_purged_complete', array(), 'ManuelTranslationBundle'));

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/change-status/{id}/{status}",
     *  name="manuel_translation_change_status",
     *  requirements={"status" = "active|inactive"}
     * )
     */
    public function changeStatusAction(Translation $translation, $status)
    {
        $translation->setActive($status === 'active');
        $this->get('manuel_translation.translations_repository')->saveTranslation($translation);

        return $this->redirectToRoute('manuel_translation_list');
    }
}