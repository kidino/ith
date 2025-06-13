<?php

namespace App\Http\Controllers;

use App\Models\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketStatusController extends Controller
{
    public function __construct()
    {
        if (!Auth::user() || Auth::user()->user_type !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }

    public function index()
    {
        $statuses = TicketStatus::orderBy('id')->paginate(10);
        return view('ticket_status.index', compact('statuses'));
    }

    public function create()
    {
        return view('ticket_status.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:32',
            'default_status' => 'nullable|boolean',
        ]);
        $data['default_status'] = $request->has('default_status') ? (bool)$request->default_status : false;

        if ($data['default_status']) {
            TicketStatus::query()->update(['default_status' => false]);
        }

        TicketStatus::create($data);
        return redirect()->route('ticket-statuses.index')->with('success', 'Status created.');
    }

    public function show(TicketStatus $ticket_status)
    {
        return view('ticket_status.show', compact('ticket_status'));
    }

    public function edit(TicketStatus $ticket_status)
    {
        return view('ticket_status.edit', compact('ticket_status'));
    }

    public function update(Request $request, TicketStatus $ticket_status)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:32',
            'default_status' => 'nullable|boolean',
        ]);

        $data['default_status'] = $request->has('default_status') ? (bool)$request->default_status : false;

        if ($data['default_status']) {
            $ostatus = TicketStatus::query()->where('id', '!=', $ticket_status->id)->first();
            if ($ostatus) {
                $ostatus->update(['default_status' => false]);
            }
        }

        $ticket_status->update($data);
        return redirect()->route('ticket-statuses.index')->with('success', 'Status updated.');
    }

    public function destroy(TicketStatus $ticket_status)
    {
        $ticket_status->delete();
        return redirect()->route('ticket-statuses.index')->with('success', 'Status deleted.');
    }
}
