<?php

namespace App\Entity;

use App\Repository\GuestRepository;
use App\Service\Country\CountryFromPhoneService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GuestRepository::class)]
#[UniqueEntity(['phone'], message: 'Guest with this phone already exists!', groups: ['POST'])]
#[UniqueEntity(['email'], message: 'Guest with this email already exists!', groups: ['POST'])]
class Guest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'Missed required field', groups: ['POST', 'PUT'])]
    #[Assert\Length(max: 64, maxMessage: 'Field must not be longer than {{ limit }} characters.')]
    private ?string $name = null;

    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(message: 'Missed required field', groups: ['POST', 'PUT'])]
    #[Assert\Length(max: 64, maxMessage: 'Field must not be longer than {{ limit }} characters')]
    private ?string $surname = null;

    #[ORM\Column(length: 17, unique: true)]
    #[Assert\NotBlank(message: 'Missed required field', groups: ['POST', 'PUT'])]
    #[Assert\Length(
        min: 11,
        max: 17,
        minMessage: 'Field "phone" must be longer than {{ limit }} characters.',
        maxMessage: 'Field "phone" must not be longer than {{ limit }} characters.')
    ]
    #[Assert\Regex(
        pattern: '/^(?=(?:\D*\d){11})\+?\d{1,3}[-.\s]?\(?\d{1,4}\)?[-.\s]?\d{1,4}[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/',
        message: 'Invalid format')
    ]
    private ?string $phone = null;

    #[ORM\Column(length: 64)]
    #[Assert\Length(max: 64, maxMessage: 'Field "country" must not be longer than {{ limit }} characters.')]
    #[Assert\Regex(
        pattern: '/^[\p{Cyrillic}\s\-]+$/u',
        message: 'Field must contain only letters, spaces and hyphens'
    )]
    private ?string $country = null;

    #[ORM\Column(length: 128, unique: true, nullable: true)]
    #[Assert\Length(max: 128, maxMessage: 'Field must not be longer than {{ limit }} characters')]
    #[Assert\Email(message: 'Invalid format')]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        if(!$this->country){
            $this->country = CountryFromPhoneService::extract($phone);
        }

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function returnFieldsNames(): array
    {
        return array_keys(get_object_vars($this));
    }
}
