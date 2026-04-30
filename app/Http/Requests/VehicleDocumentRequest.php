<?php

namespace App\Http\Requests;

use App\Models\VehicleDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $documentId = $this->route('vehicle_document')?->id;

        return [
            'vehicle_id' => ['required', 'integer', Rule::exists('vehicles', 'id')->whereNull('deleted_at')],
            'document_type' => [
                'required',
                Rule::in(array_keys(VehicleDocument::typeOptions())),
                Rule::unique('vehicle_documents', 'document_type')
                    ->where(fn ($query) => $query->where('vehicle_id', $this->input('vehicle_id')))
                    ->ignore($documentId),
            ],
            'document_no' => ['nullable', 'string', 'max:100'],
            'provider_name' => ['nullable', 'string', 'max:150'],
            'insurance_capital' => ['nullable', 'numeric', 'min:0'],
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['required', 'date'],
            'tax_expires_at' => ['nullable', 'date'],
            'compulsory_expires_at' => ['nullable', 'date'],
            'insurance_expires_at' => ['nullable', 'date'],
            'alert_before_days' => ['required', 'integer', 'min:1', 'max:365'],
            'is_alert_enabled' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
