<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Model\GuestModel;
use App\Service\Request\RequestHandlerService;
use App\Service\Response\ApiGuestResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class GuestController extends AbstractController
{

    public function __construct(private GuestModel $model, private RequestHandlerService $requestHandler){}

    #[Route('/api/v1/guest', name: 'guest_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data = $this->requestHandler->handleRequest($request, Guest::class);
        if(!$data instanceof Guest){
            return ApiGuestResponse::guestValidationErrorResponse($data);
        }

        $this->model->createGuest($data);
        return ApiGuestResponse::createSuccessResponse($data);
    }

    #[Route('/api/v1/guests', name: 'guest_read_all', methods: ['GET'])]
    public function readAll(): Response
    {
        $guests = $this->model->getAll();
        if(!$guests){
            return ApiGuestResponse::noContentResponse();
        }

        return ApiGuestResponse::readResponse($guests);
    }

    #[Route('/api/v1/guest/{id}', name: 'guest_read_one', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readOne(int $id): Response
    {
        try{
            $guest = $this->model->getById($id);
        }
        catch (NotFoundHttpException $e){
            return ApiGuestResponse::guestNotFoundResponse($e);
        }

        return ApiGuestResponse::readResponse($guest);
    }

    #[Route('/api/v1/guest/{id}', name: 'guest_update_all_fields', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function updateAllFields(Request $request, int $id): Response
    {
        try {
            $data = $this->requestHandler->handleRequest($request, Guest::class);
            if(!$data instanceof Guest){
                return ApiGuestResponse::guestValidationErrorResponse($data);
            }

            $this->model->updateAllFields($id, $data);
        }
        catch (NotFoundHttpException $e){
            return ApiGuestResponse::noContentResponse();
        }
        catch (UniqueConstraintViolationException $e){
            return ApiGuestResponse::httpUniqueKeyConflictResponse();
        }

        return ApiGuestResponse::updatedSuccessResponse();
    }

    #[Route('/api/v1/guest/{id}', name: 'guest_update_some_fields', requirements: ['id' => '\d+'], methods: ['PATCH'])]
    public function updateSomeFields(Request $request, int $id): Response
    {
        try {
            $data = $this->requestHandler->handleRequest($request, Guest::class);
            if(!$data instanceof Guest){
                return ApiGuestResponse::guestValidationErrorResponse($data);
            }

            $this->model->updateSomeFields($id, $data);
        }
        catch (NotFoundHttpException $e){
            return ApiGuestResponse::guestNotFoundResponse($e);
        }
        catch (UniqueConstraintViolationException $e){
            ApiGuestResponse::httpUniqueKeyConflictResponse();
        }

        return ApiGuestResponse::updatedSuccessResponse();
    }

    #[Route('/api/v1/guest/{id}', name: 'guest_update', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try{
            $this->model->deleteUserById($id);
        }
        catch(NotFoundHttpException $e){
            return ApiGuestResponse::guestNotFoundResponse($e);
        }

        return ApiGuestResponse::noContentResponse();
    }

}