@extends('layouts.app')

@php
    $title = 'รายงานยางใกล้เปลี่ยน';
    $subtitle = 'ติดตามยางที่ใกล้ถึงระยะเปลี่ยนยางมาตรฐานหรือถึงกำหนดเปลี่ยน พร้อมคำนวณไมล์ปัจจุบันอัตโนมัติตามประเภทรถ';
@endphp

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('tire-registrations.report') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">ประเภทรถ</label>
                <select name="vehicle_type" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($vehicleTypes as $vehicleType)
                        <option value="{{ $vehicleType }}" @selected($selectedVehicleType === $vehicleType)>{{ $vehicleType }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">ทะเบียนรถ</label>
                <select name="vehicle_id" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @selected($selectedVehicleId === $vehicle->id)>{{ $vehicle->registration_number }} - {{ $vehicle->vehicle_type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="">ทั้งหมด</option>
                    <option value="warning" @selected($selectedStatus === 'warning')>ใกล้เปลี่ยน</option>
                    <option value="replace" @selected($selectedStatus === 'replace')>ถึงกำหนดเปลี่ยน</option>
                    <option value="normal" @selected($selectedStatus === 'normal')>ปกติ</option>
                    <option value="no_standard" @selected($selectedStatus === 'no_standard')>ยังไม่กำหนดระยะ</option>
                    <option value="no_mileage" @selected($selectedStatus === 'no_mileage')>ข้อมูลไมล์ไม่พอ</option>
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <button class="btn btn-primary">ค้นหารายงาน</button>
                <a href="{{ route('tire-registrations.report') }}" class="btn btn-outline-secondary">ล้างตัวกรอง</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">ทั้งหมด</div>
                <div class="display-6 fw-bold">{{ number_format($summary['total']) }}</div>
                <div class="text-muted small">ตำแหน่งยาง</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-warning-subtle">
            <div class="card-body">
                <div class="text-muted small">ใกล้เปลี่ยน</div>
                <div class="display-6 fw-bold text-warning">{{ number_format($summary['warning']) }}</div>
                <div class="text-muted small">มากกว่า 80%</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-danger-subtle">
            <div class="card-body">
                <div class="text-muted small">ถึงกำหนดเปลี่ยน</div>
                <div class="display-6 fw-bold text-danger">{{ number_format($summary['replace']) }}</div>
                <div class="text-muted small">ครบหรือเกิน 100%</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">ข้อมูลยังไม่ครบ</div>
                <div class="display-6 fw-bold text-secondary">{{ number_format($summary['missing']) }}</div>
                <div class="text-muted small">ยังไม่กำหนดระยะหรือยังไม่มีไมล์</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="fw-semibold">สถานะยางตามระยะเปลี่ยนยางมาตรฐาน</div>
            <div class="text-muted small">กลุ่มรถทั่วไปใช้ไมล์จากบันทึกการใช้รถ ส่วนลากจูงและรถกึ่งพ่วงใช้งานจากไมล์ปลายในเที่ยวขนส่ง</div>
        </div>
        <div class="text-muted small">ทั้งหมด {{ number_format($rows->count()) }} รายการ</div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr>
                    <th>ทะเบียนรถ</th>
                    <th>ประเภทรถ</th>
                    <th>ตำแหน่ง</th>
                    <th>รหัสยาง</th>
                    <th>วันที่ติดตั้ง</th>
                    <th class="text-end">ไมล์ที่ติดตั้ง</th>
                    <th class="text-end">ไมล์ปัจจุบัน</th>
                    <th class="text-end">ระยะใช้งาน</th>
                    <th class="text-end">ระยะเปลี่ยนยางมาตรฐาน</th>
                    <th class="text-end">คงเหลือ</th>
                    <th class="text-end">ใช้งานแล้ว</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    @php
                        $vehicle = $row['vehicle'];
                        $registration = $row['registration'];
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $vehicle?->registration_number ?: '-' }}</td>
                        <td>{{ $vehicle?->vehicle_type ?: '-' }}</td>
                        <td>{{ $registration->tire_position }}</td>
                        <td>{{ $registration->tire_serial_number }}</td>
                        <td>{{ optional($registration->installed_at)->format('d/m/Y') ?: '-' }}</td>
                        <td class="text-end">{{ $registration->installed_mileage_km !== null ? number_format($registration->installed_mileage_km, 2) : '-' }}</td>
                        <td class="text-end">{{ $row['current_mileage_km'] !== null ? number_format($row['current_mileage_km'], 2) : '-' }}</td>
                        <td class="text-end">{{ $row['distance_used_km'] !== null ? number_format($row['distance_used_km'], 2) : '-' }}</td>
                        <td class="text-end">{{ $registration->standard_replacement_distance_km !== null ? number_format($registration->standard_replacement_distance_km, 2) : '-' }}</td>
                        <td class="text-end">
                            @if($row['remaining_distance_km'] !== null)
                                {{ number_format($row['remaining_distance_km'], 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end">
                            @if($row['usage_percent'] !== null)
                                {{ number_format($row['usage_percent'], 2) }}%
                            @else
                                -
                            @endif
                        </td>
                        <td><span class="badge {{ $row['alert_badge_class'] }}">{{ $row['alert_label'] }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-4">ยังไม่มีข้อมูลรายงานยางตามเงื่อนไขที่เลือก</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
