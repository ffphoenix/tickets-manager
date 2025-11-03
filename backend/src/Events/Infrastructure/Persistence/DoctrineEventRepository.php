<?php

namespace App\Events\Infrastructure\Persistence;

use App\Entity\Event as OrmEvent;
use App\Events\Domain\Event as DomainEvent;
use App\Events\Domain\EventId;
use App\Events\Domain\EventRepository;
use App\Organisers\Domain\OrganiserId;
use App\Shared\Domain\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineEventRepository implements EventRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function save(DomainEvent $event): void
    {
        $repo = $this->em->getRepository(OrmEvent::class);
        /** @var OrmEvent|null $entity */
        $entity = $repo->find((string) $event->id());
        if (!$entity) {
            $entity = new OrmEvent(
                (string) $event->id(),
                $event->name(),
                (string) $event->organiserId(),
                $event->startAt(),
                $event->endAt(),
                $event->createdAt(),
            );
        } else {
            $entity->setName($event->name());
            $entity->setStartAt($event->startAt());
            $entity->setEndAt($event->endAt());
        }

        $this->em->persist($entity);
        $this->em->flush();
    }

    public function get(EventId $id): DomainEvent
    {
        /** @var OrmEvent|null $entity */
        $entity = $this->em->find(OrmEvent::class, (string) $id);
        if (!$entity) {
            throw new NotFound('Event not found: ' . (string) $id);
        }

        return new DomainEvent(
            EventId::fromString($entity->getId()),
            $entity->getName(),
            OrganiserId::fromString($entity->getOrganiserId()),
            $entity->getStartAt(),
            $entity->getEndAt(),
            $entity->getCreatedAt(),
        );
    }

    public function exists(EventId $id): bool
    {
        return (bool) $this->em->getRepository(OrmEvent::class)->find((string) $id);
    }
}
