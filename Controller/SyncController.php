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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class SyncController extends Controller
{
    /**
     * @Route("/generate-file", name="manuel_translation_generate_file")
     */
    public function generateFilesAction()
    {
        $this->get('manuel_translation.synchronizator')->generateFile();

        $this->addFlash('success', $this->get('translator')->trans('update_file_complete_flash'));

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/sync", name="manuel_translation_load_from_file")
     */
    public function syncAction()
    {
        $result = $this->get('manuel_translation.synchronizator')->sync();

        return $this->render('@ManuelTranslation/Sync/resolve_conflicts.html.twig', array(
            'news' => $result->getNews(),
            'updates' => $result->getUpdated(),
            'conflicted_items' => $result->getConflictItems(),
            'inactivated' => $result->getInactivated(),
        ));
    }

    /**
     * @Route("/resolve-conflict/{id}",
     *  name="manuel_translation_resolve_conflict"
     * )
     */
    public function resolveConflictAction(Translation $translation, Request $request)
    {
        $values = $request->request->get('values', array());
        $files = $request->request->get('files', array());
        $hash = $request->request->get('hash');

        $this->get('manuel_translation.synchronizator')->updateTranslation(
            $translation, $values, $files, $hash
        );
        $this->getDoctrine()->getManager()->flush();

        return new Response('Ok');
    }
}