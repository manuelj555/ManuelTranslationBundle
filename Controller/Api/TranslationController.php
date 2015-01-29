<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Controller\Api;

use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/translations")
 */
class TranslationController extends Controller
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $request = $container->get('request_stack')->getCurrentRequest();

        if (!$request->headers->has('api-key')) {
            throw $this->createAccessDeniedException('Api Key not Found');
        }

        $apiKey = $container->getParameter('manuel_translation.server.api_key');

        if($request->headers->get('api-key') != $apiKey){
            throw $this->createAccessDeniedException('Invalid Credentials!');
        }
    }

    /**
     * @Route("/get-all")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getAllAction()
    {
        $items = $this->get('manuel_translation.translations_repository')->getAll();

        $result = array();

        foreach ($items as $item) {
            $result[$item['domain']][$item['code']] = $item;
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/get-all-changed")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getAllChangedAction()
    {
        $items = $this->get('manuel_translation.translations_repository')->getAllChanged();

        $result = array();

        foreach ($items as $item) {
            $result[$item['domain']][$item['code']] = $item;
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/save")
     * @Method({"POST", "PUT"})
     * @return JsonResponse
     */
    public function postAction(Request $request)
    {
        $post = $request->request->all();

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $repository = $this->get('manuel_translation.translations_repository');

        $entity = $repository->findOneBy(array(
            'domain' => $post['domain'],
            'code' => $post['code'],
        ));

        if (!$entity) {
            $entity = new Translation($post['code'], $post['domain']);
            $entity->setNew($post['new']);
            $entity->setActive($post['active']);
            $entity->setAutogenerated($post['autogenerated']);
        }

        if ($post['version'] < $entity->getVersion() and !isset($post['force'])) {
            return new Response(sprintf('"%s" in "%s" Out of Date', $post['code'], $post['domain']), Response::HTTP_BAD_REQUEST);
        }

        foreach ($post['values'] as $locale => $value) {
            $entity->setValue($locale, $value);
        }

        $entity->setVersion($post['version'] + 1);
        $entity->setLocalEditions(0);
        $entity->setServerEditions(0);
        $entity->setIsChanged(false);
        $entity->setFiles($post['files']);

        $em->persist($entity);
        $em->flush();

        return new Response($entity->getVersion());
    }

    /**
     * @Route("/mark-updated")
     * @Method({"POST", "PUT"})
     * @return JsonResponse
     */
    public function markUpdatedAction(Request $request)
    {
        $post = $request->request->all();
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        /* @var $repository TranslationRepository */
        $repository = $this->get('manuel_translation.translations_repository');

        $entity = $repository->findOneBy(array(
            'domain' => $post['domain'],
            'code' => $post['code'],
        ));

        if (!$entity) {
            throw $this->createNotFoundException(sprintf('"%s" in "%s" NOT FOUND', $post['code'], $post['domain']));
        }

        $entity->setLocalEditions(0);

        $em->persist($entity);
        $em->flush();

        return new Response($entity->getVersion());
    }

    /**
     * @Route("/generate-backup")
     * @Method({"POST", "PUT"})
     * @return JsonResponse
     */
    public function generateBackupAction()
    {
        $this->get('manuel_translation.translation_manager')->generateBackup();

        return new Response('Ok');
    }

    /**
     * @Route("/find")
     * @Method({"GET"})
     * @return JsonResponse
     */
    public function findAction(Request $request)
    {
        $code = $request->get('code');
        $domain = $request->get('domain');

        $data = $this->get('manuel_translation.translations_repository')
            ->getOneArrayByCodeAndDomain($code, $domain);

        if (!$data) {
            throw $this->createNotFoundException();
        }

        return new JsonResponse($data);
    }

}