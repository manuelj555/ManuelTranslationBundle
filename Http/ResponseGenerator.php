<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Http;

use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class ResponseGenerator
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * JsonResponseGenerator constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function forOne(
        Request $request, 
        Translation $translation, 
        ConstraintViolationListInterface $errorList = null
    ) {
        $data = $this->serializer->serialize($translation, $request->getRequestFormat());
        $errors = $this->errorsToArray($errorList);

        return new Response(
            $data,
            count($errors) ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK,
            ['errors' => json_encode($errors)]
        );
    }

    public function forAll(Request $request, $translations, array $headers = [])
    {
        $data = $this->serializer->serialize($translations, $request->getRequestFormat());

        return new Response($data, Response::HTTP_OK, $headers);
    }

    private function errorsToArray(ConstraintViolationListInterface $errorList = null)
    {
        if (null === $errorList || 0 === count($errorList)) {
            return [];
        }

        $errors = [];

        /** @var ConstraintViolationInterface $item */
        foreach ($errorList as $item) {
            $errors[$item->getPropertyPath()][] = $item->getMessage();
        }

        return $errors;
    }
}