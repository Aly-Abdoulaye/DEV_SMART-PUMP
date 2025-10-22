<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'subject',
        'description',
        'priority',
        'status',
        'category',
        'company_id',
        'user_id',
        'assigned_to',
        'closed_at',
        'resolution_notes'
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    // Relations
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(SupportTicketReply::class);
    }

    // Méthodes utilitaires
    public function isOpen()
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    public function isClosed()
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'dark',
            default => 'secondary'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'in_progress' => 'info',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary'
        };
    }

    // Scope pour les tickets ouverts
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    // Scope pour les tickets par priorité
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }
}