<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketStatus;
use App\Models\Comment;
use App\Models\User;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['status', 'category', 'assignees'])->paginate(20);
        return view('ticket.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['comments.user', 'status', 'category', 'assignees']);
        $statuses = TicketStatus::all();
        return view('ticket.show', compact('ticket', 'statuses'));
    }

    public function updateStatus(Request $request, $ticketId)
    {
        $request->validate([
            'ticket_status_id' => 'required|exists:ticket_statuses,id',
        ]);

        $ticket = Ticket::findOrFail($ticketId);
        $ticket->ticket_status_id = $request->ticket_status_id;
        $ticket->save();

        return redirect()->route('tickets.show', $ticket)->with('success', 'Status updated.');
    }

    public function addComment(Request $request, $ticketId)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($ticketId);
        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);


    return redirect()->route('tickets.show', $ticket)->with('success', 'Comment added.');    

    }

    public function addAssignee(Request $request, $ticketId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        $ticket = Ticket::findOrFail($ticketId);
        $ticket->assignees()->syncWithoutDetaching([$request->user_id]);
        return redirect()->route('tickets.show', $ticket)->with('success', 'Assignee added.');
    }

    public function removeAssignee($ticketId, $userId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $ticket->assignees()->detach($userId);
        return redirect()->route('tickets.show', $ticket)->with('success', 'Assignee removed.');
    }
}
