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
        $query = $this->getDoctrine()
            ->getRepository('ManuelTranslationBundle:Translation')
            ->getAllQueryBuilder();

        $data = $query->getQuery()->getArrayResult();

        //dump($data);die;

        return new JsonResponse($data);
    }
}