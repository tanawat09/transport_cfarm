@extends('layouts.app')

@php
    $title = 'บันทึกการใช้รถ';
    $subtitle = 'กรอกข้อมูลการใช้รถทั่วไปจาก QR Code หรือเลือกทะเบียนรถจากรายการ';
@endphp

@push('styles')
<style>
    .usage-locked-banner {
        margin-bottom: 1rem;
        padding: 1rem 1.1rem;
        border: 1px solid rgba(59, 130, 246, 0.16);
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.09), rgba(14, 165, 233, 0.06));
        color: #17324d;
    }

    .usage-locked-banner strong {
        color: #0f3c68;
    }

    .usage-form-card {
        overflow: visible;
    }

    .usage-form-card > .card-body {
        overflow: visible;
        padding: 1.35rem;
    }

    .usage-form-grid {
        align-items: stretch;
    }

    .usage-field {
        min-width: 0;
    }

    .usage-field-inner {
        height: 100%;
        padding: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.78);
    }

    .usage-field .form-label {
        display: block;
        margin-bottom: .5rem;
        line-height: 1.35;
    }

    .usage-field .form-control,
    .usage-field .form-select {
        width: 100%;
        max-width: 100%;
    }

    .usage-calculated .form-control {
        font-weight: 800;
        color: #17324d;
        background: rgba(31, 111, 120, 0.06);
    }

    .usage-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .75rem;
        padding-top: .35rem;
    }

    .usage-actions .btn-primary {
        min-width: 170px;
    }

    @media (max-width: 991.98px) {
        .usage-form-card > .card-body {
            padding: 1rem;
        }

        .usage-field-inner {
            padding: .95rem;
        }
    }

    @media (max-width: 767.98px) {
        .usage-locked-banner {
            padding: .9rem 1rem;
            border-radius: 16px;
            font-size: .92rem;
        }

        .usage-form-card {
            border-radius: 18px;
        }

        .usage-form-card > .card-body {
            padding: .85rem;
        }

        .usage-field-inner {
            padding: .85rem;
            border-radius: 14px;
        }

        .usage-field .form-label {
            font-size: .9rem;
        }

        .usage-actions {
            position: sticky;
            bottom: calc(.75rem + env(safe-area-inset-bottom));
            z-index: 20;
            flex-direction: column-reverse;
            align-items: stretch;
            padding: .85rem;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.18);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .usage-actions .btn {
            width: 100%;
            min-height: 50px;
        }

        .usage-actions .btn-primary {
            min-width: 0;
        }
    }

    @media (max-width: 575.98px) {
        .usage-locked-banner {
            margin-bottom: .8rem;
            padding: .8rem;
            border-radius: 14px;
        }

        .usage-form-card {
            margin-left: -4px;
            margin-right: -4px;
            border-radius: 16px;
        }

        .usage-form-card > .card-body {
            padding: .65rem;
        }

        .usage-form-grid {
            --bs-gutter-x: .7rem;
            --bs-gutter-y: .7rem;
        }

        .usage-field-inner {
            padding: .75rem;
        }

        .usage-actions {
            bottom: calc(.5rem + env(safe-area-inset-bottom));
            padding: .75rem;
        }
    }
</style>
@endpush

@section('content')
@if($lockedVehicle)
    <div class="usage-locked-banner">
        เปิดฟอร์มจาก QR Code ของรถ <strong>{{ $lockedVehicle->registration_number }}</strong>
        @if($latestLog)
            <div class="small mt-1">ดึงไมล์ล่าสุดจากวันที่ {{ $latestLog->usage_date?->format('d/m/Y') }}: {{ number_format((float) $latestLog->odometer_end, 2) }}</div>
        @endif
    </div>
@endif

<div class="card usage-form-card">
    <div class="card-body">
        <form method="POST" action="{{ route('vehicle-usage-logs.store') }}" class="row g-3 usage-form-grid" id="vehicle-usage-form">
            @csrf

            <div class="col-md-4 usage-field">
                <div class="usage-field-inner">
                    <label for="usage_date" class="form-label">วันที่ใช้รถ</label>
                    <input type="date" name="usage_date" id="usage_date" value="{{ old('usage_date', optional($log->usage_date)->format('Y-m-d') ?: now()->toDateString()) }}" class="form-control" required>
                </div>
            </div>

            <div class="col-md-4 usage-field usage-calculated">
                <div class="usage-field-inner">
                    <label class="form-label">ประจำเดือน</label>
                    <input type="month" id="usage_month_display" value="{{ old('usage_date', optional($log->usage_date)->format('Y-m-d') ?: now()->toDateString()) ? substr(old('usage_date', optional($log->usage_date)->format('Y-m-d') ?: now()->toDateString()), 0, 7) : now()->format('Y-m') }}" class="form-control" readonly>
                </div>
            </div>

            <div class="col-md-4 usage-field">
                <div class="usage-field-inner">
                    <label for="vehicle_id" class="form-label">ทะเบียนรถ</label>
                    <select name="vehicle_id" id="vehicle_id" class="form-select" required @disabled($lockedVehicle)>
                        <option value="">เลือกทะเบียนรถ</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" @selected((string) old('vehicle_id', $log->vehicle_id) === (string) $vehicle->id)>
                                {{ $vehicle->registration_number }} - {{ $vehicle->vehicle_type }}
                            </option>
                        @endforeach
                    </select>
                    @if($lockedVehicle)
                        <input type="hidden" name="vehicle_id" value="{{ $lockedVehicle->id }}">
                    @endif
                </div>
            </div>

            <div class="col-md-4 usage-field">
                <div class="usage-field-inner">
                    <label for="driver_id" class="form-label">ผู้ขับขี่</label>
                    <input type="text" name="driver_name" id="driver_name" value="{{ old('driver_name', $log->driver_name) }}" class="form-control" placeholder="พิมพ์ชื่อผู้ขับขี่">
                    <select name="driver_id" id="driver_id" class="form-select d-none" disabled>
                        <option value="">เลือกผู้ขับขี่</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}" @selected((string) old('driver_id', $log->driver_id) === (string) $driver->id)>
                                {{ $driver->employee_code }} - {{ $driver->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4 usage-field">
                <div class="usage-field-inner">
                    <label for="odometer_start" class="form-label">ไมล์เริ่มต้น</label>
                    <input type="number" step="0.01" min="0" name="odometer_start" id="odometer_start" value="{{ old('odometer_start', $log->odometer_start) }}" class="form-control">
                </div>
            </div>

            <div class="col-md-4 usage-field">
                <div class="usage-field-inner">
                    <label for="odometer_end" class="form-label">ไมล์สิ้นสุด</label>
                    <input type="number" step="0.01" min="0" name="odometer_end" id="odometer_end" value="{{ old('odometer_end', $log->odometer_end) }}" class="form-control">
                </div>
            </div>

            <div class="col-md-4 usage-field usage-calculated">
                <div class="usage-field-inner">
                    <label class="form-label">ระยะทาง</label>
                    <input type="text" id="distance_display" class="form-control" value="0.00" readonly>
                </div>
            </div>

            <div class="col-md-4 usage-field">
                <div class="usage-field-inner">
                    <label for="fuel_liters" class="form-label">การเติมน้ำมัน (ลิตร)</label>
                    <input type="number" step="0.01" min="0" name="fuel_liters" id="fuel_liters" value="{{ old('fuel_liters', $log->fuel_liters) }}" class="form-control">
                </div>
            </div>

            <div class="col-md-4 usage-field">
                <div class="usage-field-inner">
                    <label for="fuel_price_per_liter" class="form-label">ราคาน้ำมัน (บาท/ลิตร)</label>
                    <input type="number" step="0.01" min="0" name="fuel_price_per_liter" id="fuel_price_per_liter" value="{{ old('fuel_price_per_liter', $log->fuel_price_per_liter) }}" class="form-control">
                </div>
            </div>

            <div class="col-md-4 usage-field usage-calculated">
                <div class="usage-field-inner">
                    <label class="form-label">รวมเงิน</label>
                    <input type="text" id="fuel_total_display" class="form-control" value="0.00" readonly>
                </div>
            </div>

            <div class="col-md-6 usage-field">
                <div class="usage-field-inner">
                    <label for="purpose" class="form-label">วัตถุประสงค์</label>
                    <input type="text" name="purpose" id="purpose" value="{{ old('purpose', $log->purpose) }}" class="form-control">
                </div>
            </div>

            <div class="col-md-6 usage-field">
                <div class="usage-field-inner">
                    <label for="destination" class="form-label">สถานที่เป้าหมาย</label>
                    <input type="text" name="destination" id="destination" value="{{ old('destination', $log->destination) }}" class="form-control">
                </div>
            </div>

            <div class="col-12 usage-field">
                <div class="usage-field-inner">
                    <label for="notes" class="form-label">หมายเหตุ</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $log->notes) }}</textarea>
                </div>
            </div>

            <div class="col-12 usage-actions">
                <button class="btn btn-primary">บันทึกการใช้รถ</button>
                <a href="{{ route('vehicle-usage-logs.index') }}" class="btn btn-outline-secondary">กลับ</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const usageDate = document.getElementById('usage_date');
        const usageMonth = document.getElementById('usage_month_display');
        const start = document.getElementById('odometer_start');
        const end = document.getElementById('odometer_end');
        const distance = document.getElementById('distance_display');
        const liters = document.getElementById('fuel_liters');
        const price = document.getElementById('fuel_price_per_liter');
        const total = document.getElementById('fuel_total_display');

        function toNumber(field) {
            return Number.parseFloat(field?.value || '0') || 0;
        }

        function refreshCalculatedFields() {
            const distanceValue = Math.max(0, toNumber(end) - toNumber(start));
            const totalValue = toNumber(liters) * toNumber(price);

            distance.value = distanceValue.toFixed(2);
            total.value = totalValue.toFixed(2);

            if (usageDate && usageDate.value) {
                usageMonth.value = usageDate.value.substring(0, 7);
            }
        }

        [usageDate, start, end, liters, price].forEach(function (field) {
            field?.addEventListener('input', refreshCalculatedFields);
            field?.addEventListener('change', refreshCalculatedFields);
        });

        refreshCalculatedFields();
    });
</script>
@endpush
