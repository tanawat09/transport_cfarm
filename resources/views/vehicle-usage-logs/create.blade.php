@extends('layouts.app')

@php
    $title = 'บันทึกการใช้รถ';
    $subtitle = 'กรอกข้อมูลการใช้รถทั่วไปจาก QR Code หรือเลือกทะเบียนรถจากรายการ';
@endphp

@section('content')
@if($lockedVehicle)
    <div class="alert alert-info">
        เปิดฟอร์มจาก QR Code ของรถ <strong>{{ $lockedVehicle->registration_number }}</strong>
        @if($latestLog)
            <div class="small mt-1">ดึงไมล์ล่าสุดจากวันที่ {{ $latestLog->usage_date?->format('d/m/Y') }}: {{ number_format((float) $latestLog->odometer_end, 2) }}</div>
        @endif
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('vehicle-usage-logs.store') }}" class="row g-3" id="vehicle-usage-form">
            @csrf

            <div class="col-md-4">
                <label for="usage_date" class="form-label">วันที่ใช้รถ</label>
                <input type="date" name="usage_date" id="usage_date" value="{{ old('usage_date', optional($log->usage_date)->format('Y-m-d') ?: now()->toDateString()) }}" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">ประจำเดือน</label>
                <input type="month" id="usage_month_display" value="{{ old('usage_date', optional($log->usage_date)->format('Y-m-d') ?: now()->toDateString()) ? substr(old('usage_date', optional($log->usage_date)->format('Y-m-d') ?: now()->toDateString()), 0, 7) : now()->format('Y-m') }}" class="form-control" readonly>
            </div>

            <div class="col-md-4">
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

            <div class="col-md-4">
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

            <div class="col-md-4">
                <label for="odometer_start" class="form-label">ไมล์เริ่มต้น</label>
                <input type="number" step="0.01" min="0" name="odometer_start" id="odometer_start" value="{{ old('odometer_start', $log->odometer_start) }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label for="odometer_end" class="form-label">ไมล์สิ้นสุด</label>
                <input type="number" step="0.01" min="0" name="odometer_end" id="odometer_end" value="{{ old('odometer_end', $log->odometer_end) }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">ระยะทาง</label>
                <input type="text" id="distance_display" class="form-control" value="0.00" readonly>
            </div>

            <div class="col-md-4">
                <label for="fuel_liters" class="form-label">การเติมน้ำมัน (ลิตร)</label>
                <input type="number" step="0.01" min="0" name="fuel_liters" id="fuel_liters" value="{{ old('fuel_liters', $log->fuel_liters) }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label for="fuel_price_per_liter" class="form-label">ราคาน้ำมัน (บาท/ลิตร)</label>
                <input type="number" step="0.01" min="0" name="fuel_price_per_liter" id="fuel_price_per_liter" value="{{ old('fuel_price_per_liter', $log->fuel_price_per_liter) }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">รวมเงิน</label>
                <input type="text" id="fuel_total_display" class="form-control" value="0.00" readonly>
            </div>

            <div class="col-md-6">
                <label for="purpose" class="form-label">วัตถุประสงค์</label>
                <input type="text" name="purpose" id="purpose" value="{{ old('purpose', $log->purpose) }}" class="form-control">
            </div>

            <div class="col-md-6">
                <label for="destination" class="form-label">สถานที่เป้าหมาย</label>
                <input type="text" name="destination" id="destination" value="{{ old('destination', $log->destination) }}" class="form-control">
            </div>

            <div class="col-12">
                <label for="notes" class="form-label">หมายเหตุ</label>
                <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $log->notes) }}</textarea>
            </div>

            <div class="col-12">
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
