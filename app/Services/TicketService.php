<?php

namespace App\Services;

use App\Models\Ticket;
use App\Domain\Tickets\Classification\TicketClassificationStrategy;

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
