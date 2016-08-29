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


/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 * 
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/", name="manuel_translation_api_list")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $query = $this->get('manuel_translation.repository')
            ->getAllQueryBuilder();

        $data = $query->getQuery()->getResult();
        $data = $this->get('serializer')->normalize($data, 'array');

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}", name="manuel_translation_api_post")
     * @Method("POST")
     */
    public function postAction(Request $request, Translation $translation)
    {
        $translation = $this->get('serializer')->deserialize(
            $request->getContent(), 
            Translation::class, 
            'json', 
            ['object_to_populate' => $translation]
        );

        $this->get('manuel_translation.repository')->saveTranslation($translation);

        return new JsonResponse(
            $this->get('serializer')->normalize($translation, 'array')
        );
    }
}