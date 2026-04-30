@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label">วันที่เริ่มต้น</label>
                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">วันที่สิ้นสุด</label>
                <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">รถ</label>
                <select name="vehicle_id" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @selected(($filters['vehicle_id'] ?? null) == $vehicle->id)>
                            {{ $vehicle->registration_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">พนักงานขับ</label>
                <select name="driver_id" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" @selected(($filters['driver_id'] ?? null) == $driver->id)>
                            {{ $driver->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">ฟาร์ม</label>
                <select name="farm_id" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}" @selected(($filters['farm_id'] ?? null) == $farm->id)>
                            {{ $farm->farm_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">คู่สัญญา</label>
                <select name="vendor_id" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" @selected(($filters['vendor_id'] ?? null) == $vendor->id)>
                            {{ $vendor->vendor_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2 justify-content-between flex-wrap">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">ค้นหารายงาน</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">ล้างตัวกรอง</a>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.export.excel', request()->query()) }}" class="btn btn-success">Export Excel</a>
                    <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-danger">Export PDF</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3 h-100">
            <div class="text-muted small">จำนวนเที่ยว</div>
            <div class="h3 mb-0">{{ number_format($summary['total_jobs']) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3 h-100">
            <div class="text-muted small">อาหารรวม (กก.)</div>
            <div class="h3 mb-0">{{ number_format($summary['total_food_weight_kg'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3 h-100">
            <div class="text-muted small">น้ำมันเติมจริงรวม (ลิตร)</div>
            <div class="h3 mb-0">{{ number_format($summary['total_actual_oil_liters'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3 h-100">
            <div class="text-muted small">น้ำมันอนุมัติรวม (ลิตร)</div>
            <div class="h3 mb-0">{{ number_format($summary['total_approved_oil_liters'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3 h-100">
            <div class="text-muted small">ส่วนต่างน้ำมันรวม (ลิตร)</div>
            <div class="h3 mb-0">{{ number_format($summary['total_oil_difference_liters'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3 h-100">
            <div class="text-muted small">ค่าน้ำมันรวม (บาท)</div>
            <div class="h5 mb-0">{{ number_format($summary['total_oil_cost'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="text-muted small">ส่วนต่างน้ำมันรวม (บาท)</div>
            <div class="h5 mb-0">{{ number_format($summary['total_oil_difference_amount'], 2) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="text-muted small">ส่วนต่างระยะทางรวม (กม.)</div>
            <div class="h5 mb-0">{{ number_format($summary['total_distance_difference_km'], 2) }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-1">รายละเอียดรายงานเที่ยวขนส่ง</h5>
            <div class="text-muted small">แสดงข้อมูลครบตามฟอร์มบันทึกเที่ยวขนส่ง</div>
        </div>
        <div class="text-muted small">จำนวน {{ number_format($jobs->total()) }} รายการ</div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-bordered table-sm align-middle text-nowrap">
            <thead class="table-light">
                <tr>
                    <th>วันที่ขนส่ง</th>
                    <th>เลขที่เอกสาร</th>
                    <th>รถ</th>
                    <th>พนักงานขับ</th>
                    <th>ฟาร์ม</th>
                    <th>คู่สัญญา</th>
                    <th class="text-end">จำนวนอาหาร (กก.)</th>
                    <th class="text-end">ไมล์ต้น</th>
                    <th class="text-end">ไมล์ปลาย</th>
                    <th class="text-end">ระยะทางจริง (กม.)</th>
                    <th class="text-end">ระยะทางมาตรฐาน (กม.)</th>
                    <th class="text-end">น้ำมันที่บริษัทกำหนด (ลิตร)</th>
                    <th class="text-end">ชดเชยน้ำมัน (ลิตร)</th>
                    <th>เหตุผลชดเชย</th>
                    <th>รายละเอียดชดเชย</th>
                    <th class="text-end">น้ำมันอนุมัติรวม (ลิตร)</th>
                    <th class="text-end">น้ำมันเติมจริง (ลิตร)</th>
                    <th class="text-end">ราคา/ลิตร</th>
                    <th class="text-end">ค่าน้ำมัน (บาท)</th>
                    <th class="text-end">ส่วนต่างน้ำมัน (ลิตร)</th>
                    <th class="text-end">ส่วนต่างน้ำมัน (บาท)</th>
                    <th class="text-end">ส่วนต่างระยะทาง (กม.)</th>
                    <th class="text-end">อัตราเฉลี่ยน้ำมัน (กม./ลิตร)</th>
                    <th>หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>{{ $job->transport_date?->format('d/m/Y') }}</td>
                        <td>{{ $job->document_no }}</td>
                        <td>
                            {{ $job->vehicle?->registration_number }}
                            @if($job->vehicle?->brand || $job->vehicle?->model)
                                <div class="text-muted small">{{ trim(($job->vehicle?->brand ?? '').' '.($job->vehicle?->model ?? '')) }}</div>
                            @endif
                        </td>
                        <td>
                            {{ $job->driver?->full_name }}
                            @if($job->driver?->employee_code)
                                <div class="text-muted small">{{ $job->driver->employee_code }}</div>
                            @endif
                        </td>
                        <td>{{ $job->farm?->farm_name }}</td>
                        <td>{{ $job->vendor?->vendor_name }}</td>
                        <td class="text-end">{{ number_format((float) $job->food_weight_kg, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->odometer_start, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->odometer_end, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->actual_distance_km, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->standard_distance_km, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->company_oil_liters, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->oil_compensation_liters, 2) }}</td>
                        <td>{{ $job->oilCompensationReason?->reason_name ?: '-' }}</td>
                        <td class="text-wrap" style="min-width: 220px;">{{ $job->oil_compensation_details ?: '-' }}</td>
                        <td class="text-end">{{ number_format((float) $job->approved_oil_liters, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->actual_oil_liters, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->oil_price_per_liter, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->total_oil_cost, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->oil_difference_liters, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->oil_difference_amount, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->distance_difference_km, 2) }}</td>
                        <td class="text-end">{{ number_format((float) $job->average_fuel_rate_km_per_liter, 2) }}</td>
                        <td class="text-wrap" style="min-width: 220px;">{{ $job->notes ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="24" class="text-center text-muted">ไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $jobs->links() }}
    </div>
</div>
@endsection
