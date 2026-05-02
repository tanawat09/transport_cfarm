<?php

namespace App\Http\Requests;

use App\Models\PreTripInspection;
use App\Models\PreTripChecklistItem;
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

        $rules = [
            'inspection_date' => ['required', 'date'],
            'inspection_time' => ['required', 'date_format:H:i'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'odometer_km' => ['nullable', 'numeric', 'min:0'],
            'inspection_items' => ['required', 'array'],
            'overall_note' => ['nullable', 'string'],
        ];

        foreach (PreTripChecklistItem::query()->active()->ordered()->get() as $item) {
            $rules["inspection_items.{$item->key}.status"] = ['required', $statusRule];
            $rules["inspection_items.{$item->key}.note"] = ['nullable', 'string'];
        }

        return $rules;
    }
}
