<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'subject',
        'message',
        'metadata',
        'company_id',
        'sent_by',
        'is_sent',
        'sent_at',
        'is_read',
        'read_at',
        'scheduled_for',
        'is_recurring',
        'recurrence_pattern'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_sent' => 'boolean',
        'is_read' => 'boolean',
        'is_recurring' => 'boolean',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'scheduled_for' => 'datetime',
    ];

    // Relations
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    // Méthodes utilitaires
    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'maintenance' => 'warning',
            'update' => 'info',
            'alert' => 'danger',
            'billing' => 'primary',
            'info' => 'success',
            default => 'secondary'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'maintenance' => 'fas fa-tools',
            'update' => 'fas fa-sync-alt',
            'alert' => 'fas fa-exclamation-triangle',
            'billing' => 'fas fa-credit-card',
            'info' => 'fas fa-info-circle',
            default => 'fas fa-bell'
        };
    }

    public function isScheduled()
    {
        return $this->scheduled_for && $this->scheduled_for->isFuture();
    }

    public function isDue()
    {
        return $this->scheduled_for && $this->scheduled_for->isPast() && !$this->is_sent;
    }

    public function markAsSent()
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => now()
        ]);
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeUnsent($query)
    {
        return $query->where('is_sent', false);
    }

    public function scopeDue($query)
    {
        return $query->where('scheduled_for', '<=', now())
                    ->where('is_sent', false);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Méthode pour créer une notification broadcast
    public static function createBroadcast($type, $subject, $message, $sentBy, $companies = null, $schedule = null)
    {
        $companies = $companies ?? Company::where('is_active', true)->get();

        foreach ($companies as $company) {
            self::create([
                'type' => $type,
                'subject' => $subject,
                'message' => $message,
                'company_id' => $company->id,
                'sent_by' => $sentBy,
                'scheduled_for' => $schedule,
                'is_sent' => is_null($schedule), // Immédiatement envoyé si pas de planning
                'sent_at' => is_null($schedule) ? now() : null,
            ]);
        }

        return $companies->count();
    }
}