<?php

namespace App\Organisers\Infrastructure\Persistence;

use App\Organisers\Domain\Entity\Organiser as DomainOrganiser;
use App\Organisers\Domain\OrganiserRepository;
use App\Organisers\Domain\ValueObject\OrganiserId;
use App\Shared\Domain\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineOrganiserRepository implements OrganiserRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function save(DomainOrganiser $organiser): void
    {
        $this->em->persist($organiser);
        $this->em->flush();
    }

    public function get(OrganiserId $id): DomainOrganiser
    {
        $entity = $this->em->find(DomainOrganiser::class, $id);
        if (!$entity) {
            throw new NotFound('Event not found: ' . (string) $id);
        }

        return $entity;
    }

    public function exists(OrganiserId $id): bool
    {
        return (bool) $this->em->getRepository(DomainOrganiser::class)->find((string) $id);
    }

    /**
     * Delete organiser by id
     *
     * @throws NotFound
     */
    public function delete(OrganiserId $id): void
    {
        /** @var DomainOrganiser|null $entity */
        $entity = $this->em->find(DomainOrganiser::class, (string) $id);
        if (!$entity) {
            throw new NotFound('Organiser not found: ' . (string) $id);
        }
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * @return DomainOrganiser[]
     */
    public function all(): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(DomainOrganiser::class, 'e')
            ->orderBy('e.createdAt', 'ASC');

        /** @var DomainOrganiser[] $rows */
        $rows = $qb->getQuery()->getResult();

        return $rows;
    }
}
