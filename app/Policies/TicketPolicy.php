<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->user_type, ['admin', 'it']);
    }

    public function view(User $user, Ticket $ticket)
    {
        // Admin and IT can view all, user can view own, vendor can view assigned
        if ($user->user_type === 'admin' || $user->user_type === 'it') {
            return true;
        }
        if ($user->user_type === 'user') {
            return $ticket->user_id === $user->id;
        }
        if ($user->user_type === 'vendor') {
            return $ticket->assignees->contains($user->id);
        }
        return false;
    }

    public function update(User $user, Ticket $ticket)
    {
        // Admin and IT can update all, user can update own, vendor can update assigned
        return $this->view($user, $ticket);
    }

    public function delete(User $user, Ticket $ticket)
    {
        // Only admin or IT can delete
        return $user->user_type === 'admin' || $user->user_type === 'it';
    }

    public function create(User $user)
    {
        // All authenticated users can create
        return in_array($user->user_type, ['admin', 'it', 'user']);
    }

    public function updateStatus(User $user, Ticket $ticket)
    {
        // Admin, IT, or assigned Vendor can update status
        if (in_array($user->user_type, ['admin', 'it'])) {
            return true;
        }
        if ($user->user_type === 'vendor') {
            return $ticket->assignees->contains($user->id);
        }
        return false;
    }

    public function addComment(User $user, Ticket $ticket)
    {
        // Anyone who can view the ticket can add a comment
        return $this->view($user, $ticket);
    }

    public function assignUser(User $user, Ticket $ticket)
    {
        // Only Admin or IT can assign users
        return in_array($user->user_type, ['admin', 'it']);
    }

    public function updateCategory(User $user, Ticket $ticket)
    {
        // Only Admin or IT can update category
        return in_array($user->user_type, ['admin', 'it']);
    }

    public function resolveTicket(User $user, Ticket $ticket)
    {
        // Only Admin can mark tickets as resolved
        return $user->user_type === 'admin';
    }
}
