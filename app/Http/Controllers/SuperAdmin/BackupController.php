<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $backups = $this->getBackupFiles();
        $storageInfo = $this->getStorageInfo();
        
        return view('super-admin.backup.index', compact('backups', 'storageInfo'));
    }

    public function create()
    {
        try {
            Artisan::call('backup:run');
            
            return redirect()->route('super-admin.backup.index')
                ->with('success', 'Sauvegarde créée avec succès.');
                
        } catch (\Exception $e) {
            return redirect()->route('super-admin.backup.index')
                ->with('error', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (!file_exists($filePath)) {
            abort(404);
        }
        
        return response()->download($filePath);
    }

    public function destroy($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        return redirect()->route('super-admin.backup.index')
            ->with('success', 'Sauvegarde supprimée avec succès.');
    }

    public function cleanup()
    {
        try {
            // Supprimer les sauvegardes de plus de 30 jours
            $backupPath = storage_path('app/backups');
            $files = glob($backupPath . '/*.zip');
            $deletedCount = 0;
            
            foreach ($files as $file) {
                if (filemtime($file) < strtotime('-30 days')) {
                    unlink($file);
                    $deletedCount++;
                }
            }
            
            return redirect()->route('super-admin.backup.index')
                ->with('success', "{$deletedCount} anciennes sauvegardes supprimées.");
                
        } catch (\Exception $e) {
            return redirect()->route('super-admin.backup.index')
                ->with('error', 'Erreur lors du nettoyage: ' . $e->getMessage());
        }
    }

    private function getBackupFiles()
    {
        $backupPath = storage_path('app/backups');
        
        if (!is_dir($backupPath)) {
            return [];
        }
        
        $files = glob($backupPath . '/*.zip');
        $backups = [];
        
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => $this->formatBytes(filesize($file)),
                'date' => Carbon::createFromTimestamp(filemtime($file))->format('d/m/Y H:i'),
                'path' => $file,
            ];
        }
        
        // Trier par date (plus récent en premier)
        usort($backups, function($a, $b) {
            return filemtime($b['path']) - filemtime($a['path']);
        });
        
        return $backups;
    }

    private function getStorageInfo()
    {
        $backupPath = storage_path('app/backups');
        $totalSize = 0;
        
        if (is_dir($backupPath)) {
            foreach (glob($backupPath . '/*.zip') as $file) {
                $totalSize += filesize($file);
            }
        }
        
        return [
            'total_size' => $this->formatBytes($totalSize),
            'file_count' => count(glob($backupPath . '/*.zip')),
            'last_backup' => $this->getLastBackupDate(),
        ];
    }

    private function getLastBackupDate()
    {
        $backupPath = storage_path('app/backups');
        $files = glob($backupPath . '/*.zip');
        
        if (empty($files)) {
            return 'Aucune sauvegarde';
        }
        
        $latestFile = max(array_map('filemtime', $files));
        return Carbon::createFromTimestamp($latestFile)->format('d/m/Y H:i');
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
}