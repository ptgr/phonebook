<?php

namespace App\Entity;

use App\Enum\PhoneNumberType;
use App\Repository\PhoneNumberRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhoneNumberRepository::class)]
#[ORM\Table(name: 'phone_number')]
class PhoneNumber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $number = null;
    
    #[ORM\Column(enumType: PhoneNumberType::class)]
    private PhoneNumberType $type;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Contact $contact;

    public function __construct()
    {
        $this->type = PhoneNumberType::PERSONAL;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getType(): PhoneNumberType
    {
        return $this->type;
    }

    public function setType(PhoneNumberType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function setContact(Contact $contact): self
    {
        $this->contact = $contact;
        return $this;
    }
}