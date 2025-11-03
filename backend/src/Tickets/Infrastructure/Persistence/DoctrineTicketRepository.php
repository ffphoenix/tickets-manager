<?php

namespace App\Tickets\Infrastructure\Persistence;

use App\Events\Domain\EventId;
use App\Organisers\Domain\OrganiserId;
use App\Shared\Domain\Exception\NotFound;
use App\Tickets\Domain\Ticket as DomainTicket;
use App\Tickets\Domain\TicketId;
use App\Tickets\Domain\TicketRepository;
use App\Tickets\Domain\TicketStatus;
use App\Entity\Ticket as OrmTicket;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTicketRepository implements TicketRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function save(DomainTicket $ticket): void
    {
        $repo = $this->em->getRepository(OrmTicket::class);
        $entity = $repo->find((string) $ticket->id());
        if (!$entity) {
            $entity = new OrmTicket(
                (string) $ticket->id(),
                (string) $ticket->eventId(),
                (string) $ticket->organiserId(),
                $ticket->priceCents(),
                $ticket->status()->value,
                $ticket->createdAt(),
            );
        } else {
            $entity->setPriceCents($ticket->priceCents());
            $entity->setStatus($ticket->status()->value);
        }

        $this->em->persist($entity);
        $this->em->flush();
    }

    public function get(TicketId $id): DomainTicket
    {
        $entity = $this->em->find(OrmTicket::class, (string) $id);
        if (!$entity) {
            throw new NotFound('Ticket not found: ' . (string) $id);
        }

        return new DomainTicket(
            TicketId::fromString($entity->getId()),
            EventId::fromString($entity->getEventId()),
            OrganiserId::fromString($entity->getOrganiserId()),
            $entity->getPriceCents(),
            TicketStatus::from($entity->getStatus()),
            $entity->getCreatedAt(),
        );
    }

    public function exists(TicketId $id): bool
    {
        return (bool) $this->em->getRepository(OrmTicket::class)->find((string) $id);
    }
}
