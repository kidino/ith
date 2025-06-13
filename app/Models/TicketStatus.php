<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketStatus extends Model
{
    protected $table = 'ticket_statuses';

    protected $fillable = ['name', 'color'];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
