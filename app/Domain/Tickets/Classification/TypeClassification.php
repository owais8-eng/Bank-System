<?php

namespace App\Domain\Tickets\Classification;

use App\Models\Ticket;

class TypeClassification implements TicketClassificationStrategy
{
    public function classify($tickets)
    {
        return $tickets->sortBy('type');
    }
}
