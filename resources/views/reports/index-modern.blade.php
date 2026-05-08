@extends('layouts.app')

@php
    $title = 'รายงานการขนส่ง';
    $subtitle = 'สรุปเที่ยวขนส่ง น้ำมัน ระยะทาง และต้นทุน';

    $summaryCards = [
        ['label' => 'จำนวนเที่ยวขนส่ง', 'value' => number_format($summary['total_jobs']), 'note' => 'เที่ยว', 'tone' => 'navy'],
        ['label' => 'น้ำหนักอาหารรวม', 'value' => number_format($summary['total_food_weight_kg'], 2), 'note' => 'กก.', 'tone' => 'teal'],
        ['label' => 'น้ำมันเติมจริงรวม', 'value' => number_format($summary['total_actual_oil_liters'], 2), 'note' => 'ลิตร', 'tone' => 'amber'],
        ['label' => 'น้ำมันอนุมัติรวม', 'value' => number_format($summary['total_approved_oil_liters'], 2), 'note' => 'ลิตร', 'tone' => 'sky'],
        ['label' => 'ต้นทุนน้ำมันรวม', 'value' => number_format($summary['total_oil_cost'], 2), 'note' => 'บาท', 'tone' => 'rose'],
        ['label' => 'ต้นทุนน้ำมันต่ออาหาร 1 กก.', 'value' => number_format($summary['oil_cost_per_kg'], 2), 'note' => 'บาท / กก.', 'tone' => 'violet'],
        ['label' => 'ส่วนต่างน้ำมันรวม', 'value' => number_format($summary['total_oil_difference_liters'], 2), 'note' => 'ลิตร', 'tone' => 'orange'],
        ['label' => 'ส่วนต่างระยะทางรวม', 'value' => number_format($summary['total_distance_difference_km'], 2), 'note' => 'กม.', 'tone' => 'blue'],
    ];

    $toneClasses = [
        'navy' => 'is-navy',
        'teal' => 'is-teal',
        'amber' => 'is-amber',
        'sky' => 'is-sky',
        'rose' => 'is-rose',
        'violet' => 'is-violet',
        'orange' => 'is-orange',
        'blue' => 'is-blue',
    ];
@endphp

@push('styles')
<style>
    .reports-shell {
        display: grid;
        gap: 18px;
    }

    .reports-panel {
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .reports-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        background: linear-gradient(180deg, rgba(248, 251, 252, 0.98), rgba(255, 255, 255, 0.92));
    }

    .reports-panel-title {
        margin: 0;
        color: #17324d;
        font-size: 1rem;
        font-weight: 800;
    }

    .reports-panel-subtitle {
        margin: 4px 0 0;
        color: #6b7c8c;
        font-size: 0.84rem;
    }

    .reports-panel-meta {
        color: #7b8896;
        font-size: 0.8rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .reports-panel-tools {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }

    .reports-panel-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .reports-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 8px 14px;
        border-radius: 12px;
        border: 1px solid rgba(23, 50, 77, 0.12);
        background: #fff;
        color: #17324d;
        text-decoration: none;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
        transition: 0.18s ease;
    }

    .reports-action-btn:hover {
        color: #17324d;
        background: #f5f9fc;
        transform: translateY(-1px);
    }

    .reports-action-btn.is-solid {
        background: #17324d;
        border-color: #17324d;
        color: #fff;
    }

    .reports-action-btn.is-solid:hover {
        color: #fff;
        background: #214364;
    }

    .reports-filter-body {
        padding: 18px 20px 20px;
    }

    .reports-filter-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 12px;
    }

    .reports-field {
        display: grid;
        gap: 8px;
        padding: 12px;
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.92);
    }

    .reports-field.col-span-2 {
        grid-column: span 2;
    }

    .reports-field label {
        margin: 0;
        color: #445566;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .reports-field .form-control,
    .reports-field .form-select {
        min-height: 42px;
        border-radius: 12px;
        border-color: rgba(148, 163, 184, 0.28);
        box-shadow: none;
    }

    .reports-field .form-control:focus,
    .reports-field .form-select:focus {
        border-color: rgba(31, 111, 120, 0.42);
        box-shadow: 0 0 0 0.2rem rgba(31, 111, 120, 0.12);
    }

    .reports-filter-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid rgba(148, 163, 184, 0.14);
    }

    .reports-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .reports-summary-card {
        position: relative;
        padding: 16px 18px;
        border-radius: 20px;
        border: 1px solid rgba(148, 163, 184, 0.14);
        background: #fff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .reports-summary-card::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: #17324d;
    }

    .reports-summary-card.is-navy::before { background: #17324d; }
    .reports-summary-card.is-teal::before { background: #1f6f78; }
    .reports-summary-card.is-amber::before { background: #d97706; }
    .reports-summary-card.is-sky::before { background: #0284c7; }
    .reports-summary-card.is-rose::before { background: #e11d48; }
    .reports-summary-card.is-violet::before { background: #7c3aed; }
    .reports-summary-card.is-orange::before { background: #ea580c; }
    .reports-summary-card.is-blue::before { background: #2563eb; }

    .reports-summary-label {
        color: #64748b;
        font-size: 0.84rem;
        font-weight: 700;
    }

    .reports-summary-value {
        margin-top: 10px;
        color: #0f172a;
        font-size: 1.7rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .reports-summary-note {
        margin-top: 8px;
        color: #8a96a3;
        font-size: 0.8rem;
    }

    .reports-table-wrap {
        padding: 0 20px 20px;
    }

    .reports-table {
        margin-bottom: 0;
    }

    .reports-table thead th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #f7fafc;
        color: #334155;
        font-size: 0.78rem;
        font-weight: 800;
        border-bottom-width: 1px;
    }

    .reports-table tbody td {
        vertical-align: top;
        font-size: 0.86rem;
    }

    .reports-table .text-end {
        font-variant-numeric: tabular-nums;
    }

    .reports-table-subtext {
        color: #8a96a3;
        font-size: 0.76rem;
    }

    .reports-empty {
        padding: 24px 16px;
        color: #94a3b8;
        text-align: center;
    }

    .reports-pagination {
        margin-top: 16px;
    }

    @media (max-width: 1399.98px) {
        .reports-summary-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 1199.98px) {
        .reports-field.col-span-2 {
            grid-column: span 3;
        }

        .reports-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .reports-panel-head {
            flex-direction: column;
        }

        .reports-panel-tools {
            width: 100%;
            align-items: stretch;
        }

        .reports-panel-actions {
            justify-content: flex-start;
        }

        .reports-panel-meta {
            white-space: normal;
        }

        .reports-field.col-span-2 {
            grid-column: span 4;
        }
    }

    @media (max-width: 767.98px) {
        .reports-filter-body,
        .reports-table-wrap {
            padding-left: 16px;
            padding-right: 16px;
        }

        .reports-filter-grid,
        .reports-summary-grid {
            grid-template-columns: 1fr;
        }

        .reports-field.col-span-2 {
            grid-column: auto;
        }

        .reports-filter-footer {
            justify-content: stretch;
            flex-direction: column;
        }

        .reports-filter-footer .btn,
        .reports-filter-footer .reports-action-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="reports-shell">
    <section class="reports-panel">
        <div class="reports-panel-head">
            <div>
                <h2 class="reports-panel-title">ตัวกรองรายงาน</h2>
                <p class="reports-panel-subtitle">เลือกช่วงวันที่ ทะเบียนรถ พนักงานขับ ฟาร์ม หรือคู่สัญญา แล้วดูผลสรุปได้ทันที</p>
            </div>
            <div class="reports-panel-tools">
                <div class="reports-panel-meta">ช่วงวันที่ {{ $filters['start_date'] ?? '-' }} ถึง {{ $filters['end_date'] ?? '-' }}</div>
                <div class="reports-panel-actions">
                    <a href="{{ route('reports.export.excel', request()->query()) }}" class="reports-action-btn is-solid">Export Excel</a>
                    <a href="{{ route('reports.export.pdf', request()->query()) }}" class="reports-action-btn">Export PDF</a>
                </div>
            </div>
        </div>
        <div class="reports-filter-body">
            <form method="GET">
                <div class="reports-filter-grid">
                    <div class="reports-field col-span-2">
                        <label for="start_date">วันที่เริ่มต้น</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="form-control">
                    </div>
                    <div class="reports-field col-span-2">
                        <label for="end_date">วันที่สิ้นสุด</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="form-control">
                    </div>
                    <div class="reports-field col-span-2">
                        <label for="report_vehicle_id">ทะเบียน</label>
                        <select name="vehicle_id" id="report_vehicle_id" class="form-select">
                            <option value="">ทั้งหมด</option>
                            @foreach($vehicles as $vehicle)
                                <option
                                    value="{{ $vehicle->id }}"
                                    data-primary-driver-id="{{ $vehicle->primary_driver_id }}"
                                    @selected(($filters['vehicle_id'] ?? null) == $vehicle->id)
                                >
                                    {{ $vehicle->registration_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="reports-field col-span-2">
                        <label for="report_driver_id">พนักงานขับ</label>
                        <select name="driver_id" id="report_driver_id" class="form-select">
                            <option value="">ทั้งหมด</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" @selected(($filters['driver_id'] ?? null) == $driver->id)>{{ $driver->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="reports-field col-span-2">
                        <label for="farm_id">ฟาร์ม</label>
                        <select name="farm_id" id="farm_id" class="form-select">
                            <option value="">ทั้งหมด</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}" @selected(($filters['farm_id'] ?? null) == $farm->id)>{{ $farm->farm_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="reports-field col-span-2">
                        <label for="vendor_id">คู่สัญญา</label>
                        <select name="vendor_id" id="vendor_id" class="form-select">
                            <option value="">ทั้งหมด</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" @selected(($filters['vendor_id'] ?? null) == $vendor->id)>{{ $vendor->vendor_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="reports-filter-footer">
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">ล้างตัวกรอง</a>
                    <button class="btn btn-primary">ค้นหารายงาน</button>
                </div>
            </form>
        </div>
    </section>

    <section class="reports-summary-grid">
        @foreach($summaryCards as $item)
            <article class="reports-summary-card {{ $toneClasses[$item['tone']] ?? 'is-navy' }}">
                <div class="reports-summary-label">{{ $item['label'] }}</div>
                <div class="reports-summary-value">{{ $item['value'] }}</div>
                <div class="reports-summary-note">{{ $item['note'] }}</div>
            </article>
        @endforeach
    </section>

    <section class="reports-panel">
        <div class="reports-panel-head">
            <div>
                <h2 class="reports-panel-title">รายละเอียดเที่ยวขนส่ง</h2>
                <p class="reports-panel-subtitle">แสดงข้อมูลสำหรับตรวจสอบระยะทาง น้ำมัน และต้นทุนของแต่ละเที่ยว</p>
            </div>
            <div class="reports-panel-meta">ทั้งหมด {{ number_format($jobs->total()) }} รายการ</div>
        </div>
        <div class="reports-table-wrap table-responsive">
            <table class="table table-hover table-bordered align-middle text-nowrap reports-table">
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
                                    <div class="reports-table-subtext">{{ trim(($job->vehicle?->brand ?? '') . ' ' . ($job->vehicle?->model ?? '')) }}</div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $job->driver?->full_name ?: '-' }}</div>
                                @if($job->driver?->employee_code)
                                    <div class="reports-table-subtext">{{ $job->driver->employee_code }}</div>
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
                            <td colspan="24" class="reports-empty">ไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="reports-pagination">
                {{ $jobs->links() }}
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const vehicleField = document.getElementById('report_vehicle_id');
    const driverField = document.getElementById('report_driver_id');

    if (!vehicleField || !driverField) {
        return;
    }

    const initialDriverValue = driverField.value;

    const syncDriverFromVehicle = (force = false) => {
        const selectedVehicle = vehicleField.options[vehicleField.selectedIndex];
        const driverId = selectedVehicle?.dataset?.primaryDriverId || '';

        if (!vehicleField.value) {
            if (force) {
                driverField.value = '';
            }
            return;
        }

        if (driverId && (force || !driverField.value)) {
            driverField.value = driverId;
            return;
        }

        if (force && !driverId) {
            driverField.value = '';
        }
    };

    syncDriverFromVehicle(!initialDriverValue);
    vehicleField.addEventListener('change', () => syncDriverFromVehicle(true));
});
</script>
@endpush
