<?php

namespace App\Tickets\Domain;

enum TicketStatus: string
{
    case CREATED = 'created';
    case RESERVED = 'reserved';
    case SOLD = 'sold';
    case CANCELLED = 'cancelled';
}
