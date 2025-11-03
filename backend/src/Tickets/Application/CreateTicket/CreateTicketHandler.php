<?php

namespace App\Tickets\Application\CreateTicket;

use App\Events\Domain\EventId;
use App\Organisers\Domain\OrganiserId;
use App\Tickets\Domain\Ticket;
use App\Tickets\Domain\TicketId;
use App\Tickets\Domain\TicketRepository;

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
