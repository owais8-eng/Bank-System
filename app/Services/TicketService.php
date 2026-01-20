<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Tickets\Classification\TicketClassificationStrategy;
use App\Models\Ticket;

class TicketService
{
    protected TicketClassificationStrategy $strategy;

    public function __construct(TicketClassificationStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function setStrategy(TicketClassificationStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function getTicketsClassified()
    {
        $tickets = Ticket::all();

        return $this->strategy->classify($tickets);
    }
}
