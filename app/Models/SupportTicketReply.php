<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'ticket_id',
        'user_id',
        'is_internal',
        'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
    ];

    // Relations
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes utilitaires
    public function getAttachmentsCountAttribute()
    {
        return $this->attachments ? count($this->attachments) : 0;
    }

    public function isFromCustomer()
    {
        return !$this->is_internal && !$this->user->isSuperAdmin();
    }
}