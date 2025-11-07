<?php

namespace App\Events\Infrastructure\Persistence;

use App\Events\Domain\Entity\Event as DomainEvent;
use App\Events\Domain\EventRepository;
use App\Events\Domain\ValueObject\EventId;
use App\Shared\Domain\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineEventRepository implements EventRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function save(DomainEvent $event): void
    {
        // DomainEvent is the Doctrine entity; just persist it directly
        $this->em->persist($event);
        $this->em->flush();
    }

    public function get(EventId $id): DomainEvent
    {
        /** @var DomainEvent|null $entity */
        $entity = $this->em->find(DomainEvent::class, $id);
        if (!$entity) {
            throw new NotFound('Event not found: ' . (string) $id);
        }

        return $entity;
    }

    public function exists(EventId $id): bool
    {
        return (bool) $this->em->getRepository(DomainEvent::class)->find($id);
    }

    public function delete(EventId $id): void
    {
        /** @var DomainEvent|null $entity */
        $entity = $this->em->find(DomainEvent::class, $id);
        if (!$entity) {
            throw new NotFound('Event not found: ' . (string) $id);
        }
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * @return DomainEvent[]
     */
    public function all(): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(DomainEvent::class, 'e')
            ->orderBy('e.startAt', 'ASC');

        /** @var DomainEvent[] $rows */
        $rows = $qb->getQuery()->getResult();

        return $rows;
    }
}
