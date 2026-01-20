<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Classification;

class PriorityClassification implements TicketClassificationStrategy
{
    public function classify($tickets)
    {
        return $tickets->sortByDesc('priority');
    }
}
