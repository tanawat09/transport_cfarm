<?php

namespace App\Http\Requests;

use App\Models\TireRegistration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TireRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'integer', Rule::exists('vehicles', 'id')->whereNull('deleted_at')],
            'tire_position' => ['required', 'string', 'max:10'],
            'tire_serial_number' => ['required', 'string', 'max:100'],
            'installed_at' => ['nullable', 'date'],
            'installed_mileage_km' => ['nullable', 'numeric', 'min:0'],
            'standard_replacement_distance_km' => ['nullable', 'numeric', 'min:0'],
            'removed_mileage_km' => ['nullable', 'numeric', 'min:0'],
            'distance_run_km' => ['nullable', 'numeric', 'min:0'],
            'tread_depth_mm' => ['nullable', 'numeric', 'min:0'],
            'tire_size' => ['nullable', 'string', 'max:50'],
            'brand' => ['nullable', 'string', 'max:100'],
            'condition_status' => ['required', Rule::in(array_keys(TireRegistration::conditionOptions()))],
            'vendor_name' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator): void {
                $vehicleId = $this->input('vehicle_id');
                $tirePosition = $this->input('tire_position');
                $installedAt = $this->input('installed_at');
                $installedMileage = $this->input('installed_mileage_km');

                if (! $vehicleId || ! $tirePosition) {
                    return;
                }

                $query = TireRegistration::query()
                    ->where('vehicle_id', $vehicleId)
                    ->where('tire_position', $tirePosition);

                if ($installedAt !== null && $installedAt !== '') {
                    $query->whereDate('installed_at', $installedAt);
                } else {
                    $query->whereNull('installed_at');
                }

                if ($installedMileage !== null && $installedMileage !== '') {
                    $query->where('installed_mileage_km', (float) $installedMileage);
                } else {
                    $query->whereNull('installed_mileage_km');
                }

                if ($query->exists()) {
                    $validator->errors()->add(
                        'tire_position',
                        'ตำแหน่งยาง, วันที่ติดตั้ง และไมล์ที่ติดตั้ง ซ้ำกับข้อมูลเดิมของรถคันนี้'
                    );
                }
            },
        ];
    }
}
