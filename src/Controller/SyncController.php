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

use ManuelAguirre\Bundle\TranslationBundle\Synchronization\SyncFromConflicts;
use ManuelAguirre\Bundle\TranslationBundle\Synchronization\Synchronizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function json_decode;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
#[IsGranted('manage_translations')]
class SyncController extends AbstractController
{
    #[Route("/generate-file", name: "manuel_translation_generate_file")]
    public function generateFiles(
        Synchronizer $synchronizator,
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
        Synchronizer $synchronizator,
        SerializerInterface $serializer,
        Request $request,
    ): Response {
        $result = $synchronizator->sync($request->query->getBoolean('forced'));

        return $this->render('@ManuelTranslation/Sync/resolve_conflicts.html.twig', array(
            'news' => $result->getNews(),
            'updates' => $result->getUpdated(),
            'conflicted_items' => $serializer->serialize($result->getConflictItems(), 'json'),
            'conflicted_items_count' => count($result->getConflictItems()),
        ));
    }

    #[Route("/resolve-conflicts/", name: "manuel_translation_resolve_conflicts")]
    public function resolveConflicts(
        Request $request,
        SyncFromConflicts $sync,
    ): Response {
        $requestData = json_decode($request->getContent(), true);

        $sync->processFromRequest(
            $requestData['items'] ?? [],
            (bool)$requestData['finished'] ?? false,
        );

        return new Response('Ok');
    }
}