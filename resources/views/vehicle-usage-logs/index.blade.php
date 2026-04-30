@extends('layouts.app')

@php
    $title = 'บันทึกการใช้รถ';
    $subtitle = 'ติดตามการใช้รถทั่วไปผ่าน QR Code พร้อมคำนวณระยะทางและค่าน้ำมันอัตโนมัติ';
@endphp

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">ทะเบียนรถ</label>
                <select name="vehicle_id" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @selected((string) request('vehicle_id') === (string) $vehicle->id)>
                            {{ $vehicle->registration_number }} - {{ $vehicle->vehicle_type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">ประจำเดือน</label>
                <input type="month" name="usage_month" value="{{ request('usage_month') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">ค้นหา</label>
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="วัตถุประสงค์, สถานที่, คนขับ">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">ค้นหา</button>
                <a href="{{ route('vehicle-usage-logs.index') }}" class="btn btn-outline-secondary">ล้าง</a>
            </div>
            <div class="col text-end">
                <a href="{{ route('vehicle-usage-logs.create') }}" class="btn btn-success">เพิ่มบันทึก</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr>
                    <th>วันที่</th>
                    <th>ทะเบียนรถ</th>
                    <th>ประเภทรถ</th>
                    <th class="text-end">ไมล์เริ่มต้น</th>
                    <th class="text-end">ไมล์สิ้นสุด</th>
                    <th class="text-end">ระยะทาง</th>
                    <th>วัตถุประสงค์</th>
                    <th>สถานที่เป้าหมาย</th>
                    <th class="text-end">เติมน้ำมัน</th>
                    <th class="text-end">บาท/ลิตร</th>
                    <th class="text-end">รวมเงิน</th>
                    <th>ผู้ขับขี่</th>
                    <th>หมายเหตุ</th>
                    <th class="text-end">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->usage_date?->format('d/m/Y') }}</td>
                        <td class="fw-semibold">{{ $log->vehicle?->registration_number }}</td>
                        <td>{{ $log->vehicle?->vehicle_type ?: '-' }}</td>
                        <td class="text-end">{{ $log->odometer_start !== null ? number_format((float) $log->odometer_start, 2) : '-' }}</td>
                        <td class="text-end">{{ $log->odometer_end !== null ? number_format((float) $log->odometer_end, 2) : '-' }}</td>
                        <td class="text-end">{{ number_format((float) $log->distance_km, 2) }}</td>
                        <td>{{ $log->purpose ?: '-' }}</td>
                        <td>{{ $log->destination ?: '-' }}</td>
                        <td class="text-end">{{ $log->fuel_liters !== null ? number_format((float) $log->fuel_liters, 2) : '-' }}</td>
                        <td class="text-end">{{ $log->fuel_price_per_liter !== null ? number_format((float) $log->fuel_price_per_liter, 2) : '-' }}</td>
                        <td class="text-end">{{ number_format((float) $log->fuel_total_amount, 2) }}</td>
                        <td>{{ $log->driver_name ?: ($log->driver?->full_name ?: '-') }}</td>
                        <td>{{ $log->notes ?: '-' }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('vehicle-usage-logs.destroy', $log) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบบันทึกการใช้รถ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">ลบ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center text-muted">ยังไม่มีข้อมูลบันทึกการใช้รถ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $logs->links() }}
    </div>
</div>
@endsection
