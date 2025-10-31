<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Admin\StationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\Manager\SaleController as ManagerSaleController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Technician\DashboardController as TechnicianDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use App\Http\Controllers\SuperAdmin\ReportController;
use App\Http\Controllers\SuperAdmin\SystemController;
use App\Http\Controllers\SuperAdmin\SupportController;
use App\Http\Controllers\SuperAdmin\NotificationController;
use App\Http\Controllers\SuperAdmin\AuditController;
use App\Http\Controllers\SuperAdmin\BackupController;
use App\Http\Controllers\SuperAdmin\CompanySettingController;
use App\Livewire\SuperAdmin\SubscriptionDashboard;
use App\Livewire\SuperAdmin\UserSearch;
use App\Http\Controllers\Admin\TankController;
use App\Http\Controllers\Admin\PumpController;
use App\Http\Controllers\Admin\CompanyUserController;


// Authentification Breeze
require __DIR__.'/auth.php';

Route::get('/', function () {
    return redirect()->route('login');
});

// Routes protégées
Route::middleware(['auth'])->group(function () {
    // Tableau de bord principal (redirection selon le rôle)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

   // Super Admin Routes COMPLÈTES ET CORRIGÉES
    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    // Tableau de bord
    Route::get('/dashboard', SuperAdminDashboardController::class)->name('dashboard');

    // Gestion des entreprises
    Route::resource('companies', CompanyController::class);
    Route::post('/companies/{company}/suspend', [CompanyController::class, 'suspend'])->name('companies.suspend');
    Route::post('/companies/{company}/activate', [CompanyController::class, 'activate'])->name('companies.activate');
    Route::post('/companies/{company}/impersonate', [CompanyController::class, 'impersonate'])->name('companies.impersonate');
    // Paramètres des entreprises
    Route::get('/companies/{company}/settings', [CompanySettingController::class, 'edit'])->name('companies.settings.edit');
    Route::put('/companies/{company}/settings', [CompanySettingController::class, 'update'])->name('companies.settings.update');
    Route::post('/companies/{company}/settings/reset', [CompanySettingController::class, 'resetSettings'])->name('companies.settings.reset');
    Route::post('/companies/{company}/settings/upload-logo', [CompanySettingController::class, 'uploadLogo'])->name('companies.settings.upload-logo');
    Route::post('/companies/{company}/settings/remove-logo', [CompanySettingController::class, 'removeLogo'])->name('companies.settings.remove-logo');

    // Gestion des abonnements - CORRECTION DES ROUTES
    Route::resource('subscriptions', SubscriptionController::class);
    Route::post('/subscriptions/{company}/renew', [SubscriptionController::class, 'renew'])->name('subscriptions.renew');
    Route::post('/subscriptions/{company}/suspend', [SubscriptionController::class, 'suspend'])->name('subscriptions.suspend');
    Route::post('/subscriptions/{company}/activate', [SubscriptionController::class, 'activate'])->name('subscriptions.activate');

    // Gestion des utilisateurs
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/bulk-actions', [UserController::class, 'bulkActions'])->name('users.bulk-actions');

      // Tableaux de bord Livewire
    Route::get('/dashboard/subscriptions', SubscriptionDashboard::class)->name('dashboard.subscriptions');
    Route::get('/users/search', UserSearch::class)->name('users.search');

    // Routes pour la gestion des paiements
    Route::get('/subscriptions/{company}/create-payment', [SubscriptionController::class, 'createPayment'])->name('subscriptions.create-payment');
    Route::post('/subscriptions/{company}/store-payment', [SubscriptionController::class, 'storePayment'])->name('subscriptions.store-payment');
    Route::post('/subscriptions/check-overdue', [SubscriptionController::class, 'checkOverdueSubscriptions'])->name('subscriptions.check-overdue');

    // Support et communication
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/support/{ticket}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/status', [SupportController::class, 'updateStatus'])->name('support.status');
    Route::post('/support/{ticket}/assign', [SupportController::class, 'assign'])->name('support.assign');
    Route::get('/support/broadcast/form', [SupportController::class, 'broadcastForm'])->name('support.broadcast.form');
    Route::post('/support/broadcast', [SupportController::class, 'broadcast'])->name('support.broadcast');

    // Rapports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
    Route::get('/reports/performance', [ReportController::class, 'performance'])->name('reports.performance');

    // Administration technique - CORRECTION DES ROUTES
    Route::get('/system', [SystemController::class, 'index'])->name('system.index');
    Route::post('/system/backup', [SystemController::class, 'backup'])->name('system.backup');
    Route::post('/system/clear-cache', [SystemController::class, 'clearCache'])->name('system.clear-cache');
    Route::post('/system/migrate', [SystemController::class, 'migrate'])->name('system.migrate');
    Route::get('/system/logs', [SystemController::class, 'logs'])->name('system.logs');
    Route::post('/system/clear-logs', [SystemController::class, 'clearLogs'])->name('system.clear-logs');

    // Sécurité et audit
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    Route::get('/audit/suspicious', [AuditController::class, 'suspicious'])->name('audit.suspicious');
    Route::get('/audit/user/{user}', [AuditController::class, 'userActivity'])->name('audit.user');
    Route::get('/audit/company/{company}', [AuditController::class, 'companyActivity'])->name('audit.company');
    Route::post('/audit/export', [AuditController::class, 'export'])->name('audit.export');

    // Sauvegardes
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup/create', [BackupController::class, 'create'])->name('backup.create');
    Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy');
    Route::post('/backup/cleanup', [BackupController::class, 'cleanup'])->name('backup.cleanup');
});

   // Routes Admin
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // Gestion des utilisateurs - CORRECTION ICI
    Route::get('/users', [CompanyUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [CompanyUserController::class, 'create'])->name('users.create');
    Route::post('/users', [CompanyUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [CompanyUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [CompanyUserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle-status', [CompanyUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [CompanyUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}', [CompanyUserController::class, 'destroy'])->name('users.destroy');

    // Dashboard
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

    // Stations (routes principales)
    Route::resource('stations', StationController::class);

    // Routes supplémentaires pour les stations
    Route::post('stations/{station}/toggle-status', [StationController::class, 'toggleStatus'])
        ->name('stations.toggle-status');

    // Routes pour les cuves
    Route::prefix('stations/{station}')->name('stations.')->group(function () {
        Route::resource('tanks', TankController::class);

        Route::post('tanks/{tank}/adjust-volume', [TankController::class, 'adjustVolume'])
            ->name('tanks.adjust-volume');

        Route::post('tanks/{tank}/toggle-status', [TankController::class, 'toggleStatus'])
            ->name('tanks.toggle-status');
    });

    // Routes pour les pompes - IMPORTANT: À AJOUTER ICI
    Route::prefix('stations/{station}')->name('stations.')->group(function () {
        Route::resource('pumps', PumpController::class);

        Route::post('pumps/{pump}/update-index', [PumpController::class, 'updateIndex'])
            ->name('pumps.update-index');

        Route::post('pumps/{pump}/update-status', [PumpController::class, 'updateStatus'])
            ->name('pumps.update-status');
    });
});
    // Manager Routes
    Route::middleware(['role:manager'])->prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', ManagerDashboardController::class)->name('dashboard');
        Route::resource('sales', ManagerSaleController::class)->only(['index', 'create', 'store']);
    });

    // Employee Routes
    Route::middleware(['role:employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', EmployeeDashboardController::class)->name('dashboard');
    });

    // Technician Routes
    Route::middleware(['role:technician'])->prefix('technician')->name('technician.')->group(function () {
        Route::get('/dashboard', TechnicianDashboardController::class)->name('dashboard');
    });
});
