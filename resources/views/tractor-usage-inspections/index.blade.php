@extends('layouts.app')

@php
    $title = 'บันทึกตรวจเช็กการใช้งานรถไถ';
    $subtitle = 'สรุปผลตรวจรถไถ แยกพร้อมใช้งานและต้องติดตาม เพื่อดูสถานะประจำวันได้รวดเร็ว';
@endphp

@push('styles')
<style>
    .tractor-report-hero {
        background: linear-gradient(135deg, #184d57, #1f6f78);
        color: #fff;
        border-radius: 18px;
        padding: 1.4rem;
        box-shadow: 0 18px 36px rgba(24, 77, 87, 0.18);
    }

    .tractor-stat-card {
        border: 1px solid rgba(148, 163, 184, 0.18);
        min-height: 118px;
    }

    .tractor-stat-label {
        color: #647487;
        font-size: .9rem;
    }

    .tractor-stat-value {
        font-size: 2rem;
        font-weight: 800;
    }

    .tractor-bar-track {
        height: 8px;
        border-radius: 999px;
        overflow: hidden;
        background: #edf2f7;
    }

    .tractor-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="tractor-report-hero mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="small opacity-75">ช่วงรายงาน {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</div>
            <h2 class="h3 fw-bold mb-1">รายงานตรวจเช็กการใช้งานรถไถ</h2>
            <p class="mb-0 opacity-75">ดูภาพรวมการตรวจรถไถในแต่ละวัน พร้อมหัวข้อที่ไม่ผ่านบ่อยเพื่อวางแผนซ่อมและติดตามหน้างาน</p>
        </div>
        <a href="{{ route('tractor-usage-inspections.create') }}" class="btn btn-light">บันทึกตรวจเช็กรถไถ</a>
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
            <div class="col-md-2">
                <label class="form-label">รถไถ</label>
                <select name="vehicle_id" class="form-select">
                    <option value="">ทุกคัน</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @selected((string) request('vehicle_id') === (string) $vehicle->id)>
                            {{ $vehicle->registration_number }} - {{ $vehicle->vehicle_type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">ฟาร์ม</label>
                <select name="farm_id" class="form-select">
                    <option value="">ทุกฟาร์ม</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}" @selected((string) request('farm_id') === (string) $farm->id)>
                            {{ $farm->farm_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="">ทั้งหมด</option>
                    <option value="ready" @selected(request('status') === 'ready')>พร้อมใช้งาน</option>
                    <option value="not_ready" @selected(request('status') === 'not_ready')>ต้องตรวจซ้ำ</option>
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
        <div class="card tractor-stat-card"><div class="card-body"><div class="tractor-stat-label">รายการตรวจทั้งหมด</div><div class="tractor-stat-value">{{ number_format($totalCount) }}</div><div class="text-muted small">รายการ</div></div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card tractor-stat-card text-bg-success"><div class="card-body"><div class="small opacity-75">พร้อมใช้งาน</div><div class="tractor-stat-value">{{ number_format($readyCount) }}</div><div class="small opacity-75">{{ number_format($readyPercent, 1) }}%</div></div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card tractor-stat-card text-bg-danger"><div class="card-body"><div class="small opacity-75">ต้องตรวจซ้ำ</div><div class="tractor-stat-value">{{ number_format($notReadyCount) }}</div><div class="small opacity-75">ติดตามก่อนใช้งาน</div></div></div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card tractor-stat-card"><div class="card-body"><div class="tractor-stat-label">รถไถที่ถูกตรวจ</div><div class="tractor-stat-value">{{ number_format($inspections->getCollection()->pluck('vehicle_id')->filter()->unique()->count()) }}</div><div class="text-muted small">จากหน้าปัจจุบัน</div></div></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="fw-bold mb-3">หัวข้อที่ไม่ผ่านบ่อย</div>
                @php $maxFail = max(1, $failureStats->max('count')); @endphp
                @foreach($failureStats as $stat)
                    @php $percent = max(3, ($stat['count'] / $maxFail) * 100); @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between gap-3 mb-1">
                            <span class="small text-truncate">{{ $stat['label'] }}</span>
                            <strong class="small">{{ number_format($stat['count']) }}</strong>
                        </div>
                        <div class="tractor-bar-track"><div class="tractor-bar-fill" style="width: {{ $percent }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-body table-responsive">
                <div class="h5 fw-bold mb-3">รายการตรวจเช็ก</div>
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>เวลา</th>
                            <th>รถไถ</th>
                            <th>ฟาร์ม</th>
                            <th class="text-end">ชั่วโมงงาน</th>
                            <th>ผู้บันทึก</th>
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
                            <td>{{ $inspection->farm?->farm_name ?? '-' }}</td>
                            <td class="text-end">{{ number_format((float) $inspection->working_hours, 2) }}</td>
                            <td>{{ $inspection->user?->name ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $inspection->is_ready_for_use ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ $inspection->is_ready_for_use ? 'พร้อมใช้งาน' : 'ต้องตรวจซ้ำ' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('tractor-usage-inspections.show', $inspection) }}" class="btn btn-sm btn-info text-white">ดู</a>
                                <a href="{{ route('tractor-usage-inspections.edit', $inspection) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                                <form method="POST" action="{{ route('tractor-usage-inspections.destroy', $inspection) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบรายการตรวจเช็กรถไถนี้?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">ลบ</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">ยังไม่มีรายการตรวจเช็กรถไถ</td></tr>
                    @endforelse
                    </tbody>
                </table>
                {{ $inspections->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
