<?php

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\PhoneNumber;
use App\Enum\PhoneNumberType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

final class PhoneNumberRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $registry)
    {
        parent::__construct($registry, new ClassMetadata(PhoneNumber::class));
    }

    public function prepare(PhoneNumberType $phoneNumberType, string $number, Contact $contact): ?PhoneNumber
    {
        $phoneNumber = new PhoneNumber();
        $phoneNumber->setType($phoneNumberType);
        $phoneNumber->setNumber($number);
        $phoneNumber->setContact($contact);

        $this->getEntityManager()->persist($phoneNumber);

        return $phoneNumber;
    }

    public function deleteForContact(Contact $contact): void
    {
        $this->createQueryBuilder('phoneNumbers')
            ->delete()
            ->where('phoneNumbers.contact = :contact_id')
            ->setParameter('contact_id', $contact->getId())
            ->getQuery()
            ->execute();
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

}