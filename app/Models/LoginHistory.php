<?php
// app/Models/LoginHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginHistory extends Model
{
    use HasFactory;

    protected $table = 'login_history';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'login_at',
        'success',
        'failure_reason'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'success' => 'boolean',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('login_at', '>=', Carbon::now()->subDays($days));
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    // Accessors
    public function getBrowserAttribute()
    {
        if (!$this->user_agent) {
            return 'Inconnu';
        }

        $userAgent = $this->user_agent;

        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        if (strpos($userAgent, 'Opera') !== false) return 'Opera';

        return 'Autre';
    }

    public function getDeviceTypeAttribute()
    {
        if (!$this->user_agent) {
            return 'Inconnu';
        }

        $userAgent = $this->user_agent;

        if (strpos($userAgent, 'Mobile') !== false) return 'Mobile';
        if (strpos($userAgent, 'Tablet') !== false) return 'Tablet';

        return 'Desktop';
    }

    public function getStatusColorAttribute()
    {
        return $this->success ? 'success' : 'danger';
    }

    public function getStatusTextAttribute()
    {
        return $this->success ? 'Succès' : 'Échec';
    }

    // Méthodes utilitaires
    public static function logLoginAttempt($userId, $ipAddress, $userAgent, $success = true, $failureReason = null)
    {
        return self::create([
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'login_at' => now(),
            'success' => $success,
            'failure_reason' => $failureReason,
        ]);
    }

    public static function getFailedAttemptsCount($userId, $hours = 1)
    {
        return self::failed()
            ->byUser($userId)
            ->where('login_at', '>=', Carbon::now()->subHours($hours))
            ->count();
    }

    public static function getLastSuccessfulLogin($userId)
    {
        return self::successful()
            ->byUser($userId)
            ->latest('login_at')
            ->first();
    }
}
