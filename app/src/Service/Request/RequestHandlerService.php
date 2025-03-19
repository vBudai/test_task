<?php

namespace App\Service\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class RequestHandlerService
{
    protected const VALIDATION_GROUPS = ['POST', 'PUT'];

    public function __construct(
        private RequestValidatorService $requestValidator,
        private SerializerInterface $serializer
    ) {
    }

    public function handleRequest(Request $request, string $entityClass): object | array
    {
        if ($contentTypeError = $this->requestValidator->validateContentType($request)) {
            return $contentTypeError;
        }

        $entity = $this->serializer->deserialize($request->getContent(), $entityClass, 'json');

        $group = in_array($request->getMethod(), self::VALIDATION_GROUPS) ? $request->getMethod() : '';

        if ($validationError = $this->requestValidator->validateEntity($entity, $group)) {
            return $validationError;
        }

        return $entity;
    }
}