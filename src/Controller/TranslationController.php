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
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;
use ManuelAguirre\Bundle\TranslationBundle\Synchronization\Synchronizer;
use ManuelAguirre\Bundle\TranslationBundle\Translation\CacheRemover;
use ManuelAguirre\Bundle\TranslationBundle\Translation\Loader\DoctrineLoader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function array_diff;
use function array_diff_ukey;
use function array_udiff;
use function json_decode;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
#[IsGranted('manage_translations')]
class TranslationController extends AbstractController
{
    public function __construct(private ParameterBagInterface $parameters)
    {
    }

    #[Route("/list/{page}", name: "manuel_translation_list")]
    public function index(
        Request $request,
        TranslationRepository $repository,
        int $page = 1
    ): Response {
        return $this->render('@ManuelTranslation/Default/index.html.twig', array(
            'locales' => $this->parameters->get('manuel_translation.locales'),
            'domains' => $repository->getExistentDomains(),
        ));
    }

    #[Route("/show/item/{id}", name: "manuel_translation_show_item")]
    public function getTranslationItem(Translation $translation): Response
    {
        return $this->render('@ManuelTranslation/Translation/_row.html.twig', array(
            'translation' => $translation,
            'locales' => $this->parameters->get('manuel_translation.locales'),
        ));
    }

    #[Route("/save-from-profiler", name: "manuel_translation_save_from_profiler")]
    public function saveFromProfiler(
        Request $request,
        ValidatorInterface $validator,
        TranslationRepository $repository
    ): Response {
        $translation = $this->getNewTranslationInstance();
        $translation->setCode($request->request->get('code'));
        $translation->setDomain($request->request->get('domain'));

        foreach ($request->request->get('values', array()) as $locale => $value) {
            $translation->setValue($locale, $value);
        }

        if (count($validator->validate($translation)) == 0) {
            $repository->saveTranslation($translation);
        }

        return new Response('Ok');
    }

    #[Route("/get-missing/", name: "manuel_translation_get_missing_items", methods: "post")]
    public function getMissing(
        Request $request,
        TranslationRepository $repository
    ): Response {
        $search = json_decode($request->getContent(), true);

        $items = $repository->findByCodesAndDomains($search);

        $missing = array_udiff($search, $items, fn($a, $b) => $a <=> $b);
        dump($missing);

        return $this->json($missing);
    }

    protected function getNewTranslationInstance(): Translation
    {
        $translation = new Translation();
        $translation->setActive(true);

        foreach ($this->parameters->get('manuel_translation.locales') as $locale) {
            $translation->setValue($locale, null);
        }

        return $translation;
    }

    #[Route("/download.php", name: "manuel_translation_download_backup_file")]
    public function liveDownloadBackup(
        Synchronizer $synchronizator,
        TranslatorInterface $translator
    ): Response {
        $path = $this->getParameter('kernel.cache_dir') . '/sf_translations.php';

        if (!$synchronizator->generateFile($path)) {
            $this->addFlash('warning',
                $translator->trans('local_hash_update_of_range', array(), 'ManuelTranslationBundle'));

            return $this->redirectToRoute('manuel_translation_list');
        }

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }

    #[Route("/clear-cache", name: "manuel_translation_clear_cache")]
    public function clearCache(
        CacheRemover $cacheRemover
    ): Response {
        if (false !== $cacheRemover->clear()) {
            $this->addFlash('success', 'Caché limpiada con éxito');
        } else {
            $this->addFlash('warning', 'No se pudo limpiar la caché de traducciones');
        }

        return $this->redirectToRoute('manuel_translation_list');
    }
}