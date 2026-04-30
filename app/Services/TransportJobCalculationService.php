<?php

namespace App\Services;

use App\Models\RouteStandard;
use App\Models\TransportJob;

class TransportJobCalculationService
{
    public function findActiveRouteStandard(int $farmId, int $vendorId): ?RouteStandard
    {
        return RouteStandard::query()
            ->where('farm_id', $farmId)
            ->where('vendor_id', $vendorId)
            ->where('status', 'active')
            ->first();
    }

    public function findLatestVehicleJob(int $vehicleId): ?TransportJob
    {
        return TransportJob::query()
            ->where('vehicle_id', $vehicleId)
            ->latest('transport_date')
            ->latest('id')
            ->first();
    }

    public function buildPayload(array $validated, RouteStandard $routeStandard): array
    {
        $companyOil = (float) $routeStandard->company_oil_liters;
        $standardDistance = (float) $routeStandard->standard_distance_km;
        $oilCompensation = (float) ($validated['oil_compensation_liters'] ?? 0);
        $actualOil = (float) $validated['actual_oil_liters'];
        $oilPrice = (float) $validated['oil_price_per_liter'];
        $odometerStart = (float) $validated['odometer_start'];
        $odometerEnd = (float) $validated['odometer_end'];
        $actualDistance = $odometerEnd - $odometerStart;
        $approvedOil = $companyOil + $oilCompensation;
        $oilDifference = $actualOil - $approvedOil;
        $oilDifferenceAmount = $oilDifference * $oilPrice;
        $averageFuelRate = $actualOil > 0 ? $actualDistance / $actualOil : 0;
        $totalOilCost = $actualOil * $oilPrice;
        $distanceDifference = $actualDistance - $standardDistance;

        return [
            'transport_date' => $validated['transport_date'],
            'vehicle_id' => $validated['vehicle_id'],
            'driver_id' => $validated['driver_id'],
            'farm_id' => $validated['farm_id'],
            'vendor_id' => $validated['vendor_id'],
            'route_standard_id' => $routeStandard->id,
            'food_weight_kg' => round((float) $validated['food_weight_kg'], 2),
            'odometer_start' => round($odometerStart, 2),
            'odometer_end' => round($odometerEnd, 2),
            'actual_distance_km' => round($actualDistance, 2),
            'standard_distance_km' => round($standardDistance, 2),
            'company_oil_liters' => round($companyOil, 2),
            'oil_compensation_liters' => round($oilCompensation, 2),
            'oil_compensation_reason_id' => $validated['oil_compensation_reason_id'] ?? null,
            'oil_compensation_details' => $validated['oil_compensation_details'] ?? null,
            'approved_oil_liters' => round($approvedOil, 2),
            'actual_oil_liters' => round($actualOil, 2),
            'oil_price_per_liter' => round($oilPrice, 2),
            'total_oil_cost' => round($totalOilCost, 2),
            'oil_difference_liters' => round($oilDifference, 2),
            'oil_difference_amount' => round($oilDifferenceAmount, 2),
            'distance_difference_km' => round($distanceDifference, 2),
            'average_fuel_rate_km_per_liter' => round($averageFuelRate, 2),
            'notes' => $validated['notes'] ?? null,
        ];
    }
}
