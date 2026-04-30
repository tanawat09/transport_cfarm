<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $driverId = $this->route('driver')?->id;

        return [
            'employee_code' => ['required', 'string', 'max:50', Rule::unique('drivers', 'employee_code')->ignore($driverId)->whereNull('deleted_at')],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'driving_license_number' => ['required', 'string', 'max:100', Rule::unique('drivers', 'driving_license_number')->ignore($driverId)->whereNull('deleted_at')],
            'driving_license_expiry_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
