<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ManagesTicketSorting
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
                // This is the corrected join from the previous step
                $query->leftJoin('users', 'tickets.user_id', '=', 'users.id')
                      ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
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
}
