<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Classification;

class TypeClassification implements TicketClassificationStrategy
{
    public function classify($tickets)
    {
        return $tickets->sortBy('type');
    }
}
