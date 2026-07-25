<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Sekretaris;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Autentikasi (login pakai USERNAME, tanpa email)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Pengalihan beranda sesuai peran
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->middleware('auth')->name('home');

/*
|--------------------------------------------------------------------------
| Notifikasi (admin & sekretaris)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/feed', [NotificationController::class, 'feed'])->name('notifications.feed');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::delete('/notifications/clear', [NotificationController::class, 'clear'])->name('notifications.clear');
    Route::get('/notifications/{id}', [NotificationController::class, 'read'])->name('notifications.read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

/*
|--------------------------------------------------------------------------
| Area ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Master: Pengguna
        Route::resource('users', Admin\UserController::class)->except('show');

        // Master: Kategori
        Route::resource('categories', Admin\CategoryController::class)->except('show');

        // Master: Kelas peminjam
        Route::resource('kelas', Admin\KelasController::class)
            ->parameters(['kelas' => 'kelas'])
            ->except('show');

        // Master: Tata Letak Lab
        Route::resource('labs', Admin\LabController::class);
        Route::post('labs/{lab}/groups', [Admin\LabGroupController::class, 'store'])->name('labs.groups.store');
        Route::put('labs/{lab}/groups/{group}', [Admin\LabGroupController::class, 'update'])->name('labs.groups.update');
        Route::delete('labs/{lab}/groups/{group}', [Admin\LabGroupController::class, 'destroy'])->name('labs.groups.destroy');
        Route::post('groups/{group}/tables', [Admin\LabTableController::class, 'store'])->name('groups.tables.store');
        Route::put('groups/{group}/tables/{table}', [Admin\LabTableController::class, 'update'])->name('groups.tables.update');
        Route::delete('groups/{group}/tables/{table}', [Admin\LabTableController::class, 'destroy'])->name('groups.tables.destroy');

        // Barang
        Route::resource('items', Admin\ItemController::class);
        Route::post('items/{item}/adjust-stock', [Admin\ItemController::class, 'adjustStock'])->name('items.adjust');

        // Laporan rusak/hilang
        Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/{report}', [Admin\ReportController::class, 'show'])->name('reports.show');
        Route::patch('reports/{report}/status', [Admin\ReportController::class, 'updateStatus'])->name('reports.status');
        Route::delete('reports/{report}', [Admin\ReportController::class, 'destroy'])->name('reports.destroy');

        // Riwayat perbaikan
        Route::resource('repairs', Admin\RepairController::class)->except('show');

        // Pengajuan barang
        Route::get('procurements', [Admin\ProcurementController::class, 'index'])->name('procurements.index');
        Route::get('procurements/{procurement}', [Admin\ProcurementController::class, 'show'])->name('procurements.show');
        Route::patch('procurements/{procurement}/status', [Admin\ProcurementController::class, 'updateStatus'])->name('procurements.status');
        Route::delete('procurements/{procurement}', [Admin\ProcurementController::class, 'destroy'])->name('procurements.destroy');

        // Audit inventaris
        Route::resource('audits', Admin\StockAuditController::class)->only(['index', 'create', 'store', 'destroy']);

        // Log aktivitas
        Route::get('activities', [Admin\ActivityLogController::class, 'index'])->name('activities.index');

        // Export
        Route::get('export/items/excel', [Admin\ExportController::class, 'itemsExcel'])->name('export.items.excel');
        Route::get('export/items/pdf', [Admin\ExportController::class, 'itemsPdf'])->name('export.items.pdf');
        Route::get('export/reports/excel', [Admin\ExportController::class, 'reportsExcel'])->name('export.reports.excel');
        Route::get('export/reports/pdf', [Admin\ExportController::class, 'reportsPdf'])->name('export.reports.pdf');
	Route::get('export/procurements/excel', [Admin\ExportController::class, 'procurementsExcel'])->name('export.procurements.excel');
	Route::get('export/procurements/pdf', [Admin\ExportController::class, 'procurementsPdf'])->name('export.procurements.pdf');
        Route::get('export/repairs/excel', [Admin\ExportController::class, 'repairsExcel'])->name('export.repairs.excel');
        Route::get('export/repairs/pdf', [Admin\ExportController::class, 'repairsPdf'])->name('export.repairs.pdf');
        Route::get('export/audits/excel', [Admin\ExportController::class, 'auditsExcel'])->name('export.audits.excel');
        Route::get('export/audits/pdf', [Admin\ExportController::class, 'auditsPdf'])->name('export.audits.pdf');
    });

/*
|--------------------------------------------------------------------------
| Area SEKRETARIS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:sekretaris'])
    ->prefix('sekretaris')
    ->name('sekretaris.')
    ->group(function () {
        Route::get('/dashboard', [Sekretaris\DashboardController::class, 'index'])->name('dashboard');

        // Inventaris (read-only)
        Route::get('inventaris', [Sekretaris\InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventaris/{item}', [Sekretaris\InventoryController::class, 'show'])->name('inventory.show');

        // Laporan rusak & hilang
        Route::resource('reports', Sekretaris\ReportController::class);

        // Pengajuan barang
        Route::resource('procurements', Sekretaris\ProcurementController::class);
    });
