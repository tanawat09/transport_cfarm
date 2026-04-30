<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\PreTripInspectionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RouteStandardController;
use App\Http\Controllers\TireRegistrationController;
use App\Http\Controllers\TelegramSettingController;
use App\Http\Controllers\TireAlertReportController;
use App\Http\Controllers\TransportJobController;
use App\Http\Controllers\TransportJobLookupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleDocumentController;
use App\Http\Controllers\VehicleUsageLogController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/lookup/route-standard', [TransportJobLookupController::class, 'routeStandard'])->name('lookup.route-standard');
    Route::get('/lookup/farm-vendors', [TransportJobLookupController::class, 'farmVendors'])->name('lookup.farm-vendors');
    Route::get('/lookup/document-number', [TransportJobLookupController::class, 'documentNumber'])->name('lookup.document-number');
    Route::get('/lookup/latest-vehicle-mileage', [TransportJobLookupController::class, 'latestVehicleMileage'])->name('lookup.latest-vehicle-mileage');

    Route::post('/transport-jobs/recalculate', [TransportJobController::class, 'recalculateAll'])->name('transport-jobs.recalculate');
    Route::resource('transport-jobs', TransportJobController::class);
    Route::get('/pre-trip-inspections/export/pdf', [PreTripInspectionController::class, 'exportPdf'])->name('pre-trip-inspections.export.pdf');
    Route::resource('pre-trip-inspections', PreTripInspectionController::class);
    Route::get('/vehicle-usage-logs', [VehicleUsageLogController::class, 'index'])->name('vehicle-usage-logs.index');
    Route::get('/vehicle-usage-logs/create', [VehicleUsageLogController::class, 'create'])->name('vehicle-usage-logs.create');
    Route::post('/vehicle-usage-logs', [VehicleUsageLogController::class, 'store'])->name('vehicle-usage-logs.store');
    Route::delete('/vehicle-usage-logs/{vehicleUsageLog}', [VehicleUsageLogController::class, 'destroy'])->name('vehicle-usage-logs.destroy');
    Route::get('/tire-registrations', [TireRegistrationController::class, 'index'])->name('tire-registrations.index');
    Route::get('/tire-registrations/report', [TireAlertReportController::class, 'index'])->name('tire-registrations.report');
    Route::post('/tire-registrations', [TireRegistrationController::class, 'store'])->name('tire-registrations.store');
    Route::delete('/tire-registrations/{tireRegistration}', [TireRegistrationController::class, 'destroy'])->name('tire-registrations.destroy');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/date-range', [ReportController::class, 'index'])->name('reports.date-range');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    Route::middleware('role:admin')->group(function () {
        Route::get('/vehicles/qr/print-bulk', [VehicleController::class, 'bulkQrPrint'])->name('vehicles.qr-print-bulk');
        Route::get('/vehicles/{vehicle}/inspection-qr', [VehicleController::class, 'inspectionQrPage'])->name('vehicles.inspection-qr-page');
        Route::get('/vehicles/{vehicle}/inspection-qr/print', [VehicleController::class, 'inspectionQrPrint'])->name('vehicles.inspection-qr-print');
        Route::get('/vehicles/{vehicle}/inspection-qr.svg', [VehicleController::class, 'inspectionQrCode'])->name('vehicles.inspection-qr-code');
        Route::get('/vehicles/{vehicle}/usage-qr', [VehicleController::class, 'usageQrPage'])->name('vehicles.usage-qr-page');
        Route::get('/vehicles/{vehicle}/usage-qr/print', [VehicleController::class, 'usageQrPrint'])->name('vehicles.usage-qr-print');
        Route::get('/vehicles/{vehicle}/usage-qr.svg', [VehicleController::class, 'usageQrCode'])->name('vehicles.usage-qr-code');
        Route::resource('vehicle-documents', VehicleDocumentController::class)->except(['show']);
        Route::get('/telegram-settings', [TelegramSettingController::class, 'edit'])->name('telegram-settings.edit');
        Route::post('/telegram-settings', [TelegramSettingController::class, 'update'])->name('telegram-settings.update');
        Route::post('/telegram-settings/test', [TelegramSettingController::class, 'test'])->name('telegram-settings.test');
        Route::resource('vehicles', VehicleController::class)->except(['show']);
        Route::resource('drivers', DriverController::class)->except(['show']);
        Route::resource('farms', FarmController::class)->except(['show']);
        Route::resource('vendors', VendorController::class)->except(['show']);
        Route::resource('route-standards', RouteStandardController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
    });
});
