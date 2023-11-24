<?php

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\Contact;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

final class AttributeRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $registry)
    {
        parent::__construct($registry, new ClassMetadata(Attribute::class));
    }

    public function prepare(string $name, string $value, Contact $contact): ?Attribute
    {
        $attribute = new Attribute();
        $attribute->setName($name);
        $attribute->setValue($value);
        $attribute->setContact($contact);

        $this->getEntityManager()->persist($attribute);

        return $attribute;
    }

    public function deleteForContact(Contact $contact): void
    {
        $this->createQueryBuilder('attribute')
            ->delete()
            ->where('attribute.contact = :contact_id')
            ->setParameter('contact_id', $contact->getId())
            ->getQuery()
            ->execute();
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }
}