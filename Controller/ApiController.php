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
use ManuelAguirre\Bundle\TranslationBundle\Http\ResponseGenerator;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 *
 * @Route("/api",
 *     requirements={"_format" = "xml|json"},
 *     defaults={"_format" = "json"}
 * )
 */
class ApiController
{
    /**
     * @var ResponseGenerator
     */
    private $responseGenerator;

    /**
     * @var TranslationRepository
     */

    private $translationRepository;
    
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ApiController constructor.
     * @param ResponseGenerator $responseGenerator
     * @param TranslationRepository $translationRepository
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ResponseGenerator $responseGenerator,
        TranslationRepository $translationRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->responseGenerator = $responseGenerator;
        $this->translationRepository = $translationRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route(".{_format}", name="manuel_translation_api_list", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
        $query = $this->translationRepository->getAllQueryBuilder(
            $request->get('search'),
            $request->get('domains'),
            $request->get('inactive') && $request->get('inactive') !== 'false'
        );

        if ($page = $request->get('page', false) and $perPage = $request->get('perPage', false)) {
            $data = new Pagerfanta(new DoctrineORMAdapter($query, false));
            $data->setMaxPerPage($perPage);
            $data->setCurrentPage($page);
        } else {
            $data = $query->getQuery()->getResult();
        }

        return $this->responseGenerator->forAll($request, $data, [
            'X-Count' => count($data),
        ]);
    }

    /**
     * @Route(".{_format}", name="manuel_translation_api_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $translation = $this->serializer->deserialize(
            $request->getContent(),
            Translation::class,
            'json'
        );

        if (!count($errors = $this->validator->validate($translation))) {
            $this->translationRepository->saveTranslation($translation);
        }

        return $this->responseGenerator->forOne($request, $translation, $errors);
    }

    /**
     * @Route("/{id}.{_format}", name="manuel_translation_api_update", methods={"PUT"})
     */
    public function updateAction(Request $request, Translation $translation)
    {
        $translation = $this->serializer->deserialize(
            $request->getContent(),
            Translation::class,
            'json',
            ['object_to_populate' => $translation]
        );

        if (!count($errors = $this->validator->validate($translation))) {
            $this->translationRepository->saveTranslation($translation);
        }

        return $this->responseGenerator->forOne($request, $translation, $errors);
    }

    /**
     * @Route("/domains.{_format}", name="manuel_translation_api_get_domains", methods={"GET"})
     */
    public function getDomainsAction(Request $request)
    {
        $domains = $this->translationRepository->getExistentDomains();

        return $this->responseGenerator->forOne($request, $domains);
    }
}