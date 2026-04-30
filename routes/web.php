<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RouteStandardController;
use App\Http\Controllers\TransportJobController;
use App\Http\Controllers\TransportJobLookupController;
use App\Http\Controllers\VehicleController;
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

    Route::resource('transport-jobs', TransportJobController::class);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/date-range', [ReportController::class, 'index'])->name('reports.date-range');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    Route::middleware('role:admin')->group(function () {
        Route::resource('vehicles', VehicleController::class)->except(['show']);
        Route::resource('drivers', DriverController::class)->except(['show']);
        Route::resource('farms', FarmController::class)->except(['show']);
        Route::resource('vendors', VendorController::class)->except(['show']);
        Route::resource('route-standards', RouteStandardController::class)->except(['show']);
    });
});
