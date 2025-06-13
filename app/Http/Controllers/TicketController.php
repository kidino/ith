<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketStatus;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Category;
use App\Models\Department;
use App\Models\Vendor;

class TicketController extends Controller
{
    protected function applySorting(Builder $query)
    {
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');

        switch ($sort) {
            case 'title':
                $query->orderBy('title', $direction);
                break;
            case 'status':
                $query->leftJoin('ticket_statuses', 'tickets.ticket_status_id', '=', 'ticket_statuses.id')
                      ->orderBy('ticket_statuses.name', $direction)
                      ->select('tickets.*');
                break;
            case 'category':
                $query->leftJoin('categories', 'tickets.category_id', '=', 'categories.id')
                      ->orderBy('categories.name', $direction)
                      ->select('tickets.*');
                break;
            case 'department':
                $query->leftJoin('departments', 'tickets.department_id', '=', 'departments.id')
                      ->orderBy('departments.name', $direction)
                      ->select('tickets.*');
                break;
            case 'user':
                $query->leftJoin('users as ticket_users', 'tickets.user_id', '=', 'ticket_users.id')
                      ->orderBy('ticket_users.name', $direction)
                      ->select('tickets.*');
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $direction);
                break;
        }
    }

    public function index()
    {
        $userType = Auth::user()->user_type ?? null;
        if ($userType === 'user') {
            return redirect()->route('tickets.mine');
        }
        if ($userType === 'vendor') {
            return redirect()->route('tickets.tasks');
        }

        $tickets = Ticket::with(['status', 'category', 'user.department', 'assignees']);
        if ($statusId = request('status')) {
            $tickets->where('ticket_status_id', $statusId);
        }
        if ($categoryId = request('category')) {
            $tickets->where('category_id', $categoryId);
        }
        $this->applySorting($tickets);
        $tickets = $tickets->paginate(10)->withQueryString();
        $activeTab = 'all';
        return view('ticket.index', compact('tickets', 'activeTab'));
    }

    public function show(Request $request, Ticket $ticket)
    {
        $ticket->load(['comments.user', 'status', 'category', 'assignees']);
        $statuses = TicketStatus::all();
        // Only users with user_type 'it' or 'vendor' for assignee selection
        $assigneeCandidates = User::whereIn('user_type', ['it', 'vendor'])->get();
        return view('ticket.show', compact('ticket', 'statuses', 'assigneeCandidates'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'ticket_status_id' => 'required|exists:ticket_statuses,id',
        ]);

        $ticket->ticket_status_id = $request->ticket_status_id;
        $ticket->save();

        return redirect()->route('tickets.show', $ticket)->with('success', 'Status updated.');
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);


    return redirect()->route('tickets.show', $ticket)->with('success', 'Comment added.');    

    }

    public function addAssignee(Request $request, Ticket $ticket)
    {
        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if (!$user || !in_array($user->user_type, ['it', 'vendor'])) {
                        $fail('The selected user is not eligible to be an assignee.');
                    }
                }
            ],
        ]);
        $ticket->assignees()->syncWithoutDetaching([$request->user_id]);
        return redirect()->route('tickets.show', $ticket)->with('success', 'Assignee added.');
    }

    public function removeAssignee(Ticket $ticket, $userId)
    {
        $ticket->assignees()->detach($userId);
        return redirect()->route('tickets.show', $ticket)->with('success', 'Assignee removed.');
    }

    public function myTickets()
    {
        $tickets = Ticket::with(['status', 'category', 'user.department', 'assignees'])
            ->where('user_id', Auth::id());
        if ($statusId = request('status')) {
            $tickets->where('ticket_status_id', $statusId);
        }
        if ($categoryId = request('category')) {
            $tickets->where('category_id', $categoryId);
        }
        $this->applySorting($tickets);
        $tickets = $tickets->paginate(10)->withQueryString();
        $activeTab = 'my';
        return view('ticket.index', compact('tickets', 'activeTab'));
    }

    public function myTasks()
    {
        $tickets = Ticket::with(['status', 'category', 'user.department', 'assignees'])
            ->whereHas('assignees', function ($q) {
                $q->where('users.id', Auth::id());
            });
        if ($statusId = request('status')) {
            $tickets->where('ticket_status_id', $statusId);
        }
        if ($categoryId = request('category')) {
            $tickets->where('category_id', $categoryId);
        }
        $this->applySorting($tickets);
        $tickets = $tickets->paginate(10)->withQueryString();
        $activeTab = 'tasks';
        return view('ticket.index', compact('tickets', 'activeTab'));
    }

    public function create()
    {
        $categories = Category::all();
        $departments = Department::all();
        $vendors = Vendor::all();
        $statuses = TicketStatus::all();
        return view('ticket.create', compact('categories', 'departments', 'vendors', 'statuses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data['user_id'] = auth()->id();
        $data['ticket_status_id'] = TicketStatus::where('default_status', true)->value('id');
        $ticket = Ticket::create($data);
        return redirect()->route('tickets.mine', $ticket)->with('success', 'Ticket created.');
    }

    public function updateCategory(Request $request, $ticketId)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);
        $ticket = Ticket::findOrFail($ticketId);
        $ticket->category_id = $request->category_id;
        $ticket->save();

        return redirect()->route('tickets.show', $ticket)->with('success', 'Category updated.');
    }
}
