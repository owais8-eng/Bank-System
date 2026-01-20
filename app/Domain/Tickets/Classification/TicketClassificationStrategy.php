<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Classification;

interface TicketClassificationStrategy
{
    public function classify($tickets);
}
