<?php

namespace App\Services;

use App\Models\TransportJob;
use App\Models\Vehicle;
use App\Models\VehicleUsageLog;

class VehicleCurrentMileageService
{
    private const USAGE_LOG_MILEAGE_TYPES = [
        'รถยนต์บรรทุกส่วนบุคคล',
        'รถไถ คูโบต้า',
        'รถยนต์นั่งส่วนบุคคลไม่เกิน 7 คน',
    ];

    private const TRANSPORT_JOB_MILEAGE_TYPES = [
        'ลากจูง',
        'รถกึ่งพ่วงบรรทุกอาหารสัตว์',
    ];

    public function resolve(Vehicle $vehicle): ?float
    {
        if (in_array($vehicle->vehicle_type, self::USAGE_LOG_MILEAGE_TYPES, true)) {
            return $this->latestUsageLogMileage($vehicle);
        }

        if (in_array($vehicle->vehicle_type, self::TRANSPORT_JOB_MILEAGE_TYPES, true)) {
            return $this->latestTransportJobMileage($vehicle);
        }

        return $this->latestUsageLogMileage($vehicle) ?? $this->latestTransportJobMileage($vehicle);
    }

    private function latestUsageLogMileage(Vehicle $vehicle): ?float
    {
        $mileage = VehicleUsageLog::query()
            ->where('vehicle_id', $vehicle->id)
            ->orderByDesc('usage_date')
            ->orderByDesc('id')
            ->value('odometer_end');

        return $mileage !== null ? (float) $mileage : null;
    }

    private function latestTransportJobMileage(Vehicle $vehicle): ?float
    {
        $mileage = TransportJob::query()
            ->where('vehicle_id', $vehicle->id)
            ->orderByDesc('transport_date')
            ->orderByDesc('id')
            ->value('odometer_end');

        if ($mileage !== null) {
            return (float) $mileage;
        }

        if ($vehicle->vehicle_type === 'รถกึ่งพ่วงบรรทุกอาหารสัตว์' && filled($vehicle->towing_vehicle)) {
            $tractor = Vehicle::query()
                ->where('registration_number', $vehicle->towing_vehicle)
                ->first();

            if ($tractor) {
                $tractorMileage = TransportJob::query()
                    ->where('vehicle_id', $tractor->id)
                    ->orderByDesc('transport_date')
                    ->orderByDesc('id')
                    ->value('odometer_end');

                return $tractorMileage !== null ? (float) $tractorMileage : null;
            }
        }

        return null;
    }
}
