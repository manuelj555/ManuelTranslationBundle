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
        $query = $this->get('manuel_translation.repository')->getAllQueryBuilder(
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
        $data = $this->get('serializer')->normalize($data, 'array');

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
        $translation = $this->get('serializer')->deserialize(
            $request->getContent(), 
            Translation::class, 
            'json' //, 
            //['object_to_populate' => $translation]
        );

        $this->get('manuel_translation.repository')->saveTranslation($translation);

        return new JsonResponse(
            $this->get('serializer')->normalize($translation, 'array')
        );
    }

    /**
     * @Route("/{id}", name="manuel_translation_api_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, Translation $translation)
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

    /**
     * @Route("/domains", name="manuel_translation_api_get_domains")
     * @Method("GET")
     */
    public function getDomainsAction()
    {
        $domains = $this->get('manuel_translation.repository')->getExistentDomains();

        return new JsonResponse($domains);
    }
}