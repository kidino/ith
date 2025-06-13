<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\TicketStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __invoke()
    {

        Gate::authorize('viewAny', Ticket::class);

        $categoryCounts = Category::select('categories.name')
            ->leftJoin('tickets', 'tickets.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->selectRaw('count(tickets.id) as count')
            ->get();

        $statuses = TicketStatus::select('id', 'name', 'color')
            ->get()
            ->keyBy('name');

        $statusCounts = TicketStatus::select('name')
            ->leftJoin('tickets', 'tickets.ticket_status_id', '=', 'ticket_statuses.id')
            ->groupBy('ticket_statuses.id', 'ticket_statuses.name')
            ->selectRaw('count(tickets.id) as count')
            ->pluck('count', 'name');

        $statusColors = $statuses->mapWithKeys(function($status) {
            return [$status->name => $status->color];
        });

        $departmentCounts = \App\Models\Department::select('departments.name')
            ->leftJoin('users', 'users.department_id', '=', 'departments.id')
            ->leftJoin('tickets', 'tickets.user_id', '=', 'users.id')
            ->groupBy('departments.id', 'departments.name')
            ->selectRaw('count(tickets.id) as count')
            ->get();

        $user = Auth::user();
        $assignedToMeCount = 0;
        $createdByMeCount = 0;
        if ($user) {
            $assignedToMeCount = $user->assignedTickets()->count();
            $createdByMeCount = $user->tickets()->count();
        }

        return view('dashboard', compact(
            'categoryCounts',
            'statusCounts',
            'statusColors',
            'departmentCounts',
            'assignedToMeCount',
            'createdByMeCount'
        ));
    }
}
