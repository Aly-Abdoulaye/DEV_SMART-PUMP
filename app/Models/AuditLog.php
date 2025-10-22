<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'user_id',
        'company_id',
        'suspicious',
        'risk_level',
        'suspicious_reason'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'suspicious' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Méthodes utilitaires
    public function getRiskColorAttribute()
    {
        return match($this->risk_level) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'secondary'
        };
    }

    public function isHighRisk()
    {
        return $this->risk_level === 'high';
    }

    public function getActionTypeAttribute()
    {
        if (str_contains($this->action, 'login')) return 'connexion';
        if (str_contains($this->action, 'create')) return 'création';
        if (str_contains($this->action, 'update')) return 'modification';
        if (str_contains($this->action, 'delete')) return 'suppression';
        return 'autre';
    }

    // Scopes
    public function scopeSuspicious($query)
    {
        return $query->where('suspicious', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Méthode statique pour logger les actions
    public static function logAction($action, $description, $userId, $companyId = null, $oldValues = null, $newValues = null)
    {
        $log = self::create([
            'action' => $action,
            'description' => $description,
            'user_id' => $userId,
            'company_id' => $companyId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]);

        // Détection automatique d'activités suspectes
        $log->detectSuspiciousActivity();

        return $log;
    }

    private function detectSuspiciousActivity()
    {
        $suspicious = false;
        $reason = null;
        $riskLevel = 'low';

        // Règles de détection d'activités suspectes
        $suspiciousPatterns = [
            'multiple_failed_logins' => str_contains($this->action, 'login.failed'),
            'sensitive_data_access' => str_contains($this->action, 'sensitive.access'),
            'mass_deletion' => str_contains($this->action, 'mass.delete'),
            'unauthorized_access' => str_contains($this->action, 'unauthorized'),
        ];

        foreach ($suspiciousPatterns as $pattern => $isSuspicious) {
            if ($isSuspicious) {
                $suspicious = true;
                $riskLevel = 'high';
                $reason = "Activité suspecte détectée: {$pattern}";
                break;
            }
        }

        // Vérifier les modifications hors heures de bureau
        $hour = $this->created_at->hour;
        if (($hour < 6 || $hour > 22) && str_contains($this->action, 'update')) {
            $suspicious = true;
            $riskLevel = 'medium';
            $reason = "Modification en dehors des heures normales de travail";
        }

        if ($suspicious) {
            $this->update([
                'suspicious' => true,
                'risk_level' => $riskLevel,
                'suspicious_reason' => $reason
            ]);
        }
    }
}