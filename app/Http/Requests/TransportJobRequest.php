<?php

namespace App\Http\Requests;

use App\Models\OilCompensationReason;
use App\Models\RouteStandard;
use Illuminate\Foundation\Http\FormRequest;

class TransportJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transport_date' => ['required', 'date'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'farm_id' => ['required', 'exists:farms,id'],
            'vendor_id' => ['required', 'exists:vendors,id'],
            'route_standard_id' => ['required', 'exists:route_standards,id'],
            'food_weight_kg' => ['required', 'numeric', 'min:0'],
            'odometer_start' => ['required', 'numeric', 'min:0'],
            'odometer_end' => ['required', 'numeric', 'min:0', 'gte:odometer_start'],
            'oil_compensation_liters' => ['nullable', 'numeric', 'min:0'],
            'oil_compensation_reason_id' => ['nullable', 'exists:oil_compensation_reasons,id'],
            'oil_compensation_details' => ['nullable', 'string'],
            'actual_oil_liters' => ['required', 'numeric', 'min:0'],
            'oil_price_per_liter' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $routeStandard = RouteStandard::query()
                ->whereKey($this->input('route_standard_id'))
                ->where('farm_id', $this->input('farm_id'))
                ->where('vendor_id', $this->input('vendor_id'))
                ->where('status', 'active')
                ->first();

            if (! $routeStandard) {
                $validator->errors()->add('route_standard_id', 'ไม่พบมาตรฐานเส้นทางที่ใช้งานได้สำหรับฟาร์มและคู่สัญญานี้');
            }

            $compensationLiters = (float) $this->input('oil_compensation_liters', 0);
            $reasonId = $this->input('oil_compensation_reason_id');
            $details = trim((string) $this->input('oil_compensation_details'));

            if ($compensationLiters > 0 && empty($reasonId)) {
                $validator->errors()->add('oil_compensation_reason_id', 'เมื่อมีชดเชยน้ำมัน ต้องระบุเหตุผลชดเชย');
            }

            if ($reasonId) {
                $reason = OilCompensationReason::query()->find($reasonId);

                if ($reason && ($reason->requires_details || $reason->reason_name === 'อื่นๆ') && $details === '') {
                    $validator->errors()->add('oil_compensation_details', 'เหตุผลนี้ต้องระบุรายละเอียดเพิ่มเติม');
                }
            }
        });
    }
}
