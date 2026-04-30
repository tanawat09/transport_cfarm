<?php

namespace App\Http\Requests;

use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleUsageLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'integer', Rule::exists('vehicles', 'id')->whereNull('deleted_at')],
            'driver_id' => ['nullable', 'integer', Rule::exists('drivers', 'id')->whereNull('deleted_at')],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'usage_date' => ['required', 'date'],
            'odometer_start' => ['nullable', 'numeric', 'min:0'],
            'odometer_end' => ['nullable', 'numeric', 'min:0'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'fuel_liters' => ['nullable', 'numeric', 'min:0'],
            'fuel_price_per_liter' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $start = $this->input('odometer_start');
                $end = $this->input('odometer_end');

                if ($start !== null && $start !== '' && $end !== null && $end !== '' && (float) $end < (float) $start) {
                    $validator->errors()->add('odometer_end', 'ไมล์สิ้นสุดต้องมากกว่าหรือเท่ากับไมล์เริ่มต้น');
                }

                $vehicle = $this->filled('vehicle_id')
                    ? Vehicle::query()->find($this->integer('vehicle_id'))
                    : null;

                if ($vehicle && ! $vehicle->supportsUsageLog()) {
                    $validator->errors()->add('vehicle_id', 'รถประเภทนี้ไม่ต้องบันทึก QR ใช้รถ');
                }
            },
        ];
    }
}
