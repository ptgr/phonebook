<?php

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\Contact;
use App\Entity\PhoneNumber;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

final class ContactRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $registry)
    {
        parent::__construct($registry, new ClassMetadata(Contact::class));
    }

    public function getWithRelations(?int $id = null, array $queryParameters = []): array
    {
        $supportedFilterParams = ['number' => 'phoneNumbers.number'];

        $queryBuilder = $this->createQueryBuilder('c')
            ->leftJoin(PhoneNumber::class, 'phoneNumbers', 'WITH', 'phoneNumbers.contact = c')
            ->leftJoin(Attribute::class, 'attribute', 'WITH', 'attribute.contact = c')
            ->addSelect('phoneNumbers')
            ->addSelect('attribute');

        if ($id !== null) {
            $queryBuilder->andWhere('c.id = :id')
                ->setParameter('id', $id);
        }

        foreach ($queryParameters as $key => $value) {
            if (!isset($supportedFilterParams[$key]))
                continue;

            $queryBuilder->andWhere($supportedFilterParams[$key] . ' LIKE :value')
                ->setParameter('value', '%' . $value);
        }

        return $queryBuilder->getQuery()->getScalarResult();
    }

    public function update(int $id, string $firstName, string $lastName): ?Contact
    {
        $contact = $this->find($id);
        $contact->setFirstName($firstName);
        $contact->setLastName($lastName);

        $this->save();

        return $contact;
    }

    public function prepare(string $firstName, string $lastName): ?Contact
    {
        $contact = new Contact();
        $contact->setFirstName($firstName);
        $contact->setLastName($lastName);

        $this->getEntityManager()->persist($contact);

        return $contact;
    }

    public function save(): void
    {
        $this->getEntityManager()->flush();
    }
}