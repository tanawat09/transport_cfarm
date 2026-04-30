@extends('layouts.app')

@php
    $title = 'รายงานการขนส่ง';
    $subtitle = 'สรุปเที่ยวขนส่ง น้ำมัน ระยะทาง และต้นทุน พร้อมค้นหาข้อมูลย้อนหลังได้ในหน้าเดียว';
@endphp

@push('styles')
<style>
    .report-filter-card {
        border-radius: 22px;
        overflow: hidden;
    }

    .report-filter-strip {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        background: linear-gradient(135deg, rgba(31, 111, 120, 0.08), rgba(23, 50, 77, 0.04));
    }

    .summary-card .value {
        font-size: 1.8rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .summary-card .label {
        color: #64748b;
        font-size: .88rem;
        font-weight: 700;
    }

    .summary-card .note {
        color: #8a96a3;
        font-size: .82rem;
    }

    .formula-box {
        border: 1px dashed rgba(148, 163, 184, 0.3);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.72);
    }
</style>
@endpush

@section('content')
<div class="card report-filter-card mb-4">
    <div class="report-filter-strip d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="fw-semibold">ตัวกรองรายงาน</div>
            <div class="text-muted small">เลือกช่วงวันที่และเงื่อนไขที่ต้องการก่อนดูรายงานหรือส่งออก</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('reports.export.excel', request()->query()) }}" class="btn btn-success btn-sm">Export Excel</a>
            <a href="{{ route('reports.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">Export PDF</a>
        </div>
    </div>
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
                <label class="form-label">ทะเบียน</label>
                <select name="vehicle_id" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @selected(($filters['vehicle_id'] ?? null) == $vehicle->id)>{{ $vehicle->registration_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">พนักงานขับ</label>
                <select name="driver_id" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" @selected(($filters['driver_id'] ?? null) == $driver->id)>{{ $driver->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">ฟาร์ม</label>
                <select name="farm_id" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}" @selected(($filters['farm_id'] ?? null) == $farm->id)>{{ $farm->farm_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">คู่สัญญา</label>
                <select name="vendor_id" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" @selected(($filters['vendor_id'] ?? null) == $vendor->id)>{{ $vendor->vendor_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2 justify-content-between flex-wrap pt-2">
                <div class="text-muted small">สามารถกรองเฉพาะทะเบียนรถลากจูงเพื่อดูผลการขนส่งของแต่ละคันได้โดยตรง</div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">ค้นหารายงาน</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">ล้างตัวกรอง</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'จำนวนเที่ยวขนส่ง', 'value' => number_format($summary['total_jobs']), 'note' => 'เที่ยว'],
        ['label' => 'น้ำหนักอาหารรวม', 'value' => number_format($summary['total_food_weight_kg'], 2), 'note' => 'กก.'],
        ['label' => 'น้ำมันเติมจริงรวม', 'value' => number_format($summary['total_actual_oil_liters'], 2), 'note' => 'ลิตร'],
        ['label' => 'น้ำมันอนุมัติรวม', 'value' => number_format($summary['total_approved_oil_liters'], 2), 'note' => 'ลิตร'],
        ['label' => 'ต้นทุนน้ำมันรวม', 'value' => number_format($summary['total_oil_cost'], 2), 'note' => 'บาท'],
        ['label' => 'ต้นทุนน้ำมันต่ออาหาร 1 กก.', 'value' => number_format($summary['oil_cost_per_kg'], 2), 'note' => 'บาท / กก.'],
        ['label' => 'ส่วนต่างน้ำมัน', 'value' => number_format($summary['total_oil_difference_liters'], 2), 'note' => 'ลิตร'],
        ['label' => 'ส่วนต่างระยะทาง', 'value' => number_format($summary['total_distance_difference_km'], 2), 'note' => 'กม.'],
    ] as $item)
        <div class="col-sm-6 col-xl-3">
            <div class="card summary-card h-100">
                <div class="card-body">
                    <div class="label">{{ $item['label'] }}</div>
                    <div class="value mt-2">{{ $item['value'] }}</div>
                    <div class="note mt-2">{{ $item['note'] }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="formula-box p-3 mb-4">
    <div class="fw-semibold mb-1">คำอธิบายสรุป</div>
    <div class="text-muted small">ประสิทธิภาพการบรรทุกอาหารคำนวณจากน้ำหนักอาหารรวมเทียบกับความจุรถรวมของเที่ยวที่เลือก และต้นทุนต่างๆ จะอ้างอิงจากข้อมูลที่บันทึกในเที่ยวขนส่งจริง</div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="fw-semibold">รายละเอียดเที่ยวขนส่ง</div>
            <div class="text-muted small">แสดงข้อมูลครบสำหรับการตรวจสอบน้ำมัน ระยะทาง และต้นทุน</div>
        </div>
        <div class="text-muted small">ทั้งหมด {{ number_format($jobs->total()) }} รายการ</div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover table-bordered align-middle text-nowrap">
            <thead>
                <tr>
                    <th>วันที่ขนส่ง</th>
                    <th>เลขที่เอกสาร</th>
                    <th>ทะเบียน</th>
                    <th>พนักงานขับ</th>
                    <th>ฟาร์ม</th>
                    <th>คู่สัญญา</th>
                    <th class="text-end">จำนวนอาหาร (กก.)</th>
                    <th class="text-end">ไมล์ต้น</th>
                    <th class="text-end">ไมล์ปลาย</th>
                    <th class="text-end">ระยะทางจริง</th>
                    <th class="text-end">ระยะทางมาตรฐาน</th>
                    <th class="text-end">น้ำมันบริษัท</th>
                    <th class="text-end">ชดเชยน้ำมัน</th>
                    <th>เหตุผลชดเชย</th>
                    <th>รายละเอียดชดเชย</th>
                    <th class="text-end">น้ำมันอนุมัติรวม</th>
                    <th class="text-end">น้ำมันเติมจริง</th>
                    <th class="text-end">ราคา/ลิตร</th>
                    <th class="text-end">ค่าน้ำมัน</th>
                    <th class="text-end">ส่วนต่างน้ำมัน</th>
                    <th class="text-end">ส่วนต่างเป็นเงิน</th>
                    <th class="text-end">ส่วนต่างระยะทาง</th>
                    <th class="text-end">อัตราเฉลี่ยน้ำมัน</th>
                    <th>หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>{{ $job->transport_date?->format('d/m/Y') }}</td>
                        <td>{{ $job->document_no }}</td>
                        <td>
                            <div class="fw-semibold">{{ $job->vehicle?->registration_number ?: '-' }}</div>
                            @if($job->vehicle?->brand || $job->vehicle?->model)
                                <div class="text-muted small">{{ trim(($job->vehicle?->brand ?? '') . ' ' . ($job->vehicle?->model ?? '')) }}</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $job->driver?->full_name ?: '-' }}</div>
                            @if($job->driver?->employee_code)
                                <div class="text-muted small">{{ $job->driver->employee_code }}</div>
                            @endif
                        </td>
                        <td>{{ $job->farm?->farm_name ?: '-' }}</td>
                        <td>{{ $job->vendor?->vendor_name ?: '-' }}</td>
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
                        <td colspan="24" class="text-center text-muted py-4">ไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
@endsection
