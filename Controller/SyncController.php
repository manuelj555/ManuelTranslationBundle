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
use ManuelAguirre\Bundle\TranslationBundle\Model\TranslationLastEdit;
use ManuelAguirre\Bundle\TranslationBundle\Synchronization\Synchronizator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class SyncController extends AbstractController
{
    #[Route("/generate-file", name: "manuel_translation_generate_file")]
    public function generateFiles(
        Synchronizator $synchronizator,
        TranslatorInterface $translator
    ): Response {
        if ($synchronizator->generateFile()) {
            $this->addFlash('success',
                $translator->trans('update_file_complete_flash', [], 'ManuelTranslationBundle'));
        } else {
            $this->addFlash('warning',
                $translator->trans('local_hash_update_of_range', [], 'ManuelTranslationBundle'));
        }

        return $this->redirectToRoute('manuel_translation_list');
    }

    #[Route("/sync", name: "manuel_translation_load_from_file")]
    public function sync(
        Synchronizator $synchronizator,
        Request $request
    ): Response {
        $result = $synchronizator->sync($request->query->getBoolean('forced'));

        return $this->render('@ManuelTranslation/Sync/resolve_conflicts.html.twig', array(
            'news' => $result->getNews(),
            'updates' => $result->getUpdated(),
            'conflicted_items' => $result->getConflictItems(),
        ));
    }

    #[Route("/resolve-conflict/{id}", name: "manuel_translation_resolve_conflict")]
    public function resolveConflict(
        Translation $translation,
        Request $request,
        Synchronizator $synchronizator,
        EntityManagerInterface $entityManager
    ): Response {
        $values = (array)$request->request->get('values', []);
        $hash = $request->request->get('hash');
        $active = $request->request->get('active');
        $new = $request->request->get('new');
        $autogenerated = $request->request->get('autogenerated');
        $lastChanged = TranslationLastEdit::from($request->request->get('last_changed'));

        $synchronizator->updateTranslation(
            $translation, $values, $hash, $active, $new, $autogenerated, $lastChanged
        );
        $entityManager->flush();

        return new Response('Ok');
    }

    #[Route("/resolve-conflict-done", name: "manuel_translation_resolve_conflict_done")]
    public function doneSync(Synchronizator $synchronizator): Response
    {
        $synchronizator->markSyncAsDone();

        return $this->redirectToRoute('manuel_translation_list');
    }
}