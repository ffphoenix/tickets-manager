<?php

namespace App\Tickets\Domain;

use App\Shared\Domain\Exception\NotFound;

interface TicketRepository
{
    public function save(Ticket $ticket): void;

    /**
     * @throws NotFound
     */
    public function get(TicketId $id): Ticket;

    public function exists(TicketId $id): bool;
}
