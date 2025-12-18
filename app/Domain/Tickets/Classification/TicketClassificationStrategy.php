<?php

namespace App\Domain\Tickets\Classification;

interface TicketClassificationStrategy
{
    public function classify($tickets);
}
