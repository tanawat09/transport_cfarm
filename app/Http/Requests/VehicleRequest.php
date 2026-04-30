<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')?->id;

        return [
            'registration_number' => ['required', 'string', 'max:50', Rule::unique('vehicles', 'registration_number')->ignore($vehicleId)->whereNull('deleted_at')],
            'registered_at' => ['nullable', 'date'],
            'vehicle_type' => ['nullable', 'string', 'max:100'],
            'towing_vehicle' => ['nullable', 'string', 'max:100'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'capacity_kg' => ['nullable', 'numeric', 'min:0'],
            'standard_fuel_rate_km_per_liter' => ['nullable', 'numeric', 'min:0'],
            'primary_driver_id' => ['nullable', 'integer', Rule::exists('drivers', 'id')->whereNull('deleted_at')],
            'status' => ['required', Rule::in(['active', 'inactive', 'maintenance'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
