@php
    $title = 'แดชบอร์ดภาพรวม';
    $subtitle = 'ติดตามสถานะรถ งานขนส่ง น้ำมัน เอกสาร และการใช้งานล่าสุดได้จากหน้าหลักเดียว';
@endphp

@extends('layouts.app')

@push('styles')
<style>
    .dashboard-shell {
        display: grid;
        gap: 18px;
    }

    .dashboard-hero {
        position: relative;
        overflow: hidden;
        padding: 28px;
        border: 1px solid #d7e8de;
        border-radius: 16px;
        color: #16324b;
        background:
            radial-gradient(circle at top right, rgba(55, 187, 110, 0.14), transparent 32%),
            linear-gradient(135deg, #ffffff 0%, #f5fbf7 100%);
        box-shadow: 0 18px 38px rgba(18, 38, 58, 0.08);
    }

    .dashboard-hero::after {
        content: "";
        position: absolute;
        right: -56px;
        bottom: -72px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(31, 157, 85, 0.16), rgba(31, 157, 85, 0));
        pointer-events: none;
    }

    .dashboard-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding: 7px 12px;
        border: 1px solid #d7e8de;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.86);
        color: #1b6b45;
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .dashboard-title {
        margin: 0 0 8px;
        font-size: 2rem;
        font-weight: 900;
        line-height: 1.08;
    }

    .dashboard-hero-note {
        color: #667789;
        font-size: .95rem;
    }

    .dashboard-hero-meta {
        margin-top: 18px;
        color: #6f8192;
        font-size: .82rem;
        font-weight: 600;
    }

    .dashboard-hero-meta strong {
        color: #1d334a;
    }

    .metric-label {
        color: #667789;
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .02em;
        text-transform: uppercase;
    }

    .metric-value {
        font-size: 1.95rem;
        font-weight: 900;
        line-height: 1.1;
    }

    .metric-note {
        color: #8392a2;
        font-size: .8rem;
    }

    .mini-stat {
        position: relative;
        height: 100%;
        padding: 18px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #dce9e1;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
    }

    .mini-stat::before {
        content: "";
        position: absolute;
        inset: 0 auto auto 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #1f9d55, #7cd992);
    }

    .mini-stat-label {
        color: #657788;
        font-size: .82rem;
        font-weight: 700;
    }

    .mini-stat-value {
        margin-top: 8px;
        font-size: 2.1rem;
        font-weight: 900;
        line-height: 1;
        color: #14324a;
    }

    .metric-card {
        position: relative;
        overflow: hidden;
        border-radius: 14px;
        box-shadow: 0 12px 28px rgba(18, 38, 58, 0.06);
    }

    .metric-card .card-body {
        padding: 1.1rem 1rem 1rem;
    }

    .metric-card.metric-card-compact .card-body {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding: .85rem .9rem .8rem;
    }

    .metric-card.metric-card-compact {
        height: 138px !important;
        min-height: 0;
    }

    .metric-card.metric-card-compact .metric-icon {
        width: 36px;
        height: 36px;
        margin-bottom: 10px;
        border-radius: 10px;
        font-size: .92rem;
    }

    .metric-card.metric-card-compact .metric-label {
        font-size: .74rem;
    }

    .metric-card.metric-card-compact .metric-value {
        font-size: 1.62rem;
    }

    .metric-card.metric-card-compact .metric-note {
        margin-top: .35rem !important;
        font-size: .74rem;
    }

    .metric-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #1f9d55, #8dde9d);
    }

    .metric-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        border-radius: 12px;
        background: #ebf8ef;
        color: #1b7e49;
        font-size: 1.05rem;
    }

    .metric-card.is-alert .metric-icon {
        background: #fff1f1;
        color: #d44f4f;
    }

    .metric-card.is-warning .metric-icon {
        background: #fff7e7;
        color: #c78a17;
    }

    .section-title {
        font-size: 1.02rem;
        font-weight: 900;
    }

    .progress-track {
        width: 100%;
        height: 10px;
        border-radius: 8px;
        background: #edf3f0;
        overflow: hidden;
    }

    .progress-bar-soft {
        height: 100%;
        border-radius: 8px;
        background: linear-gradient(90deg, #1f9d55, #86daa2);
    }

    .dashboard-list-item:last-child {
        border-bottom: 0 !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }

    .dashboard-list-item {
        padding: 14px 0;
    }

    .chart-grid {
        display: grid;
        grid-template-columns: repeat(6, minmax(60px, 1fr));
        gap: 14px;
        align-items: end;
        min-height: 240px;
    }

    .chart-col {
        display: grid;
        gap: 10px;
        align-items: end;
    }

    .chart-bar {
        min-height: 12px;
        border-radius: 8px 8px 3px 3px;
        background: linear-gradient(180deg, #7fd89b 0%, #1f9d55 100%);
        box-shadow: 0 10px 18px rgba(31, 157, 85, 0.18);
    }

    .chart-caption {
        color: #64748b;
        font-size: .78rem;
        text-align: center;
        white-space: nowrap;
    }

    .dashboard-panel {
        border: 1px solid #dce7df;
        border-radius: 14px;
        box-shadow: 0 12px 28px rgba(18, 38, 58, 0.06);
    }

    .dashboard-panel .card-header {
        padding: 1rem 1rem .9rem;
        background: linear-gradient(180deg, #fbfefc 0%, #f4faf6 100%);
    }

    .dashboard-panel .card-body {
        padding: 1rem;
    }

    .dashboard-table thead th {
        background: #f6faf7;
        color: #5d7082;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .02em;
    }

    @media (max-width: 991.98px) {
        .dashboard-title {
            font-size: 1.72rem;
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-hero {
            padding: 20px;
        }

        .dashboard-title {
            font-size: 1.46rem;
        }

        .mini-stat-value {
            font-size: 1.82rem;
        }

        .metric-value {
            font-size: 1.72rem;
        }

        .metric-card.metric-card-compact {
            height: 132px !important;
            min-height: 0;
        }

        .chart-grid {
            overflow-x: auto;
            grid-template-columns: repeat(6, 88px);
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-shell">
<div class="dashboard-hero mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-xl-7">
            <div class="dashboard-badge">
                <i class="bi bi-activity"></i>
                <span>อัปเดตล่าสุด {{ now()->format('d/m/Y H:i') }} น.</span>
            </div>
            <h2 class="dashboard-title">ศูนย์รายงานระบบขนส่งอาหารไก่</h2>
            <p class="mb-0 dashboard-hero-note">ดูภาพรวมการเดินรถ ค่าใช้น้ำมัน เอกสารใกล้หมดอายุ สถานะตรวจรถก่อนวิ่ง และประวัติการใช้งานรถในมุมที่อ่านง่ายขึ้น</p>
            <div class="dashboard-hero-meta">
                ภาพรวมสำหรับวันนี้: <strong>งานขนส่ง</strong>, <strong>เอกสารรถ</strong>, <strong>การตรวจสภาพ</strong> และ <strong>การใช้งานรถ</strong>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="row g-3">
                <div class="col-6">
                    <div class="mini-stat">
                        <div class="mini-stat-label">เที่ยววันนี้</div>
                        <div class="mini-stat-value">{{ number_format($todayJobsCount) }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mini-stat">
                        <div class="mini-stat-label">เที่ยวเดือนนี้</div>
                        <div class="mini-stat-value">{{ number_format($monthJobsCount) }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mini-stat">
                        <div class="mini-stat-label">ตรวจรถวันนี้</div>
                        <div class="mini-stat-value">{{ number_format($inspectionTodayCount) }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mini-stat">
                        <div class="mini-stat-label">เอกสารใกล้หมด</div>
                        <div class="mini-stat-value">{{ number_format($expiringDocumentCount) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'รถพร้อมใช้งาน', 'value' => number_format($vehicleCount), 'note' => 'คัน', 'icon' => 'bi-truck-front', 'compact' => true],
        ['label' => 'ค่าน้ำมันเดือนนี้', 'value' => number_format($monthlyOilCost, 2), 'note' => 'บาท', 'icon' => 'bi-fuel-pump', 'compact' => true],
        ['label' => 'ระยะทางเดือนนี้', 'value' => number_format($monthlyDistance, 2), 'note' => 'กม.', 'icon' => 'bi-signpost-2', 'compact' => true],
        ['label' => 'น้ำหนักอาหารเดือนนี้', 'value' => number_format($monthlyFoodWeight, 2), 'note' => 'กก.', 'icon' => 'bi-box-seam', 'compact' => true],
    ] as $metric)
        <div class="col-sm-6 col-xl-3">
            <div class="card metric-card {{ empty($metric['compact']) ? 'h-100' : '' }} {{ !empty($metric['compact']) ? 'metric-card-compact' : '' }} {{ $metric['class'] ?? '' }}">
                <div class="card-body">
                    <div class="metric-icon"><i class="bi {{ $metric['icon'] }}"></i></div>
                    <div class="metric-label">{{ $metric['label'] }}</div>
                    <div class="metric-value mt-2">{{ $metric['value'] }}</div>
                    <div class="metric-note mt-2">{{ $metric['note'] }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card dashboard-panel h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="section-title">แนวโน้มค่าน้ำมันย้อนหลัง 6 เดือน</span>
                <span class="badge text-bg-light">บาท / เดือน</span>
            </div>
            <div class="card-body">
                <div class="chart-grid">
                    @foreach($monthlyJobStats as $stat)
                        @php $height = max(12, ($stat['oil_cost'] / $maxMonthlyOilCost) * 190); @endphp
                        <div class="chart-col">
                            <div class="text-center small fw-semibold">{{ number_format($stat['oil_cost'], 0) }}</div>
                            <div class="chart-bar" style="height: {{ $height }}px"></div>
                            <div class="chart-caption">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card dashboard-panel h-100">
            <div class="card-header">
                <span class="section-title">เที่ยวขนส่งย้อนหลัง 6 เดือน</span>
            </div>
            <div class="card-body">
                @foreach($monthlyJobStats as $stat)
                    @php $percent = max(5, ($stat['jobs_count'] / $maxMonthlyJobs) * 100); @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $stat['label'] }}</span>
                            <strong>{{ number_format($stat['jobs_count']) }} เที่ยว</strong>
                        </div>
                        <div class="progress-track">
                            <div class="progress-bar-soft" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="card dashboard-panel h-100">
            <div class="card-header">
                <span class="section-title">รถที่วิ่งงานมากที่สุดในเดือนนี้</span>
            </div>
            <div class="card-body">
                @forelse($topVehicleRows as $row)
                    @php $percent = max(5, ($row->jobs_count / $maxTopVehicleJobs) * 100); @endphp
                    <div class="dashboard-list-item border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">{{ $row->vehicle?->registration_number ?: '-' }}</span>
                            <span>{{ number_format($row->jobs_count) }} เที่ยว</span>
                        </div>
                        <div class="progress-track mb-2">
                            <div class="progress-bar-soft" style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="small text-muted">{{ number_format((float) $row->distance_km, 2) }} กม. | {{ number_format((float) $row->oil_cost, 2) }} บาท</div>
                    </div>
                @empty
                    <div class="text-muted">ยังไม่มีข้อมูลของเดือนนี้</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card dashboard-panel h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="section-title">เอกสารรถใกล้หมดอายุ</span>
                <a href="{{ route('vehicle-documents.index') }}" class="btn btn-sm btn-outline-secondary">ดูทั้งหมด</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm align-middle dashboard-table">
                    <thead>
                        <tr>
                            <th>ทะเบียน</th>
                            <th>เอกสาร</th>
                            <th class="text-end">วันหมดอายุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expiringDocuments as $document)
                            <tr>
                                <td class="fw-semibold">{{ $document->vehicle?->registration_number ?: '-' }}</td>
                                <td>{{ $document->typeLabel() }}</td>
                                <td class="text-end">
                                    <span class="badge {{ $document->statusBadgeClass() }}">{{ $document->expires_at?->format('d/m/Y') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">ยังไม่มีเอกสารใกล้หมดอายุ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card dashboard-panel h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="section-title">เที่ยวขนส่งล่าสุด</span>
                <a href="{{ route('transport-jobs.index') }}" class="btn btn-sm btn-primary">ดูทั้งหมด</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle dashboard-table">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>เลขที่เอกสาร</th>
                            <th>ทะเบียน</th>
                            <th>ฟาร์ม</th>
                            <th class="text-end">ค่าน้ำมัน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentJobs as $job)
                            <tr>
                                <td>{{ $job->transport_date?->format('d/m/Y') }}</td>
                                <td>{{ $job->document_no }}</td>
                                <td class="fw-semibold">{{ $job->vehicle?->registration_number ?: '-' }}</td>
                                <td>{{ $job->farm?->farm_name ?: '-' }}</td>
                                <td class="text-end">{{ number_format((float) $job->total_oil_cost, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">ยังไม่มีข้อมูลเที่ยวขนส่ง</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card dashboard-panel mb-4">
            <div class="card-header">
                <span class="section-title">ตรวจรถไม่ผ่านล่าสุด</span>
            </div>
            <div class="card-body">
                @forelse($failedInspections as $inspection)
                    <div class="dashboard-list-item border-bottom pb-3 mb-3">
                        <div class="fw-semibold">{{ $inspection->vehicle?->registration_number ?: '-' }}</div>
                        <div class="small text-muted mt-1">{{ $inspection->inspection_date?->format('d/m/Y') }} {{ $inspection->inspection_time }}</div>
                    </div>
                @empty
                    <div class="text-muted">ไม่มีรายการตรวจรถไม่ผ่าน</div>
                @endforelse
            </div>
        </div>

        <div class="card dashboard-panel">
            <div class="card-header">
                <span class="section-title">บันทึกการใช้รถล่าสุด</span>
            </div>
            <div class="card-body">
                @forelse($recentUsageLogs as $log)
                    <div class="dashboard-list-item border-bottom pb-3 mb-3">
                        <div class="fw-semibold">{{ $log->vehicle?->registration_number ?: '-' }} | {{ $log->driver_name ?: ($log->driver?->full_name ?: '-') }}</div>
                        <div class="small text-muted mt-1">{{ $log->usage_date?->format('d/m/Y') }} | {{ number_format((float) $log->distance_km, 2) }} กม.</div>
                    </div>
                @empty
                    <div class="text-muted">ยังไม่มีบันทึกการใช้รถ</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</div>
@endsection
