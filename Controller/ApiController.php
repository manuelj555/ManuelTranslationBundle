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
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Serializer;


/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 * 
 * @Route("/api", service="manuel_translation.controller.api")
 */
class ApiController
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var TranslationRepository
     */
    private $translationRepository;

    /**
     * ApiController constructor.
     * @param Serializer $serializer
     * @param TranslationRepository $translationRepository
     */
    public function __construct(Serializer $serializer, TranslationRepository $translationRepository)
    {
        $this->serializer = $serializer;
        $this->translationRepository = $translationRepository;
    }

    /**
     * @Route("/", name="manuel_translation_api_list")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $query = $this->translationRepository->getAllQueryBuilder(
            $request->get('search'), 
            $request->get('domains'), 
            $request->get('inactive') && $request->get('inactive') !== 'false'
        );

        if($page = $request->get('page', false) and $perPage = $request->get('perPage', false)){
            $data = new Pagerfanta(new DoctrineORMAdapter($query, false));
            $data->setMaxPerPage($perPage);
            $data->setCurrentPage($page);
        }else{
            $data = $query->getQuery()->getResult();            
        }

        $totalCount = count($data);
        $data = $this->serializer->normalize($data);

        return new JsonResponse($data, Response::HTTP_OK, [
            'X-Count' => $totalCount,
        ]);
    }

    /**
     * @Route("/", name="manuel_translation_api_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $translation = $this->serializer->deserialize(
            $request->getContent(), 
            Translation::class, 
            'json' //, 
            //['object_to_populate' => $translation]
        );

        $this->translationRepository->saveTranslation($translation);

        return new JsonResponse(
            $this->serializer->normalize($translation)
        );
    }

    /**
     * @Route("/{id}", name="manuel_translation_api_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, Translation $translation)
    {
        $translation = $this->serializer->deserialize(
            $request->getContent(), 
            Translation::class, 
            'json', 
            ['object_to_populate' => $translation]
        );

        $this->translationRepository->saveTranslation($translation);

        return new JsonResponse(
            $this->serializer->normalize($translation)
        );
    }

    /**
     * @Route("/domains", name="manuel_translation_api_get_domains")
     * @Method("GET")
     */
    public function getDomainsAction()
    {
        $domains = $this->translationRepository->getExistentDomains();

        return new JsonResponse($domains);
    }
}