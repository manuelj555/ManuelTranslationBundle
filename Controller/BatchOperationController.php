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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route("/batch-process")]
#[IsGranted('manage_translations')]
class BatchOperationController extends AbstractController
{
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