<?php

namespace App\Domain\Tickets\Classification;

use App\Models\Ticket;

class PriorityClassification implements TicketClassificationStrategy
{
    public function classify($tickets)
    {
        return $tickets->sortByDesc('priority');
    }
}
