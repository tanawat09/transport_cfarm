<?php

namespace App\Http\Requests;

use App\Models\PreTripInspection;
use Illuminate\Foundation\Http\FormRequest;

class PreTripInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statusRule = 'in:' . implode(',', array_keys(PreTripInspection::statusOptions()));

        return [
            'inspection_date' => ['required', 'date'],
            'inspection_time' => ['required', 'date_format:H:i'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'odometer_km' => ['nullable', 'numeric', 'min:0'],
            'engine_oil_and_fluids_status' => ['required', $statusRule],
            'engine_oil_and_fluids_note' => ['nullable', 'string'],
            'belt_status' => ['required', $statusRule],
            'belt_note' => ['nullable', 'string'],
            'lights_status' => ['required', $statusRule],
            'lights_note' => ['nullable', 'string'],
            'leak_status' => ['required', $statusRule],
            'leak_note' => ['nullable', 'string'],
            'parking_brake_status' => ['required', $statusRule],
            'parking_brake_note' => ['nullable', 'string'],
            'pedals_and_steering_status' => ['required', $statusRule],
            'pedals_and_steering_note' => ['nullable', 'string'],
            'tires_and_wheels_status' => ['required', $statusRule],
            'tires_and_wheels_note' => ['nullable', 'string'],
            'overall_note' => ['nullable', 'string'],
        ];
    }
}
