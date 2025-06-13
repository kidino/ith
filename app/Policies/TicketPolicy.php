<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user)
    {
        return true;
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
}
