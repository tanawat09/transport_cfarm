<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Farm;
use App\Models\PreTripInspection;
use App\Models\TireRegistration;
use App\Models\TransportJob;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\VehicleUsageLog;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $currentMonth = now()->startOfMonth();

        $monthlyJobRows = TransportJob::query()
            ->selectRaw("DATE_FORMAT(transport_date, '%Y-%m') as month_key")
            ->selectRaw('COUNT(*) as jobs_count')
            ->selectRaw('COALESCE(SUM(food_weight_kg), 0) as food_weight')
            ->selectRaw('COALESCE(SUM(actual_distance_km), 0) as distance_km')
            ->selectRaw('COALESCE(SUM(total_oil_cost), 0) as oil_cost')
            ->where('transport_date', '>=', $currentMonth->copy()->subMonths(5)->toDateString())
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $monthlyJobStats = collect(range(5, 0))->map(function (int $monthsAgo) use ($monthlyJobRows, $currentMonth) {
            $date = $currentMonth->copy()->subMonths($monthsAgo);
            $key = $date->format('Y-m');
            $row = $monthlyJobRows->get($key);

            return [
                'label' => $date->translatedFormat('M Y'),
                'jobs_count' => (int) ($row?->jobs_count ?? 0),
                'food_weight' => (float) ($row?->food_weight ?? 0),
                'distance_km' => (float) ($row?->distance_km ?? 0),
                'oil_cost' => (float) ($row?->oil_cost ?? 0),
            ];
        });

        $topVehicleRows = TransportJob::query()
            ->with('vehicle')
            ->selectRaw('vehicle_id, COUNT(*) as jobs_count, COALESCE(SUM(actual_distance_km), 0) as distance_km, COALESCE(SUM(total_oil_cost), 0) as oil_cost')
            ->whereBetween('transport_date', [$monthStart, $monthEnd])
            ->whereNotNull('vehicle_id')
            ->groupBy('vehicle_id')
            ->orderByDesc('jobs_count')
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'vehicleCount' => Vehicle::query()->where('status', 'active')->count(),
            'driverCount' => Driver::query()->where('status', 'active')->count(),
            'farmCount' => Farm::query()->count(),
            'vendorCount' => Vendor::query()->where('status', 'active')->count(),
            'todayJobsCount' => TransportJob::query()->whereDate('transport_date', $today)->count(),
            'monthJobsCount' => TransportJob::query()->whereBetween('transport_date', [$monthStart, $monthEnd])->count(),
            'monthlyOilCost' => TransportJob::query()->whereBetween('transport_date', [$monthStart, $monthEnd])->sum('total_oil_cost'),
            'monthlyDistance' => TransportJob::query()->whereBetween('transport_date', [$monthStart, $monthEnd])->sum('actual_distance_km'),
            'monthlyFoodWeight' => TransportJob::query()->whereBetween('transport_date', [$monthStart, $monthEnd])->sum('food_weight_kg'),
            'pendingCalculationCount' => TransportJob::query()->where('calculation_status', 'pending')->count(),
            'expiredDocumentCount' => VehicleDocument::query()->whereDate('expires_at', '<', $today)->count(),
            'expiringDocumentCount' => VehicleDocument::query()->whereDate('expires_at', '>=', $today)->whereDate('expires_at', '<=', now()->addDays(30)->toDateString())->count(),
            'inspectionTodayCount' => PreTripInspection::query()->whereDate('inspection_date', $today)->count(),
            'inspectionFailMonthCount' => PreTripInspection::query()->whereBetween('inspection_date', [$monthStart, $monthEnd])->where('is_ready_to_drive', false)->count(),
            'usageLogMonthCount' => VehicleUsageLog::query()->whereBetween('usage_date', [$monthStart, $monthEnd])->count(),
            'usageLogDistanceMonth' => VehicleUsageLog::query()->whereBetween('usage_date', [$monthStart, $monthEnd])->sum('distance_km'),
            'tireWarningCount' => TireRegistration::query()->whereIn('condition_status', ['warning', 'replace', 'repair'])->count(),
            'monthlyJobStats' => $monthlyJobStats,
            'maxMonthlyOilCost' => max(1, $monthlyJobStats->max('oil_cost')),
            'maxMonthlyJobs' => max(1, $monthlyJobStats->max('jobs_count')),
            'topVehicleRows' => $topVehicleRows,
            'maxTopVehicleJobs' => max(1, $topVehicleRows->max('jobs_count') ?? 1),
            'expiringDocuments' => VehicleDocument::query()
                ->with('vehicle')
                ->where('is_alert_enabled', true)
                ->whereDate('expires_at', '<=', now()->addDays(30)->toDateString())
                ->orderBy('expires_at')
                ->limit(8)
                ->get(),
            'failedInspections' => PreTripInspection::query()
                ->with(['vehicle', 'driver'])
                ->where('is_ready_to_drive', false)
                ->latest('inspection_date')
                ->latest('inspection_time')
                ->limit(6)
                ->get(),
            'recentJobs' => TransportJob::query()->with(['vehicle', 'driver', 'farm', 'vendor'])->latest('transport_date')->latest('id')->limit(8)->get(),
            'recentUsageLogs' => VehicleUsageLog::query()->with(['vehicle', 'driver'])->latest('usage_date')->latest('id')->limit(6)->get(),
        ]);
    }
}
