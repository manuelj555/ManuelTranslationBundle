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
        $locales = $this->container->getParameter('manuel_translation.locales');
        $locale = current($locales);
        $extractDirs = $this->container->getParameter('manuel_translation.extract_dirs');
        $transFilesDirs = $this->container->getParameter('manuel_translation.translations_files_dirs');
        $extractor = $this->get('translation.extractor');

        $usedMessages = new MessageCatalogue($locale);

        foreach ($extractDirs as $dir) {
            $extractor->extract($dir, $usedMessages);
        }

        foreach ($locales as $locale) {
            $catalogue = new MessageCatalogue($locale);
            $used = new MessageCatalogue($locale, $usedMessages->all());

            foreach ($transFilesDirs as $dir) {
                $this->get('manuel_translation.translation_loader')->loadMessages($dir, $catalogue);
            }

            $operation = new MergeOperation($catalogue, $used);
            $merge = $operation->getResult();

            $this->get('manuel_translation.translations_doctrine_dumper')->dump($merge);
        }

        $this->addFlash('success', 'Database Loaded!!!');

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/synchronize/up", name="manuel_translation_synchronize_up")
     */
    public function synchronizeUpAction()
    {
        $response = $this->forward('ManuelTranslationBundle:Api/Translation:getAll');

        $serverTranslations = json_decode($response->getContent(), true);

        $sync = $this->get('manuel_translation.synchronizator');

        $sync->serverItems = $serverTranslations;

        $result = $sync->up($updated);

//        if ($result == Synchronizator::STATUS_CONFLICT) {
//            return $this->redirectToRoute('manuel_translation_show_conflicts');
//        }

        $this->addFlash('success', $this->get('translator')
            ->trans('flash.sync_complete', array('%updated%' => $updated), 'ManuelTranslationBundle'));

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/synchronize/down", name="manuel_translation_synchronize_down")
     */
    public function synchronizeDownAction()
    {
        $response = $this->forward('ManuelTranslationBundle:Api/Translation:getAll');

        $serverTranslations = json_decode($response->getContent(), true);

        $sync = $this->get('manuel_translation.synchronizator');

        $sync->serverItems = $serverTranslations;

        $result = $sync->down($updated);

//        if ($result == Synchronizator::STATUS_CONFLICT) {
//            return $this->redirectToRoute('manuel_translation_show_conflicts');
//        }

        $this->addFlash('success', $this->get('translator')
            ->trans('flash.sync_complete', array('%updated%' => $updated), 'ManuelTranslationBundle'));

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/synchronize/edit-conflicts", name="manuel_translation_show_conflicts")
     */
    public function editConflictsAction()
    {

//        $this->addFlash('success', 'Synchronization Complete!!!');

        return $this->redirectToRoute('manuel_translation_list');
    }

    /**
     * @Route("/inactive-unused-translations", name="manuel_translation_inactive_unused")
     */
    public function inactiveUnusedTranslationsAction()
    {
        $locales = $this->container->getParameter('manuel_translation.locales');
        $locale = current($locales);
        $extractDirs = $this->container->getParameter('manuel_translation.extract_dirs');
        $transFilesDirs = $this->container->getParameter('manuel_translation.translations_files_dirs');
        $extractor = $this->get('translation.extractor');
        $transRepository = $this->get('manuel_translation.translations_repository');

        $usedMessages = new MessageCatalogue($locale);

        foreach ($extractDirs as $dir) {
            $extractor->extract($dir, $usedMessages);
        }

        $bdMessages = $this->get('manuel_translation.translations_doctrine_loader')->load(null, 'en');

        $operation = new DiffOperation($bdMessages, $usedMessages);

        foreach ($bdMessages->all() as $domain => $items) {
            if ($obsoletes = $operation->getObsoleteMessages($domain)) {
                $transRepository->inactiveByDomainAndCodes($domain, array_values($obsoletes));
            }
        }

        $this->addFlash('success', 'Database Purged!!!');

        return $this->redirectToRoute('manuel_translation_list');

    }
}