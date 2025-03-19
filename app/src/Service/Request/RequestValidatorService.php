<?php

namespace App\Service\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestValidatorService
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validateContentType(Request $request): array
    {
        if ($request->headers->get('Content-Type') !== 'application/json') {
            return [
                'code' => Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
                'error' => 'Content-Type must be application/json'
            ];
        }

        return [];
    }

    public function validateEntity($entity, string $group = ''): array
    {
        $validationResult = $this->validator->validate($entity, null, $group);
        if (count($validationResult) > 0) {
            $errors = [];
            foreach ($validationResult as $error) {
                $errors[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }
            return $errors;
        }

        return [];
    }
}