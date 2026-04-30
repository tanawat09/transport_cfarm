<?php

namespace App\Services;

use App\Models\TransportJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReportService
{
    public function query(array $filters = []): Builder
    {
        return TransportJob::query()
            ->with(['vehicle', 'driver', 'farm', 'vendor', 'routeStandard', 'oilCompensationReason'])
            ->when($filters['start_date'] ?? null, fn (Builder $query, $date) => $query->whereDate('transport_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn (Builder $query, $date) => $query->whereDate('transport_date', '<=', $date))
            ->when($filters['vehicle_id'] ?? null, fn (Builder $query, $vehicleId) => $query->where('vehicle_id', $vehicleId))
            ->when($filters['driver_id'] ?? null, fn (Builder $query, $driverId) => $query->where('driver_id', $driverId))
            ->when($filters['farm_id'] ?? null, fn (Builder $query, $farmId) => $query->where('farm_id', $farmId))
            ->when($filters['vendor_id'] ?? null, fn (Builder $query, $vendorId) => $query->where('vendor_id', $vendorId))
            ->orderByDesc('transport_date')
            ->orderByDesc('id');
    }

    public function summary(Builder $baseQuery): Collection
    {
        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total_jobs')
            ->selectRaw('SUM(food_weight_kg) as total_food_weight_kg')
            ->selectRaw('SUM(total_oil_cost) as total_oil_cost')
            ->selectRaw('SUM(actual_oil_liters) as total_actual_oil_liters')
            ->selectRaw('SUM(approved_oil_liters) as total_approved_oil_liters')
            ->selectRaw('SUM(oil_difference_liters) as total_oil_difference_liters')
            ->selectRaw('SUM(oil_difference_amount) as total_oil_difference_amount')
            ->selectRaw('SUM(distance_difference_km) as total_distance_difference_km')
            ->first();

        return collect([
            'total_jobs' => (int) ($summary->total_jobs ?? 0),
            'total_food_weight_kg' => round((float) ($summary->total_food_weight_kg ?? 0), 2),
            'total_oil_cost' => round((float) ($summary->total_oil_cost ?? 0), 2),
            'total_actual_oil_liters' => round((float) ($summary->total_actual_oil_liters ?? 0), 2),
            'total_approved_oil_liters' => round((float) ($summary->total_approved_oil_liters ?? 0), 2),
            'total_oil_difference_liters' => round((float) ($summary->total_oil_difference_liters ?? 0), 2),
            'total_oil_difference_amount' => round((float) ($summary->total_oil_difference_amount ?? 0), 2),
            'total_distance_difference_km' => round((float) ($summary->total_distance_difference_km ?? 0), 2),
        ]);
    }
}
