@extends('layouts.app')

@php
    $title = 'รายงานการขนส่ง';
    $subtitle = 'สรุปเที่ยวขนส่ง น้ำมัน ระยะทาง และต้นทุน พร้อมค้นหาย้อนหลังและส่งออกเอกสารได้ในหน้าเดียว';

    $summaryCards = [
        ['label' => 'จำนวนเที่ยวขนส่ง', 'value' => number_format($summary['total_jobs']), 'note' => 'เที่ยวขนส่งทั้งหมด', 'tone' => 'navy'],
        ['label' => 'น้ำหนักอาหารรวม', 'value' => number_format($summary['total_food_weight_kg'], 2), 'note' => 'กิโลกรัม', 'tone' => 'teal'],
        ['label' => 'น้ำมันเติมจริงรวม', 'value' => number_format($summary['total_actual_oil_liters'], 2), 'note' => 'ลิตร', 'tone' => 'amber'],
        ['label' => 'น้ำมันอนุมัติรวม', 'value' => number_format($summary['total_approved_oil_liters'], 2), 'note' => 'ลิตร', 'tone' => 'sky'],
        ['label' => 'ต้นทุนน้ำมันรวม', 'value' => number_format($summary['total_oil_cost'], 2), 'note' => 'บาท', 'tone' => 'rose'],
        ['label' => 'ต้นทุนน้ำมันต่ออาหาร 1 กก.', 'value' => number_format($summary['oil_cost_per_kg'], 2), 'note' => 'บาท / กก.', 'tone' => 'violet'],
        ['label' => 'ส่วนต่างน้ำมันรวม', 'value' => number_format($summary['total_oil_difference_liters'], 2), 'note' => 'ลิตร', 'tone' => 'orange'],
        ['label' => 'ส่วนต่างระยะทางรวม', 'value' => number_format($summary['total_distance_difference_km'], 2), 'note' => 'กิโลเมตร', 'tone' => 'blue'],
    ];

    $hasActiveFilters = collect([
        $filters['vehicle_id'] ?? null,
        $filters['driver_id'] ?? null,
        $filters['farm_id'] ?? null,
        $filters['vendor_id'] ?? null,
    ])->filter(fn ($value) => filled($value))->isNotEmpty();
@endphp

@push('styles')
<style>
    .reports-shell {
        display: grid;
        gap: 22px;
    }

    .reports-hero {
        padding: 26px 28px;
        border-radius: 28px;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.16), transparent 22%),
            radial-gradient(circle at left bottom, rgba(255,255,255,.08), transparent 18%),
            linear-gradient(135deg, #17324d 0%, #1e5765 56%, #2f8a70 100%);
        box-shadow: 0 26px 52px rgba(23, 50, 77, 0.2);
    }

    .reports-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 18px;
        align-items: end;
    }

    .reports-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        font-size: .82rem;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .reports-hero-title {
        margin: 0;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.08;
    }

    .reports-hero-subtitle {
        margin: 10px 0 0;
        max-width: 760px;
        color: rgba(255,255,255,.84);
        font-size: .98rem;
        line-height: 1.65;
    }

    .reports-hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .reports-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 10px 16px;
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,.18);
        background: rgba(255,255,255,.1);
        color: #fff;
        text-decoration: none;
        font-weight: 700;
        transition: .18s ease;
    }

    .reports-action-btn:hover {
        color: #fff;
        background: rgba(255,255,255,.18);
        transform: translateY(-1px);
    }

    .reports-action-btn.is-solid {
        background: rgba(255,255,255,.2);
    }

    .reports-section {
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .reports-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 18px 22px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        background: linear-gradient(180deg, rgba(248,251,252,.96), rgba(255,255,255,.92));
    }

    .reports-section-title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: #17324d;
    }

    .reports-section-subtitle {
        margin: 4px 0 0;
        color: #6a7b8b;
        font-size: .9rem;
    }

    .reports-section-meta {
        color: #7b8896;
        font-size: .84rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .reports-filter-body {
        padding: 22px;
    }

    .reports-filter-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 16px;
    }

    .reports-filter-topline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        padding: 14px 16px;
        border: 1px solid rgba(148,163,184,.16);
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(247,250,252,.96), rgba(241,247,248,.92));
    }

    .reports-filter-topline-title {
        font-size: .92rem;
        font-weight: 800;
        color: #17324d;
    }

    .reports-filter-topline-text {
        margin-top: 4px;
        color: #6f7f90;
        font-size: .84rem;
    }

    .reports-filter-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(31,111,120,.1);
        color: #184d57;
        font-size: .8rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .reports-field {
        display: grid;
        gap: 8px;
        padding: 14px;
        border: 1px solid rgba(148,163,184,.16);
        border-radius: 18px;
        background: rgba(255,255,255,.86);
        box-shadow: inset 0 1px 1px rgba(15,23,42,.02);
    }

    .reports-field.col-span-2 {
        grid-column: span 2;
    }

    .reports-field label {
        margin: 0;
        color: #445566;
        font-size: .88rem;
        font-weight: 700;
    }

    .reports-field .form-control,
    .reports-field .form-select {
        min-height: 46px;
        border-radius: 14px;
        border-color: rgba(148,163,184,.28);
        box-shadow: none;
    }

    .reports-field .form-control:focus,
    .reports-field .form-select:focus {
        border-color: rgba(31,111,120,.42);
        box-shadow: 0 0 0 .2rem rgba(31,111,120,.12);
    }

    .reports-driver-hint {
        min-height: 20px;
        color: #718294;
        font-size: .8rem;
    }

    .reports-filter-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px dashed rgba(148,163,184,.22);
    }

    .reports-filter-note {
        color: #6e7f90;
        font-size: .86rem;
        line-height: 1.6;
    }

    .reports-filter-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .reports-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .reports-metric {
        position: relative;
        padding: 18px;
        border: 1px solid rgba(148,163,184,.14);
        border-radius: 22px;
        background: rgba(255,255,255,.94);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .reports-metric::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--metric-color);
    }

    .reports-metric.tone-navy { --metric-color: #17324d; }
    .reports-metric.tone-teal { --metric-color: #1f6f78; }
    .reports-metric.tone-amber { --metric-color: #d7a23b; }
    .reports-metric.tone-sky { --metric-color: #2d9db0; }
    .reports-metric.tone-rose { --metric-color: #d66a73; }
    .reports-metric.tone-violet { --metric-color: #7c64c3; }
    .reports-metric.tone-orange { --metric-color: #de7e45; }
    .reports-metric.tone-blue { --metric-color: #4f79d7; }

    .reports-metric-label {
        color: #667788;
        font-size: .88rem;
        font-weight: 700;
        line-height: 1.45;
    }

    .reports-metric-value {
        margin-top: 12px;
        font-size: 1.85rem;
        font-weight: 800;
        line-height: 1.05;
        color: #162433;
    }

    .reports-metric-note {
        margin-top: 8px;
        color: #8b97a5;
        font-size: .82rem;
    }

    .reports-insight {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: center;
        padding: 18px 20px;
        border: 1px dashed rgba(148,163,184,.24);
        border-radius: 22px;
        background: linear-gradient(135deg, rgba(255,255,255,.92), rgba(243,249,250,.92));
    }

    .reports-insight-title {
        font-size: 1rem;
        font-weight: 800;
        color: #17324d;
    }

    .reports-insight-text {
        margin-top: 6px;
        color: #6a7b8b;
        font-size: .9rem;
        line-height: 1.65;
    }

    .reports-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 128px;
        padding: 10px 14px;
        border-radius: 999px;
        background: rgba(31,111,120,.1);
        color: #184d57;
        font-size: .82rem;
        font-weight: 800;
    }

    .reports-table-wrap {
        padding: 0 18px 18px;
    }

    .reports-table {
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .reports-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        padding: 12px 14px;
        border-bottom: 1px solid rgba(148,163,184,.16);
        background: #f6fafb;
        color: #4f6072;
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .reports-table tbody td {
        padding: 14px;
        border-top: 1px solid rgba(226,232,240,.74);
        vertical-align: top;
        background: rgba(255,255,255,.96);
    }

    .reports-table tbody tr:nth-child(even) td {
        background: rgba(248,251,252,.92);
    }

    .reports-table tbody tr:hover td {
        background: rgba(238,246,248,.96);
    }

    .reports-vehicle-cell,
    .reports-driver-cell {
        min-width: 150px;
    }

    .reports-vehicle-main,
    .reports-driver-main {
        font-weight: 800;
        color: #162433;
    }

    .reports-vehicle-sub,
    .reports-driver-sub {
        margin-top: 3px;
        color: #7a8895;
        font-size: .82rem;
    }

    .reports-number {
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .reports-text-wrap {
        min-width: 220px;
        white-space: normal;
        line-height: 1.55;
    }

    .reports-empty {
        padding: 40px 16px;
        text-align: center;
        color: #7d8895;
    }

    @media (max-width: 1399.98px) {
        .reports-filter-grid {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }

        .reports-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .reports-hero-grid,
        .reports-insight,
        .reports-filter-footer {
            display: grid;
            grid-template-columns: 1fr;
        }

        .reports-hero-actions {
            justify-content: flex-start;
        }

        .reports-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .reports-field.col-span-2 {
            grid-column: span 1;
        }
    }

    @media (max-width: 767.98px) {
        .reports-hero,
        .reports-filter-body,
        .reports-section-head,
        .reports-table-wrap {
            padding-left: 16px;
            padding-right: 16px;
        }

        .reports-filter-grid,
        .reports-metrics {
            grid-template-columns: 1fr;
        }

        .reports-filter-topline {
            flex-direction: column;
            align-items: stretch;
        }

        .reports-hero-title {
            font-size: 1.65rem;
        }
    }
</style>
@endpush

@section('content')
<div class="reports-shell">
    <section class="reports-hero">
        <div class="reports-hero-grid">
            <div>
                <div class="reports-kicker">CFARM Transport Report</div>
                <h1 class="reports-hero-title">ศูนย์รายงานงานขนส่งอาหารไก่</h1>
                <p class="reports-hero-subtitle">ตรวจสอบเที่ยวขนส่ง ระยะทาง ค่าน้ำมัน และต้นทุนย้อนหลังได้ในหน้าเดียว พร้อมส่งออกเป็น Excel หรือ PDF สำหรับใช้งานต่อทันที</p>
            </div>
            <div class="reports-hero-actions">
                <a href="{{ route('reports.export.excel', request()->query()) }}" class="reports-action-btn is-solid">Export Excel</a>
                <a href="{{ route('reports.export.pdf', request()->query()) }}" class="reports-action-btn">Export PDF</a>
            </div>
        </div>
    </section>

    <section class="reports-section">
        <div class="reports-section-head">
            <div>
                <h2 class="reports-section-title">ตัวกรองรายงาน</h2>
                <p class="reports-section-subtitle">เลือกช่วงวันที่ ทะเบียนรถ พนักงานขับ ฟาร์ม และคู่สัญญา ก่อนดูรายงานหรือส่งออกเอกสาร</p>
            </div>
            <div class="reports-section-meta">ช่วงวันที่ {{ $filters['start_date'] ?? '-' }} ถึง {{ $filters['end_date'] ?? '-' }}</div>
        </div>
        <div class="reports-filter-body">
            <form method="GET">
                <div class="reports-filter-topline">
                    <div>
                        <div class="reports-filter-topline-title">เลือกเงื่อนไขให้เหมาะกับรายงานที่ต้องการ</div>
                        <div class="reports-filter-topline-text">เริ่มจากช่วงวันที่ แล้วค่อยกรองตามทะเบียนรถ คนขับ ฟาร์ม หรือคู่สัญญา เพื่อให้ผลลัพธ์อ่านง่ายและตรงงานมากขึ้น</div>
                    </div>
                    <div class="reports-filter-status">
                        {{ $hasActiveFilters ? 'กำลังกรองข้อมูลเฉพาะรายการที่เลือก' : 'แสดงข้อมูลภาพรวมทั้งหมด' }}
                    </div>
                </div>
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
                                    data-primary-driver-name="{{ $vehicle->primaryDriver?->full_name ?? '' }}"
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
                        {{-- <div id="report_driver_hint" class="reports-driver-hint">เลือกทะเบียนรถเพื่อแสดงพนักงานขับประจำรถ</div> --}}
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
                    <div class="reports-filter-note">
                        {{ $hasActiveFilters
                            ? 'กำลังแสดงผลตามเงื่อนไขที่เลือกไว้ สามารถเปลี่ยนทะเบียนรถเพื่อดูพนักงานขับประจำรถได้ทันที'
                            : 'สามารถกรองเฉพาะทะเบียนรถลากจูงเพื่อดูผลการขนส่งของแต่ละคันได้โดยตรง' }}
                    </div>
                    <div class="reports-filter-actions">
                        <button class="btn btn-primary px-4">ค้นหารายงาน</button>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary px-4">ล้างตัวกรอง</a>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <section class="reports-metrics">
        @foreach($summaryCards as $item)
            <article class="reports-metric tone-{{ $item['tone'] }}">
                <div class="reports-metric-label">{{ $item['label'] }}</div>
                <div class="reports-metric-value">{{ $item['value'] }}</div>
                <div class="reports-metric-note">{{ $item['note'] }}</div>
            </article>
        @endforeach
    </section>

    <section class="reports-insight">
        <div>
            <div class="reports-insight-title">คำอธิบายสรุป</div>
            <div class="reports-insight-text">
                ประสิทธิภาพการขนส่งและต้นทุนต่าง ๆ คำนวณจากข้อมูลเที่ยวขนส่งจริงที่บันทึกในระบบ ช่วยให้ตรวจสอบความคลาดเคลื่อนของน้ำมันและระยะทางได้เร็วขึ้น
            </div>
        </div>
        <div class="reports-pill">ทั้งหมด {{ number_format($jobs->total()) }} รายการ</div>
    </section>

    <section class="reports-section">
        <div class="reports-section-head">
            <div>
                <h2 class="reports-section-title">รายละเอียดเที่ยวขนส่ง</h2>
                <p class="reports-section-subtitle">แสดงข้อมูลครบสำหรับตรวจสอบน้ำมัน ระยะทาง และต้นทุนของแต่ละเที่ยว</p>
            </div>
            <div class="reports-section-meta">พร้อมใช้งานสำหรับตรวจย้อนหลังและส่งออก</div>
        </div>
        <div class="reports-table-wrap table-responsive">
            <table class="table reports-table align-middle text-nowrap">
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
                            <td class="reports-vehicle-cell">
                                <div class="reports-vehicle-main">{{ $job->vehicle?->registration_number ?: '-' }}</div>
                                @if($job->vehicle?->brand || $job->vehicle?->model)
                                    <div class="reports-vehicle-sub">{{ trim(($job->vehicle?->brand ?? '') . ' ' . ($job->vehicle?->model ?? '')) }}</div>
                                @endif
                            </td>
                            <td class="reports-driver-cell">
                                <div class="reports-driver-main">{{ $job->driver?->full_name ?: '-' }}</div>
                                @if($job->driver?->employee_code)
                                    <div class="reports-driver-sub">{{ $job->driver->employee_code }}</div>
                                @endif
                            </td>
                            <td>{{ $job->farm?->farm_name ?: '-' }}</td>
                            <td>{{ $job->vendor?->vendor_name ?: '-' }}</td>
                            <td class="reports-number">{{ number_format((float) $job->food_weight_kg, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->odometer_start, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->odometer_end, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->actual_distance_km, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->standard_distance_km, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->company_oil_liters, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->oil_compensation_liters, 2) }}</td>
                            <td>{{ $job->oilCompensationReason?->reason_name ?: '-' }}</td>
                            <td class="reports-text-wrap">{{ $job->oil_compensation_details ?: '-' }}</td>
                            <td class="reports-number">{{ number_format((float) $job->approved_oil_liters, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->actual_oil_liters, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->oil_price_per_liter, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->total_oil_cost, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->oil_difference_liters, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->oil_difference_amount, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->distance_difference_km, 2) }}</td>
                            <td class="reports-number">{{ number_format((float) $job->average_fuel_rate_km_per_liter, 2) }}</td>
                            <td class="reports-text-wrap">{{ $job->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="24" class="reports-empty">ไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
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
    const driverHint = document.getElementById('report_driver_hint');

    if (!vehicleField || !driverField || !driverHint) {
        return;
    }

    const initialDriverValue = driverField.value;

    const syncDriverFromVehicle = (force = false) => {
        const selectedVehicle = vehicleField.options[vehicleField.selectedIndex];
        const driverId = selectedVehicle?.dataset?.primaryDriverId || '';
        const driverName = selectedVehicle?.dataset?.primaryDriverName || '';

        if (!vehicleField.value) {
            if (force) {
                driverField.value = '';
            }

            driverHint.textContent = 'เลือกทะเบียนรถเพื่อแสดงพนักงานขับประจำรถ';
            return;
        }

        if (driverName) {
            driverHint.textContent = `พนักงานขับประจำรถ: ${driverName}`;
        } else {
            driverHint.textContent = 'ทะเบียนรถคันนี้ยังไม่ได้กำหนดพนักงานขับประจำรถ';
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
