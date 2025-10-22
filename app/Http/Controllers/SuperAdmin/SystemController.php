<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SystemController extends Controller
{
    public function index()
    {
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'database_driver' => config('database.default'),
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'Activé' : 'Désactivé',
            'storage_size' => $this->getStorageSize(),
            'backup_count' => $this->getBackupCount(),
        ];

        return view('super-admin.system.index', compact('systemInfo'));
    }

    public function backup()
    {
        try {
            // Exécuter la commande de sauvegarde
            Artisan::call('backup:run');
            
            $output = Artisan::output();
            
            return redirect()->route('super-admin.system.index')
                ->with('success', 'Sauvegarde créée avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('super-admin.system.index')
                ->with('error', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
        }
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            return redirect()->route('super-admin.system.index')
                ->with('success', 'Cache vidé avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('super-admin.system.index')
                ->with('error', 'Erreur lors du vidage du cache: ' . $e->getMessage());
        }
    }

    public function migrate()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            
            return redirect()->route('super-admin.system.index')
                ->with('success', 'Migrations exécutées avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('super-admin.system.index')
                ->with('error', 'Erreur lors des migrations: ' . $e->getMessage());
        }
    }

    private function getStorageSize()
    {
        $size = 0;
        $path = storage_path('app');
        
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $this->formatBytes($size);
    }

    private function getBackupCount()
    {
        $backupPath = storage_path('app/backups');
        
        if (!is_dir($backupPath)) {
            return 0;
        }
        
        return count(glob($backupPath . '/*.zip'));
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function logs()
{
    $logFile = storage_path('logs/laravel.log');
    $logs = [];
    
    if (file_exists($logFile)) {
        $logs = array_slice(file($logFile), -100); // Dernières 100 lignes
        $logs = array_reverse($logs); // Plus récent en premier
    }
    
    return view('super-admin.system.logs', compact('logs'));
}

public function clearLogs()
{
    try {
        $logFile = storage_path('logs/laravel.log');
        
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
        }
        
        return redirect()->route('super-admin.system.logs')
            ->with('success', 'Logs vidés avec succès.');
            
    } catch (\Exception $e) {
        return redirect()->route('super-admin.system.logs')
            ->with('error', 'Erreur lors du vidage des logs: ' . $e->getMessage());
    }
}
}