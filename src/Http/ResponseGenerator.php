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
use function count;
use function json_encode;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class ResponseGenerator
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function forOne(
        Request $request,
        Translation $translation,
        ConstraintViolationListInterface $errorList = null
    ): Response {
        $data = $this->serializer->serialize($translation, 'json');
        $errors = $this->errorsToArray($errorList);

        return new Response(
            0 === count($errors) ? $data : json_encode($errors),
            count($errors) ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK,
        );
    }

    private function errorsToArray(ConstraintViolationListInterface $errorList = null): array
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