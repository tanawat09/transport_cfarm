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
            ->reorder()
            ->leftJoin('vehicles', 'vehicles.id', '=', 'transport_jobs.vehicle_id')
            ->selectRaw('COUNT(transport_jobs.id) as total_jobs')
            ->selectRaw('SUM(transport_jobs.food_weight_kg) as total_food_weight_kg')
            ->selectRaw('SUM(transport_jobs.total_oil_cost) as total_oil_cost')
            ->selectRaw('SUM(transport_jobs.actual_oil_liters) as total_actual_oil_liters')
            ->selectRaw('SUM(transport_jobs.approved_oil_liters) as total_approved_oil_liters')
            ->selectRaw('SUM(transport_jobs.oil_difference_liters) as total_oil_difference_liters')
            ->selectRaw('SUM(transport_jobs.oil_difference_amount) as total_oil_difference_amount')
            ->selectRaw('SUM(transport_jobs.distance_difference_km) as total_distance_difference_km')
            ->selectRaw('SUM(COALESCE(vehicles.capacity_kg, 0)) as total_vehicle_capacity_kg')
            ->first();

        $totalJobs = (int) ($summary->total_jobs ?? 0);
        $totalFoodWeightKg = round((float) ($summary->total_food_weight_kg ?? 0), 2);
        $totalFoodWeightTon = $totalFoodWeightKg > 0 ? round($totalFoodWeightKg / 1000, 4) : 0;
        $totalOilCost = round((float) ($summary->total_oil_cost ?? 0), 2);
        $totalActualOilLiters = round((float) ($summary->total_actual_oil_liters ?? 0), 2);
        $totalVehicleCapacityKg = round((float) ($summary->total_vehicle_capacity_kg ?? 0), 2);

        $loadingEfficiencyPercent = $totalVehicleCapacityKg > 0
            ? round(($totalFoodWeightKg / $totalVehicleCapacityKg) * 100, 2)
            : 0;

        $oilCostPerKg = $totalFoodWeightKg > 0
            ? round($totalOilCost / $totalFoodWeightKg, 4)
            : 0;

        $oilLitersPerTon = $totalFoodWeightTon > 0
            ? round($totalActualOilLiters / $totalFoodWeightTon, 4)
            : 0;

        $averageTripOilLiters = $totalJobs > 0
            ? round($totalActualOilLiters / $totalJobs, 4)
            : 0;

        $tripFuelUsagePerTon = ($totalJobs > 0 && $totalActualOilLiters > 0)
            ? round(($totalFoodWeightTon / $totalActualOilLiters) / $totalJobs, 4)
            : 0;

        return collect([
            'total_jobs' => $totalJobs,
            'total_food_weight_kg' => $totalFoodWeightKg,
            'total_food_weight_ton' => $totalFoodWeightTon,
            'total_oil_cost' => $totalOilCost,
            'total_actual_oil_liters' => $totalActualOilLiters,
            'total_approved_oil_liters' => round((float) ($summary->total_approved_oil_liters ?? 0), 2),
            'total_oil_difference_liters' => round((float) ($summary->total_oil_difference_liters ?? 0), 2),
            'total_oil_difference_amount' => round((float) ($summary->total_oil_difference_amount ?? 0), 2),
            'total_distance_difference_km' => round((float) ($summary->total_distance_difference_km ?? 0), 2),
            'total_vehicle_capacity_kg' => $totalVehicleCapacityKg,
            'loading_efficiency_percent' => $loadingEfficiencyPercent,
            'oil_cost_per_kg' => $oilCostPerKg,
            'oil_liters_per_ton' => $oilLitersPerTon,
            'average_trip_oil_liters' => $averageTripOilLiters,
            'trip_fuel_usage_per_ton' => $tripFuelUsagePerTon,
        ]);
    }
}
