<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TicketAutomationService
{
    /**
     * Handle automation when a comment is added to a ticket
     */
    public function handleCommentAdded(Ticket $ticket, User $user): void
    {
        $currentStatus = $ticket->status->name ?? null;
        $userType = $user->user_type;

        // Rule 1: IT/Admin first comment: new → in_progress
        if ($currentStatus === 'new' && in_array($userType, ['it', 'admin'])) {
            $this->changeStatus($ticket, 'in_progress', 'IT/Admin started working on ticket');
            return;
        }

        // Rule 2: Vendor comments: pending_vendor → in_progress
        if ($currentStatus === 'pending_vendor' && $userType === 'vendor') {
            $this->changeStatus($ticket, 'in_progress', 'Vendor provided update');
            return;
        }

        // Rule 3: User provides info: pending_user → in_progress
        if ($currentStatus === 'pending_user' && $userType === 'user' && $user->id === $ticket->user_id) {
            $this->changeStatus($ticket, 'in_progress', 'User provided requested information');
            return;
        }

        // Rule 4: Vendor first response: new → in_progress
        if ($currentStatus === 'new' && $userType === 'vendor') {
            $this->changeStatus($ticket, 'in_progress', 'Vendor started working on ticket');
            return;
        }

        Log::info('TicketAutomation: Comment added but no status change needed', [
            'ticket_id' => $ticket->id,
            'current_status' => $currentStatus,
            'user_type' => $userType,
            'user_id' => $user->id
        ]);
    }

    /**
     * Handle automation when an assignee is added to a ticket
     */
    public function handleAssigneeAdded(Ticket $ticket, User $assignee): void
    {
        $currentStatus = $ticket->status->name ?? null;
        $assigneeType = $assignee->user_type;

        // Rule 5: Vendor assigned → pending_vendor (unless already resolved/closed)
        if ($assigneeType === 'vendor' && !in_array($currentStatus, ['resolved', 'closed'])) {
            $this->changeStatus($ticket, 'pending_vendor', 'Ticket assigned to vendor');
            return;
        }

        Log::info('TicketAutomation: Assignee added but no status change needed', [
            'ticket_id' => $ticket->id,
            'current_status' => $currentStatus,
            'assignee_type' => $assigneeType,
            'assignee_id' => $assignee->id
        ]);
    }

    /**
     * Handle automation when an assignee is removed from a ticket
     */
    public function handleAssigneeRemoved(Ticket $ticket, User $removedAssignee): void
    {
        $currentStatus = $ticket->status->name ?? null;
        $assigneeType = $removedAssignee->user_type;

        // Rule 6: Last vendor removed from pending_vendor → in_progress
        if ($assigneeType === 'vendor' && $currentStatus === 'pending_vendor') {
            $remainingVendors = $ticket->assignees()->where('user_type', 'vendor')->count();
            
            if ($remainingVendors === 0) {
                $this->changeStatus($ticket, 'in_progress', 'No vendors assigned, returned to IT');
            }
        }

        Log::info('TicketAutomation: Assignee removed', [
            'ticket_id' => $ticket->id,
            'current_status' => $currentStatus,
            'removed_assignee_type' => $assigneeType
        ]);
    }

    /**
     * Handle automation for status changes (for validation and logging)
     */
    public function handleStatusChange(Ticket $ticket, string $oldStatus, User $user): void
    {
        $newStatus = $ticket->status->name ?? null;
        $userType = $user->user_type;

        // Rule 7: Only admin can set to resolved
        if ($newStatus === 'resolved' && $userType !== 'admin') {
            Log::warning('TicketAutomation: Non-admin tried to resolve ticket', [
                'ticket_id' => $ticket->id,
                'user_type' => $userType,
                'user_id' => $user->id
            ]);
            // Note: This validation should also be in the controller/policy
        }

        // Update the ticket's last activity timestamp for auto-closure tracking
        if ($newStatus === 'resolved') {
            $ticket->touch(); // Updates updated_at timestamp
        }

        Log::info('TicketAutomation: Status changed', [
            'ticket_id' => $ticket->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'user_type' => $userType,
            'user_id' => $user->id
        ]);
    }

    /**
     * Auto-close resolved tickets after 7 days of no updates
     * This method will be called by the scheduled command
     */
    public function autoCloseResolvedTickets(): int
    {
        $resolvedStatus = TicketStatus::where('name', 'resolved')->first();
        $closedStatus = TicketStatus::where('name', 'closed')->first();

        if (!$resolvedStatus || !$closedStatus) {
            Log::error('TicketAutomation: Missing required statuses for auto-closure');
            return 0;
        }

        $cutoffDate = now()->subDays(7);
        
        $tickets = Ticket::where('ticket_status_id', $resolvedStatus->id)
            ->where('updated_at', '<', $cutoffDate)
            ->get();

        $closedCount = 0;
        foreach ($tickets as $ticket) {
            $ticket->ticket_status_id = $closedStatus->id;
            $ticket->save();
            
            // Add a system comment to track the auto-closure
            $ticket->comments()->create([
                'user_id' => null, // System comment
                'comment' => 'Ticket automatically closed after 7 days with no updates since resolution.'
            ]);

            Log::info('TicketAutomation: Auto-closed resolved ticket', [
                'ticket_id' => $ticket->id,
                'resolved_date' => $ticket->updated_at,
                'days_since_resolved' => $ticket->updated_at->diffInDays(now())
            ]);

            $closedCount++;
        }

        if ($closedCount > 0) {
            Log::info("TicketAutomation: Auto-closed {$closedCount} resolved tickets");
        }

        return $closedCount;
    }

    /**
     * Change ticket status with logging
     */
    private function changeStatus(Ticket $ticket, string $newStatusName, string $reason): bool
    {
        $newStatus = TicketStatus::where('name', $newStatusName)->first();
        
        if (!$newStatus) {
            Log::error("TicketAutomation: Status '{$newStatusName}' not found");
            return false;
        }

        $oldStatus = $ticket->status->name ?? 'unknown';
        $ticket->ticket_status_id = $newStatus->id;
        $ticket->save();

        // Add a system comment to track the automation
        $ticket->comments()->create([
            'user_id' => null, // System comment
            'comment' => "Status automatically changed from '{$oldStatus}' to '{$newStatusName}': {$reason}"
        ]);

        Log::info('TicketAutomation: Status changed automatically', [
            'ticket_id' => $ticket->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatusName,
            'reason' => $reason
        ]);

        return true;
    }
}