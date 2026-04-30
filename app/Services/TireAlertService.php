<?php

namespace App\Services;

use App\Models\TireRegistration;
use App\Models\Vehicle;
use Illuminate\Support\Collection;

class TireAlertService
{
    public function __construct(
        protected VehicleCurrentMileageService $vehicleCurrentMileageService
    ) {
    }

    public function reportRows(?string $vehicleType = null, ?int $vehicleId = null, ?string $status = null): Collection
    {
        $vehicles = Vehicle::query()
            ->when($vehicleType, fn ($query) => $query->where('vehicle_type', $vehicleType))
            ->when($vehicleId, fn ($query) => $query->where('id', $vehicleId))
            ->orderBy('vehicle_type')
            ->orderBy('registration_number')
            ->get()
            ->keyBy('id');

        if ($vehicles->isEmpty()) {
            return collect();
        }

        $latestTires = TireRegistration::query()
            ->with('vehicle')
            ->whereIn('vehicle_id', $vehicles->keys())
            ->orderByDesc('installed_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn (TireRegistration $registration) => $registration->vehicle_id . '|' . $registration->tire_position)
            ->map(fn (Collection $items) => $items->first())
            ->values();

        $rows = $latestTires->map(function (TireRegistration $registration) use ($vehicles) {
            $vehicle = $vehicles->get($registration->vehicle_id);
            $currentMileage = $vehicle ? $this->vehicleCurrentMileageService->resolve($vehicle) : null;
            $installedMileage = $registration->installed_mileage_km !== null ? (float) $registration->installed_mileage_km : null;
            $standardDistance = $registration->standard_replacement_distance_km !== null ? (float) $registration->standard_replacement_distance_km : null;

            $distanceUsed = null;
            if ($currentMileage !== null && $installedMileage !== null) {
                $distanceUsed = max(0, $currentMileage - $installedMileage);
            }

            $remainingDistance = null;
            $usagePercent = null;
            if ($distanceUsed !== null && $standardDistance !== null && $standardDistance > 0) {
                $remainingDistance = $standardDistance - $distanceUsed;
                $usagePercent = ($distanceUsed / $standardDistance) * 100;
            }

            $alertCode = $this->resolveAlertCode($registration->condition_status, $standardDistance, $distanceUsed, $usagePercent);

            return [
                'vehicle' => $vehicle,
                'registration' => $registration,
                'current_mileage_km' => $currentMileage,
                'distance_used_km' => $distanceUsed,
                'remaining_distance_km' => $remainingDistance,
                'usage_percent' => $usagePercent,
                'alert_code' => $alertCode,
                'alert_label' => $this->alertLabel($alertCode),
                'alert_badge_class' => $this->alertBadgeClass($alertCode),
            ];
        });

        if ($status) {
            $rows = $rows->where('alert_code', $status)->values();
        }

        return $rows
            ->sortBy([
                [fn (array $row) => $this->alertPriority($row['alert_code'])],
                [fn (array $row) => $row['vehicle']?->registration_number ?? ''],
                [fn (array $row) => $row['registration']->tire_position],
            ])
            ->values();
    }

    public function summary(Collection $rows): array
    {
        return [
            'total' => $rows->count(),
            'warning' => $rows->where('alert_code', 'warning')->count(),
            'replace' => $rows->where('alert_code', 'replace')->count(),
            'normal' => $rows->where('alert_code', 'normal')->count(),
            'missing' => $rows->whereIn('alert_code', ['no_standard', 'no_mileage'])->count(),
        ];
    }

    public function alertLabel(string $code): string
    {
        return match ($code) {
            'replace' => 'ถึงกำหนดเปลี่ยน',
            'warning' => 'ใกล้เปลี่ยน',
            'normal' => 'ปกติ',
            'no_standard' => 'ยังไม่กำหนดระยะ',
            'no_mileage' => 'ข้อมูลไมล์ไม่พอ',
            default => 'ไม่ระบุ',
        };
    }

    public function alertBadgeClass(string $code): string
    {
        return match ($code) {
            'replace' => 'text-bg-danger',
            'warning' => 'text-bg-warning',
            'normal' => 'text-bg-success',
            'no_standard', 'no_mileage' => 'text-bg-secondary',
            default => 'text-bg-secondary',
        };
    }

    private function resolveAlertCode(string $conditionStatus, ?float $standardDistance, ?float $distanceUsed, ?float $usagePercent): string
    {
        if ($conditionStatus === 'replace') {
            return 'replace';
        }

        if ($conditionStatus === 'warning') {
            return 'warning';
        }

        if ($standardDistance === null || $standardDistance <= 0) {
            return 'no_standard';
        }

        if ($distanceUsed === null || $usagePercent === null) {
            return 'no_mileage';
        }

        if ($usagePercent >= 100) {
            return 'replace';
        }

        if ($usagePercent >= 80) {
            return 'warning';
        }

        return 'normal';
    }

    private function alertPriority(string $code): int
    {
        return match ($code) {
            'replace' => 1,
            'warning' => 2,
            'no_mileage' => 3,
            'no_standard' => 4,
            'normal' => 5,
            default => 9,
        };
    }
}
