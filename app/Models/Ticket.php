<?php

namespace App\Models;

use App\Policies\TicketPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ticket extends Model
{

    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'user_id',
        'category_id',
        'ticket_status_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, 'ticket_status_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_user');
    }

    /**
     * Scope a query to only include tickets of a given status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $statusId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByStatus($query, $statusId)
    {
        if ($statusId) {
            return $query->where('ticket_status_id', $statusId);
        }
        return $query;
    }

    /**
     * Scope a query to only include tickets of a given category.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $categoryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCategory($query, $categoryId)
    {
        if ($categoryId) {
            return $query->where('category_id', $categoryId);
        }
        return $query;
    }
}
