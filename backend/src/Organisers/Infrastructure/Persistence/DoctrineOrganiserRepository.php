<?php

namespace App\Organisers\Infrastructure\Persistence;

use App\Entity\Organiser as OrmOrganiser;
use App\Organisers\Domain\Organiser as DomainOrganiser;
use App\Organisers\Domain\OrganiserId;
use App\Organisers\Domain\OrganiserRepository;
use App\Shared\Domain\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineOrganiserRepository implements OrganiserRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function save(DomainOrganiser $organiser): void
    {
        $repo = $this->em->getRepository(OrmOrganiser::class);
        /** @var OrmOrganiser|null $entity */
        $entity = $repo->find((string) $organiser->id());
        if (!$entity) {
            $entity = new OrmOrganiser(
                (string) $organiser->id(),
                $organiser->name(),
                $organiser->createdAt(),
            );
        } else {
            $entity->setName($organiser->name());
        }

        $this->em->persist($entity);
        $this->em->flush();
    }

    public function get(OrganiserId $id): DomainOrganiser
    {
        /** @var OrmOrganiser|null $entity */
        $entity = $this->em->find(OrmOrganiser::class, (string) $id);
        if (!$entity) {
            throw new NotFound('Organiser not found: ' . (string) $id);
        }

        return new DomainOrganiser(
            OrganiserId::fromString($entity->getId()),
            $entity->getName(),
            $entity->getCreatedAt(),
        );
    }

    public function exists(OrganiserId $id): bool
    {
        return (bool) $this->em->getRepository(OrmOrganiser::class)->find((string) $id);
    }
}
