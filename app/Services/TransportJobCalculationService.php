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

    public function findLatestVehicleJob(int $vehicleId, ?string $beforeOrOnDate = null): ?TransportJob
    {
        return TransportJob::query()
            ->where('vehicle_id', $vehicleId)
            ->when($beforeOrOnDate, fn ($query) => $query->whereDate('transport_date', '<=', $beforeOrOnDate))
            ->latest('transport_date')
            ->latest('id')
            ->first();
    }

    public function buildPayload(array $validated, RouteStandard $routeStandard): array
    {
        return array_merge(
            $this->buildRawPayload($validated, $routeStandard),
            $this->calculatePayload($validated, $routeStandard)
        );
    }

    public function buildRawPayload(array $validated, RouteStandard $routeStandard): array
    {
        $companyOil = (float) $routeStandard->company_oil_liters;
        $standardDistance = (float) $routeStandard->standard_distance_km;
        $oilCompensation = (float) ($validated['oil_compensation_liters'] ?? 0);
        $actualOil = (float) $validated['actual_oil_liters'];
        $vehicleScreenOil = $this->nullableFloat($validated['vehicle_screen_oil_liters'] ?? null);
        $oilPrice = (float) $validated['oil_price_per_liter'];
        $odometerStart = (float) $validated['odometer_start'];
        $odometerEnd = (float) $validated['odometer_end'];
        $otherJobOdometerStart = $this->nullableFloat($validated['other_job_odometer_start'] ?? null);
        $otherJobOdometerEnd = $this->nullableFloat($validated['other_job_odometer_end'] ?? null);

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
            'other_job_description' => $validated['other_job_description'] ?? null,
            'other_job_odometer_start' => $otherJobOdometerStart !== null ? round($otherJobOdometerStart, 2) : null,
            'other_job_odometer_end' => $otherJobOdometerEnd !== null ? round($otherJobOdometerEnd, 2) : null,
            'other_job_distance_km' => 0,
            'actual_distance_km' => 0,
            'standard_distance_km' => round($standardDistance, 2),
            'company_oil_liters' => round($companyOil, 2),
            'oil_compensation_liters' => round($oilCompensation, 2),
            'oil_compensation_reason_id' => $validated['oil_compensation_reason_id'] ?? null,
            'oil_compensation_details' => $validated['oil_compensation_details'] ?? null,
            'approved_oil_liters' => 0,
            'actual_oil_liters' => round($actualOil, 2),
            'vehicle_screen_oil_liters' => $vehicleScreenOil !== null ? round($vehicleScreenOil, 2) : null,
            'oil_price_per_liter' => round($oilPrice, 2),
            'total_oil_cost' => 0,
            'oil_difference_liters' => 0,
            'oil_difference_amount' => 0,
            'distance_difference_km' => 0,
            'average_fuel_rate_km_per_liter' => 0,
            'calculation_status' => 'pending',
            'calculation_note' => null,
            'calculated_at' => null,
            'notes' => $validated['notes'] ?? null,
        ];
    }

    public function recalculateVehicleJobs(int $vehicleId): void
    {
        $jobs = TransportJob::query()
            ->with('routeStandard')
            ->where('vehicle_id', $vehicleId)
            ->orderBy('transport_date')
            ->orderBy('id')
            ->get();

        $previousJob = null;

        foreach ($jobs as $job) {
            if (! $job->routeStandard) {
                $job->forceFill([
                    'calculation_status' => 'error',
                    'calculation_note' => 'ไม่พบมาตรฐานเส้นทางสำหรับคำนวณ',
                    'calculated_at' => now(),
                ])->save();
                $previousJob = $job;
                continue;
            }

            $effectiveOdometerStart = $previousJob
                ? (float) $previousJob->odometer_end
                : (float) $job->odometer_start;
            $payload = $this->calculatePayloadFromJob($job, $effectiveOdometerStart);
            $note = null;
            $status = 'calculated';

            if ((float) $job->odometer_end < $effectiveOdometerStart) {
                $status = 'warning';
                $note = sprintf(
                    'ไมล์ปลาย %.2f ต่ำกว่าไมล์ต้นที่ใช้คำนวณ %.2f',
                    (float) $job->odometer_end,
                    $effectiveOdometerStart
                );
            }

            $job->forceFill(array_merge($payload, [
                'odometer_start' => round($effectiveOdometerStart, 2),
                'calculation_status' => $status,
                'calculation_note' => $note,
                'calculated_at' => now(),
            ]))->save();

            $previousJob = $job->refresh();
        }
    }

    private function calculatePayload(array $validated, RouteStandard $routeStandard): array
    {
        $companyOil = (float) $routeStandard->company_oil_liters;
        $standardDistance = (float) $routeStandard->standard_distance_km;
        $oilCompensation = (float) ($validated['oil_compensation_liters'] ?? 0);
        $actualOil = (float) $validated['actual_oil_liters'];
        $vehicleScreenOil = $this->nullableFloat($validated['vehicle_screen_oil_liters'] ?? null);
        $oilPrice = (float) $validated['oil_price_per_liter'];
        $odometerStart = (float) $validated['odometer_start'];
        $odometerEnd = (float) $validated['odometer_end'];
        $otherJobOdometerStart = $this->nullableFloat($validated['other_job_odometer_start'] ?? null);
        $otherJobOdometerEnd = $this->nullableFloat($validated['other_job_odometer_end'] ?? null);

        return $this->calculateValues(
            $companyOil,
            $standardDistance,
            $oilCompensation,
            $actualOil,
            $vehicleScreenOil,
            $oilPrice,
            $odometerStart,
            $odometerEnd,
            $otherJobOdometerStart,
            $otherJobOdometerEnd
        );
    }

    private function calculatePayloadFromJob(TransportJob $job, ?float $effectiveOdometerStart = null): array
    {
        return $this->calculateValues(
            (float) $job->routeStandard->company_oil_liters,
            (float) $job->routeStandard->standard_distance_km,
            (float) $job->oil_compensation_liters,
            (float) $job->actual_oil_liters,
            $this->nullableFloat($job->vehicle_screen_oil_liters),
            (float) $job->oil_price_per_liter,
            $effectiveOdometerStart ?? (float) $job->odometer_start,
            (float) $job->odometer_end,
            $this->nullableFloat($job->other_job_odometer_start),
            $this->nullableFloat($job->other_job_odometer_end)
        );
    }

    private function calculateValues(
        float $companyOil,
        float $standardDistance,
        float $oilCompensation,
        float $actualOil,
        ?float $vehicleScreenOil,
        float $oilPrice,
        float $odometerStart,
        float $odometerEnd,
        ?float $otherJobOdometerStart,
        ?float $otherJobOdometerEnd,
    ): array {
        $foodTransportDistance = $odometerEnd - $odometerStart;
        $otherJobDistance = ($otherJobOdometerStart !== null && $otherJobOdometerEnd !== null)
            ? max($otherJobOdometerEnd - $otherJobOdometerStart, 0)
            : 0;
        $actualDistance = $foodTransportDistance + $otherJobDistance;
        $approvedOil = $companyOil + $oilCompensation;
        $oilDifference = $companyOil - $actualOil;
        $oilDifferenceAmount = $oilDifference * $oilPrice;
        $averageFuelRate = $vehicleScreenOil > 0 ? $actualDistance / $vehicleScreenOil : 0;
        $totalOilCost = $actualOil * $oilPrice;
        $distanceDifference = $actualDistance - $standardDistance;

        return [
            'other_job_distance_km' => round($otherJobDistance, 2),
            'actual_distance_km' => round($actualDistance, 2),
            'standard_distance_km' => round($standardDistance, 2),
            'company_oil_liters' => round($companyOil, 2),
            'approved_oil_liters' => round($approvedOil, 2),
            'total_oil_cost' => round($totalOilCost, 2),
            'oil_difference_liters' => round($oilDifference, 2),
            'oil_difference_amount' => round($oilDifferenceAmount, 2),
            'distance_difference_km' => round($distanceDifference, 2),
            'average_fuel_rate_km_per_liter' => round($averageFuelRate, 2),
        ];
    }

    private function nullableFloat(mixed $value): ?float
    {
        return $value === null || $value === '' ? null : (float) $value;
    }
}
