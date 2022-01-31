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
use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Translation\TranslationManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route("/batch-process")]
class BatchOperationController extends AbstractController
{
    /**
     * La idea es pasar las traducciones de los archivos a la base de datos
     */
    #[Route("/files-to-bd", name: "manuel_translation_transfer_files_to_bd")]
    public function transferFilesToBd(
        EntityManagerInterface $em,
        TranslationManager $translationManager,
        TranslatorInterface $translator
    ): Response {
        $em->beginTransaction();
        $translationManager->extractToDatabase();
        $em->flush();
        $em->commit();

        $this->addFlash('success', $translator
            ->trans('flash.database_loaded', array(), 'ManuelTranslationBundle')
        );

        return $this->redirectToRoute('manuel_translation_list');
    }

    #[Route(
        "/change-status/{id}/{status}",
        name: "manuel_translation_change_status",
        requirements: ["status" => "active|inactive"],
    )]
    public function changeStatus(
        Translation $translation,
        $status,
    ): Response {
        $translation->setActive($status === 'active');

        return $this->redirectToRoute('manuel_translation_list');
    }
}