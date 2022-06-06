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

use Exception;
use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;
use ManuelAguirre\Bundle\TranslationBundle\Http\ResponseGenerator;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function array_udiff;
use function array_values;
use function json_decode;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
#[Route("/api")]
#[IsGranted('manage_translations')]
#[AsController]
class ApiController extends AbstractController
{
    public function __construct(
        private ResponseGenerator $responseGenerator,
        private TranslationRepository $translationRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route("", name: "manuel_translation_api_list", methods: "GET")]
    public function index(Request $request): Response
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

        return $this->json($data, Response::HTTP_OK, [
            'X-Count' => count($data),
        ]);
    }

    #[Route("", name: "manuel_translation_api_create", methods: "POST")]
    public function create(Request $request): Response
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

    #[Route("/{id}", name: "manuel_translation_api_update", methods: "PUT")]
    public function update(Request $request, Translation $translation): Response
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

    #[Route("/get-missing/", name: "manuel_translation_api_missing_items", methods: "post")]
    public function getMissing(
        Request $request,
        TranslationRepository $repository
    ): Response {
        $search = json_decode($request->getContent(), true);

        $items = $repository->findByCodesAndDomains($search);

        $missing = array_udiff($search, $items, fn($a, $b) => $a <=> $b);

        return $this->json(array_values($missing));
    }
}