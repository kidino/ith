<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketStatus;
use App\Models\Comment;
use App\Models\User;
use App\Rules\ValidAssignee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Traits\ManagesTicketSorting;
use App\Models\Category;
use App\Models\Department;
use App\Models\Vendor;
use App\Notifications\TicketCreated;
use App\Notifications\TicketUpdated;
use App\Services\TicketAutomationService;

class TicketController extends Controller
{
    use ManagesTicketSorting;

    protected TicketAutomationService $automationService;

    public function __construct(TicketAutomationService $automationService)
    {
        $this->automationService = $automationService;
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

        Gate::authorize('viewAny', Ticket::class);

        $tickets = Ticket::with(['status', 'category', 'user.department', 'assignees']);
        $tickets->filterByStatus(request('status'))
                ->filterByCategory(request('category'));
        $this->applySorting($tickets);
        $tickets = $tickets->paginate(10)->withQueryString();
        $activeTab = 'all';
        $viewStatuses = TicketStatus::orderBy('name')->pluck('name', 'id');
        $viewCategories = Category::orderBy('name')->pluck('name', 'id');
        return view('ticket.index', compact('tickets', 'activeTab', 'viewStatuses', 'viewCategories'));
    }

    public function show(Request $request, Ticket $ticket)
    {
        Gate::authorize('view', $ticket);
        $ticket->load([
            'comments.user', // Existing
            'status',        // Existing
            'category',      // Existing
            'assignees' => function ($query) { // Modified to include vendor for assignees
                $query->with('vendor');
            },
            'user.department' // Added to load ticket's user and their department
        ]);
        $statuses = TicketStatus::orderBy('name')->pluck('name', 'id');
        // Only users with user_type 'it' or 'vendor' for assignee selection
        $assigneeCandidates = User::whereIn('user_type', ['it', 'vendor'])->orderBy('name')->pluck('name', 'id');
        $viewCategories = Category::orderBy('name')->pluck('name', 'id');
        return view('ticket.show', compact('ticket', 'statuses', 'assigneeCandidates', 'viewCategories'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        Gate::authorize('updateStatus', $ticket);
        $request->validate([
            'ticket_status_id' => 'required|exists:ticket_statuses,id',
        ]);

        // Check if trying to set to resolved - only admin can do this
        $newStatus = TicketStatus::find($request->ticket_status_id);
        if ($newStatus && $newStatus->name === 'resolved') {
            Gate::authorize('resolveTicket', $ticket);
        }

        $oldStatus = $ticket->status->name ?? 'unknown';
        $ticket->ticket_status_id = $request->ticket_status_id;
        $ticket->save();
        
        // Load relationships for notification
        $ticket->load(['user', 'category', 'status', 'assignees']);
        
        // Apply automation rules
        $this->automationService->handleStatusChange($ticket, $oldStatus, auth()->user());
        
        // Send notifications to relevant users
        $recipients = $this->getNotificationRecipients($ticket, 'updated');
        Notification::send($recipients, new TicketUpdated($ticket, 'status'));

        return redirect()->route('tickets.show', $ticket)->with('success', 'Status updated.');
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        Gate::authorize('addComment', $ticket);
        $request->validate([
            'comment' => 'required|string',
        ]);

        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);
        
        // Load relationships for notification
        $ticket->load(['user', 'category', 'status', 'assignees']);
        
        // Apply automation rules
        $this->automationService->handleCommentAdded($ticket, auth()->user());
        
        // Send notifications to relevant users
        $recipients = $this->getNotificationRecipients($ticket, 'updated');
        Notification::send($recipients, new TicketUpdated($ticket, 'comment'));

        return redirect()->route('tickets.show', $ticket)->with('success', 'Comment added.');    
    }

    public function addAssignee(Request $request, Ticket $ticket)
    {
        Gate::authorize('assignUser', $ticket);
        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id', // Keep this to ensure user exists before our rule runs
                new ValidAssignee, // Use the new custom rule
            ],
        ]);
        
        $assignee = \App\Models\User::findOrFail($request->user_id);
        $ticket->assignees()->syncWithoutDetaching([$request->user_id]);
        
        // Apply automation rules
        $this->automationService->handleAssigneeAdded($ticket, $assignee);
        
        return redirect()->route('tickets.show', $ticket)->with('success', 'Assignee added.');
    }

    public function removeAssignee(Ticket $ticket, $userId)
    {
        Gate::authorize('assignUser', $ticket);
        
        $removedAssignee = \App\Models\User::findOrFail($userId);
        $ticket->assignees()->detach($userId);
        
        // Apply automation rules
        $this->automationService->handleAssigneeRemoved($ticket, $removedAssignee);
        
        return redirect()->route('tickets.show', $ticket)->with('success', 'Assignee removed.');
    }

    public function myTickets()
    {
        $tickets = Ticket::with(['status', 'category', 'user.department', 'assignees'])
            ->where('user_id', Auth::id());
        $tickets->filterByStatus(request('status'))
                ->filterByCategory(request('category'));
        $this->applySorting($tickets);
        $tickets = $tickets->paginate(10)->withQueryString();
        $activeTab = 'my';
        $viewStatuses = TicketStatus::orderBy('name')->pluck('name', 'id');
        $viewCategories = Category::orderBy('name')->pluck('name', 'id');
        return view('ticket.index', compact('tickets', 'activeTab', 'viewStatuses', 'viewCategories'));
    }

    public function myTasks()
    {
        $tickets = Ticket::with(['status', 'category', 'user.department', 'assignees'])
            ->whereHas('assignees', function ($q) {
                $q->where('users.id', Auth::id());
            });
        $tickets->filterByStatus(request('status'))
                ->filterByCategory(request('category'));
        $this->applySorting($tickets);
        $tickets = $tickets->paginate(10)->withQueryString();
        $activeTab = 'tasks';
        $viewStatuses = TicketStatus::orderBy('name')->pluck('name', 'id');
        $viewCategories = Category::orderBy('name')->pluck('name', 'id');
        return view('ticket.index', compact('tickets', 'activeTab', 'viewStatuses', 'viewCategories'));
    }

    public function create()
    {
        Gate::authorize('create', Ticket::class);
        $categories = Category::orderBy('name')->pluck('name', 'id');
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $vendors = Vendor::orderBy('name')->pluck('name', 'id');
        $statuses = TicketStatus::orderBy('name')->pluck('name', 'id');
        return view('ticket.create', compact('categories', 'departments', 'vendors', 'statuses'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Ticket::class);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data['user_id'] = auth()->id();
        $data['ticket_status_id'] = TicketStatus::where('default_status', true)->value('id');
        $ticket = Ticket::create($data);
        
        // Load relationships for notification
        $ticket->load(['user', 'category']);
        
        // Send notifications to relevant users
        $recipients = $this->getNotificationRecipients($ticket, 'created');
        Notification::send($recipients, new TicketCreated($ticket));
        
        return redirect()->route('tickets.mine', $ticket)->with('success', 'Ticket created.');
    }

    public function updateCategory(Request $request, $ticketId)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
        ]);
        $ticket = Ticket::findOrFail($ticketId);
        Gate::authorize('updateCategory', $ticket);
        $ticket->category_id = $request->category_id;
        $ticket->save();
        
        // Load relationships for notification
        $ticket->load(['user', 'category', 'status', 'assignees']);
        
        // Send notifications to relevant users
        $recipients = $this->getNotificationRecipients($ticket, 'updated');
        Notification::send($recipients, new TicketUpdated($ticket, 'category'));

        return redirect()->route('tickets.show', $ticket)->with('success', 'Category updated.');
    }

    /**
     * Get users who should receive notifications for ticket events
     */
    private function getNotificationRecipients(Ticket $ticket, $event = 'created')
    {
        $recipients = collect();
        
        // Always notify IT and admin users
        $recipients = $recipients->merge(
            User::whereIn('user_type', ['it', 'admin'])->get()
        );
        
        if ($event === 'updated') {
            // Also notify the ticket creator (if not IT/admin)
            if (!in_array($ticket->user->user_type, ['it', 'admin'])) {
                $recipients->push($ticket->user);
            }
            
            // Notify assigned users
            $recipients = $recipients->merge($ticket->assignees);
        }
        
        // Remove duplicates and exclude the current user (unless they're the ticket creator on updates)
        return $recipients->unique('id')->filter(function($user) use ($event, $ticket) {
            if ($event === 'updated' && $user->id === $ticket->user_id) {
                return true; // Always notify ticket creator on updates
            }
            return $user->id !== auth()->id();
        });
    }
}
