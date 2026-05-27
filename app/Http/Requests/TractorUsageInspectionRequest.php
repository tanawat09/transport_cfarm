<?php

namespace App\Http\Requests;

use App\Models\TractorUsageInspection;
use Illuminate\Foundation\Http\FormRequest;

class TractorUsageInspectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $statusRule = 'in:' . implode(',', array_keys(TractorUsageInspection::statusOptions()));

        $rules = [
            'inspection_date' => ['required', 'date'],
            'inspection_time' => ['required', 'date_format:H:i'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'farm_id' => ['required', 'exists:farms,id'],
            'working_hours' => ['required', 'numeric', 'min:0'],
            'inspection_items' => ['required', 'array'],
            'overall_note' => ['nullable', 'string'],
        ];

        foreach (TractorUsageInspection::checklistItems() as $item) {
            $rules["inspection_items.{$item['key']}.status"] = ['required', $statusRule];
            $rules["inspection_items.{$item['key']}.note"] = ['nullable', 'string'];
        }

        return $rules;
    }
}
