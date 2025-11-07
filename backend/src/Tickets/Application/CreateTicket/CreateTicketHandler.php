<?php

namespace App\Tickets\Application\CreateTicket;

use App\Events\Domain\ValueObject\EventId;
use App\Organisers\Domain\ValueObject\OrganiserId;
use App\Tickets\Domain\Entity\Ticket;
use App\Tickets\Domain\TicketRepository;
use App\Tickets\Domain\ValueObject\TicketId;

final class CreateTicketHandler
{
    public function __construct(private readonly TicketRepository $tickets)
    {
    }

    public function __invoke(CreateTicket $command): void
    {
        $ticket = Ticket::create(
            TicketId::fromString($command->ticketId),
            EventId::fromString($command->eventId),
            OrganiserId::fromString($command->organiserId),
            $command->priceCents,
        );

        $this->tickets->save($ticket);
    }
}
