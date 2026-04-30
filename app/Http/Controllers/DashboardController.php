<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Farm;
use App\Models\TransportJob;
use App\Models\Vehicle;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'vehicleCount' => Vehicle::query()->where('status', 'active')->count(),
            'driverCount' => Driver::query()->where('status', 'active')->count(),
            'farmCount' => Farm::query()->count(),
            'vendorCount' => Vendor::query()->where('status', 'active')->count(),
            'todayJobsCount' => TransportJob::query()->whereDate('transport_date', now()->toDateString())->count(),
            'monthlyOilCost' => TransportJob::query()->whereYear('transport_date', now()->year)->whereMonth('transport_date', now()->month)->sum('total_oil_cost'),
            'recentJobs' => TransportJob::query()->with(['vehicle', 'driver', 'farm', 'vendor'])->latest('transport_date')->latest('id')->limit(10)->get(),
        ]);
    }
}
