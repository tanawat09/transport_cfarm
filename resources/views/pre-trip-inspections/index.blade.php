@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">วันที่ตรวจ</label>
                <input type="date" name="inspection_date" value="{{ request('inspection_date') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">รถ</label>
                <select name="vehicle_id" class="form-select">
                    <option value="">ทุกคัน</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @selected((string) request('vehicle_id') === (string) $vehicle->id)>
                            {{ $vehicle->registration_number }} - {{ $vehicle->brand }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">ค้นหา</button>
                <a href="{{ route('pre-trip-inspections.index') }}" class="btn btn-outline-secondary">ล้าง</a>
            </div>
            <div class="col text-end">
                <a href="{{ route('pre-trip-inspections.create') }}" class="btn btn-success">บันทึกตรวจเช็กรถก่อนวิ่ง</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>วันที่</th>
                    <th>เวลา</th>
                    <th>รถ</th>
                    <th>พนักงานขับ</th>
                    <th>เลขไมล์</th>
                    <th>ผู้ตรวจ</th>
                    <th>สถานะ</th>
                    <th class="text-end">จัดการ</th>
                </tr>
            </thead>
            <tbody>
            @forelse($inspections as $inspection)
                <tr>
                    <td>{{ $inspection->inspection_date?->format('d/m/Y') }}</td>
                    <td>{{ \Illuminate\Support\Str::of($inspection->inspection_time)->substr(0, 5) }}</td>
                    <td>{{ $inspection->vehicle?->registration_number }}</td>
                    <td>{{ $inspection->driver?->full_name ?? '-' }}</td>
                    <td>{{ $inspection->odometer_km !== null ? number_format((float) $inspection->odometer_km, 2) : '-' }}</td>
                    <td>{{ $inspection->user?->name }}</td>
                    <td>
                        <span class="badge {{ $inspection->is_ready_to_drive ? 'text-bg-success' : 'text-bg-danger' }}">
                            {{ $inspection->is_ready_to_drive ? 'พร้อมวิ่ง' : 'ไม่พร้อมวิ่ง' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('pre-trip-inspections.show', $inspection) }}" class="btn btn-sm btn-info text-white">ดู</a>
                        <a href="{{ route('pre-trip-inspections.edit', $inspection) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                        <form method="POST" action="{{ route('pre-trip-inspections.destroy', $inspection) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบรายการตรวจเช็กนี้?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">ลบ</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">ยังไม่มีรายการตรวจเช็กรถก่อนวิ่ง</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        {{ $inspections->links() }}
    </div>
</div>
@endsection

@php
    $title = 'รายงานตรวจเช็กรถก่อนวิ่ง';
    $subtitle = 'สรุปผลตรวจรถก่อนออกวิ่ง แยกผ่าน/ไม่ผ่าน พร้อมหัวข้อที่ต้องติดตาม';
@endphp

@push('styles')
<style>
    .inspection-hero { background: linear-gradient(135deg, #17324d, #1f6f78); color: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 16px 36px rgba(23,50,77,.16); }
    .report-card { border: 1px solid #e7edf3; min-height: 118px; }
    .report-label { color: #647487; font-size: .9rem; }
    .report-value { font-size: 2rem; font-weight: 800; letter-spacing: 0; }
    .bar-track { height: 10px; background: #edf2f7; border-radius: 999px; overflow: hidden; }
    .bar-fill { height: 100%; background: #1f6f78; border-radius: 999px; }
    .fail-bar { background: #dc3545; }
    .table-report th { white-space: nowrap; }
</style>
@endpush

@section('content')
<div class="inspection-hero mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="small opacity-75">ช่วงรายงาน {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</div>
            <h2 class="h3 fw-bold mb-1">รายงานตรวจเช็กรถก่อนวิ่ง</h2>
            <p class="mb-0 opacity-75">ดูภาพรวมความพร้อมของรถ และรายการไม่ผ่านที่ต้องติดตามก่อนออกวิ่ง</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('pre-trip-inspections.export.pdf', request()->query()) }}" class="btn btn-warning">ออก PDF</a>
            <a href="{{ route('pre-trip-inspections.create') }}" class="btn btn-light">บันทึกตรวจเช็กรถ</a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">ตั้งแต่วันที่</label>
                <input type="date" name="date_from" value="{{ request('date_from', $dateFrom) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">ถึงวันที่</label>
                <input type="date" name="date_to" value="{{ request('date_to', $dateTo) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">รถ</label>
                <select name="vehicle_id" class="form-select">
                    <option value="">ทุกคัน</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @selected((string) request('vehicle_id') === (string) $vehicle->id)>
                            {{ $vehicle->registration_number }} - {{ $vehicle->brand }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="">ทั้งหมด</option>
                    <option value="ready" @selected(request('status') === 'ready')>พร้อมวิ่ง</option>
                    <option value="not_ready" @selected(request('status') === 'not_ready')>ไม่พร้อมวิ่ง</option>
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-primary">แสดง</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card report-card"><div class="card-body"><div class="report-label">ตรวจทั้งหมด</div><div class="report-value">{{ number_format($totalCount) }}</div><div class="text-muted small">รายการ</div></div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card report-card text-bg-success"><div class="card-body"><div class="small opacity-75">พร้อมวิ่ง</div><div class="report-value">{{ number_format($readyCount) }}</div><div class="small opacity-75">{{ number_format($readyPercent, 1) }}%</div></div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card report-card text-bg-danger"><div class="card-body"><div class="small opacity-75">ไม่พร้อมวิ่ง</div><div class="report-value">{{ number_format($notReadyCount) }}</div><div class="small opacity-75">ต้องตรวจซ้ำ/แก้ไข</div></div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card report-card"><div class="card-body"><div class="report-label">รถที่ถูกตรวจ</div><div class="report-value">{{ number_format($inspections->getCollection()->pluck('vehicle_id')->filter()->unique()->count()) }}</div><div class="text-muted small">จากหน้าปัจจุบัน</div></div></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-body">
                <div class="h5 fw-bold mb-3">หัวข้อที่ไม่ผ่านบ่อย</div>
                @php $maxFail = max(1, $checkFailureStats->max('count')); @endphp
                @foreach($checkFailureStats as $stat)
                    @php $percent = max(3, ($stat['count'] / $maxFail) * 100); @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between gap-3 small mb-1">
                            <span class="text-truncate">{{ $stat['label'] }}</span>
                            <strong>{{ number_format($stat['count']) }}</strong>
                        </div>
                        <div class="bar-track"><div class="bar-fill fail-bar" style="width: {{ $percent }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-body table-responsive">
                <div class="h5 fw-bold mb-3">รายการตรวจเช็ก</div>
                <table class="table table-hover align-middle table-report">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>เวลา</th>
                            <th>รถ</th>
                            <th>พนักงานขับ</th>
                            <th class="text-end">เลขไมล์</th>
                            <th>ผู้ตรวจ</th>
                            <th>สถานะ</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($inspections as $inspection)
                        <tr>
                            <td>{{ $inspection->inspection_date?->format('d/m/Y') }}</td>
                            <td>{{ \Illuminate\Support\Str::of($inspection->inspection_time)->substr(0, 5) }}</td>
                            <td class="fw-semibold">{{ $inspection->vehicle?->registration_number }}</td>
                            <td>{{ $inspection->driver?->full_name ?? '-' }}</td>
                            <td class="text-end">{{ $inspection->odometer_km !== null ? number_format((float) $inspection->odometer_km, 2) : '-' }}</td>
                            <td>{{ $inspection->user?->name }}</td>
                            <td>
                                <span class="badge {{ $inspection->is_ready_to_drive ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ $inspection->is_ready_to_drive ? 'พร้อมวิ่ง' : 'ไม่พร้อมวิ่ง' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('pre-trip-inspections.show', $inspection) }}" class="btn btn-sm btn-info text-white">ดู</a>
                                <a href="{{ route('pre-trip-inspections.edit', $inspection) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                                <form method="POST" action="{{ route('pre-trip-inspections.destroy', $inspection) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบรายการตรวจเช็กนี้?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">ลบ</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">ยังไม่มีรายการตรวจเช็กรถก่อนวิ่ง</td></tr>
                    @endforelse
                    </tbody>
                </table>
                {{ $inspections->links() }}
            </div>
        </div>
    </div>
</div>
@overwrite
