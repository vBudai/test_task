<?php

namespace App\Service\Response;

use App\Entity\Guest;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiGuestResponse
{
    public const MESSAGE_GUEST_CREATED = 'Guest has been created';
    public const MESSAGE_GUEST_UPDATED = 'Guest has been updated';
    public const MESSAGE_UNIQUE_CONFLICT = 'Guest with this phone or email already exists';
    public const MESSAGE_GUEST_NOT_FOUND = 'Guest not found';

    public static function guestValidationErrorResponse($errors): JsonResponse
    {
        return new JsonResponse([
            'code' => Response::HTTP_BAD_REQUEST,
            'data' => $errors
        ], Response::HTTP_BAD_REQUEST);
    }

    public static function createSuccessResponse($data): JsonResponse
    {
        return new JsonResponse([
            'code' => Response::HTTP_CREATED,
            'data' => self::MESSAGE_GUEST_CREATED,
        ], Response::HTTP_CREATED);
    }

    public static function noContentResponse(): JsonResponse
    {
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }


    public static function readResponse($guests): JsonResponse
    {
        try {
            $serializer = new Serializer([new ObjectNormalizer()]);

            $normalizedData = $serializer->normalize($guests, 'array');

            return new JsonResponse([
                'code' => Response::HTTP_OK,
                'data' => $normalizedData
            ], Response::HTTP_OK);
        } catch (ExceptionInterface $e) {
            return self::normalizeGuestsErrorResponse($e);
        }
    }

    public static function guestNotFoundResponse(): JsonResponse
    {
        return new JsonResponse([
            'code' => Response::HTTP_NOT_FOUND,
            'data' => self::MESSAGE_GUEST_NOT_FOUND,
        ], Response::HTTP_NOT_FOUND);
    }

    public static function httpUniqueKeyConflictResponse(): JsonResponse
    {
        return new JsonResponse([
            'code' => Response::HTTP_CONFLICT,
            'data' => self::MESSAGE_UNIQUE_CONFLICT,
        ], Response::HTTP_CONFLICT);
    }

    public static function updatedSuccessResponse(): JsonResponse
    {
        return new JsonResponse([
            'code' => Response::HTTP_OK,
            'data' => self::MESSAGE_GUEST_UPDATED,
        ], Response::HTTP_OK);
    }

    public static function normalizeGuestsErrorResponse(Exception $error): JsonResponse
    {
        return new JsonResponse([
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'data' => $error->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}