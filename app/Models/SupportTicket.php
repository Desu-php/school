<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'support_ticket_category_id',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function support_ticket_category()
    {
        return $this->belongsTo(SupportTicketCategory::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function support_ticket_messages()
    {
        return $this->hasMany(SupportTicketMessage::class);
    }

    public function unread_messages()
    {
        if (auth('api')->user()) {
            return $this->support_ticket_messages()->where('sender_is_user', false)->where('is_read', false)->count();
        }
        if (auth('admin')->user()) {
            return $this->support_ticket_messages()->where('sender_is_user', true)->where('is_read', false)->count();
        }
    }
}
