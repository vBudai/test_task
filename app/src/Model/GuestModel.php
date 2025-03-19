<?php

namespace App\Model;

use App\Entity\Guest;
use App\Repository\GuestRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GuestModel
{
    public function __construct(private GuestRepository $repository)
    {
    }

    public function createGuest(Guest $guest): void
    {
        $this->repository->create($guest);
    }

    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    public function getById(int $id): ?Guest
    {
        return $this->repository->find($id) ?? throw new NotFoundHttpException();
    }

    /**
     * @throws UniqueConstraintViolationException
     */
    public function updateAllFields(int $id, Guest $guest): void
    {
        if(!$existingGuest = $this->repository->find($id)){
            throw new NotFoundHttpException();
        }

        $this->copyFields($guest, $existingGuest, true);
        $this->repository->update($guest);
    }

    /**
     * @throws UniqueConstraintViolationException
     */
    public function updateSomeFields(int $id, Guest $guest): void
    {
        if(!$existingGuest = $this->repository->find($id)){
            throw new NotFoundHttpException();
        }

        $this->copyFields($guest, $existingGuest, false);
        $this->repository->update();
    }

    /**
     * @throws NotFoundHttpException
     */
    public function deleteUserById(int $id): void
    {
        $guest = $this->repository->find($id);
        if(!$guest){
            throw new NotFoundHttpException();
        }

        $this->repository->delete($guest);
    }

    private function copyFields(Guest $newGuest, Guest $existingGuest, bool $copyEmptyFields): void
    {
        $fields = $newGuest->returnFieldsNames();

        foreach ($fields as $field) {
            $getter = 'get' . ucfirst($field);
            $setter = 'set' . ucfirst($field);

            if (method_exists($newGuest, $getter) && method_exists($existingGuest, $setter)) {
                $value = $newGuest->$getter();
                if (!$value && $copyEmptyFields || $value) {
                    $existingGuest->$setter($value);
                }
            }
        }
    }
}