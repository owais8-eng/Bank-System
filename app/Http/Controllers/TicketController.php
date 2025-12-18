<?php

namespace App\Http\Controllers;

use App\Domain\Tickets\Classification\PriorityClassification;
use App\Domain\Tickets\Classification\TypeClassification;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'customer') {
            $tickets = $user->tickets()->latest()->get();
        } else {

            $tickets = Ticket::latest()->get();
        }


        $strategy = $request->query('sort_by') === 'priority'
            ? new PriorityClassification()
            : new TypeClassification();

        $service = new TicketService($strategy);

        $classifiedTickets = $service->getTicketsClassified();

        return response()->json($classifiedTickets);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:inquiry,complaint',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $ticket = $request->user()->tickets()->create($validated);

        return response()->json($ticket, 201);
    }

    public function show(Ticket $ticket)
    {
        return response()->json($ticket->load('comments'));
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $validated = $request->validate(['comment' => 'required|string']);

        $comment = $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'comment' => $validated['comment'],
        ]);

        return response()->json($comment, 201);
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate(['status' => 'required|in:open,pending,closed']);
        $ticket->update(['status' => $validated['status']]);

        return response()->json($ticket);
    }
}
