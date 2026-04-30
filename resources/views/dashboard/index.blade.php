@php
    $title = 'แดชบอร์ดภาพรวม';
    $subtitle = 'ติดตามสถานะรถ งานขนส่ง น้ำมัน เอกสาร และการใช้งานล่าสุดได้จากหน้าหลักเดียว';
@endphp

@extends('layouts.app')

@push('styles')
<style>
    .dashboard-hero {
        padding: 28px;
        border-radius: 24px;
        color: #fff;
        background: linear-gradient(135deg, #17324d 0%, #1f6f78 58%, #2f8a6b 100%);
        box-shadow: 0 20px 38px rgba(23, 50, 77, 0.18);
    }

    .dashboard-hero-note {
        color: rgba(255, 255, 255, 0.82);
    }

    .metric-label {
        color: #6b7b8c;
        font-size: .88rem;
        font-weight: 700;
    }

    .metric-value {
        font-size: 1.9rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .metric-note {
        color: #8a96a3;
        font-size: .82rem;
    }

    .mini-stat {
        padding: 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.14);
    }

    .section-title {
        font-size: 1.02rem;
        font-weight: 800;
    }

    .progress-track {
        width: 100%;
        height: 10px;
        border-radius: 999px;
        background: #edf2f7;
        overflow: hidden;
    }

    .progress-bar-soft {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #1f6f78, #2f8a6b);
    }

    .dashboard-list-item:last-child {
        border-bottom: 0 !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
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
        border-radius: 12px 12px 4px 4px;
        background: linear-gradient(180deg, #2f8a6b 0%, #1f6f78 100%);
    }

    .chart-caption {
        color: #64748b;
        font-size: .78rem;
        text-align: center;
        white-space: nowrap;
    }

    @media (max-width: 767.98px) {
        .chart-grid {
            overflow-x: auto;
            grid-template-columns: repeat(6, 88px);
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-hero mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-xl-7">
            <div class="small mb-2 dashboard-hero-note">อัปเดตล่าสุด {{ now()->format('d/m/Y H:i') }} น.</div>
            <h2 class="fw-bold mb-2">ศูนย์รายงานระบบขนส่งอาหารไก่</h2>
            <p class="mb-0 dashboard-hero-note">ดูภาพรวมการเดินรถ ค่าใช้น้ำมัน เอกสารใกล้หมดอายุ สถานะตรวจรถก่อนวิ่ง และประวัติการใช้งานรถในมุมที่อ่านง่ายขึ้น</p>
        </div>
        <div class="col-xl-5">
            <div class="row g-3">
                <div class="col-6">
                    <div class="mini-stat">
                        <div class="small dashboard-hero-note">เที่ยววันนี้</div>
                        <div class="display-6 fw-bold mb-0">{{ number_format($todayJobsCount) }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mini-stat">
                        <div class="small dashboard-hero-note">เที่ยวเดือนนี้</div>
                        <div class="display-6 fw-bold mb-0">{{ number_format($monthJobsCount) }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mini-stat">
                        <div class="small dashboard-hero-note">ตรวจรถวันนี้</div>
                        <div class="display-6 fw-bold mb-0">{{ number_format($inspectionTodayCount) }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mini-stat">
                        <div class="small dashboard-hero-note">เอกสารใกล้หมด</div>
                        <div class="display-6 fw-bold mb-0">{{ number_format($expiringDocumentCount) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'รถพร้อมใช้งาน', 'value' => number_format($vehicleCount), 'note' => 'คัน'],
        ['label' => 'ค่าน้ำมันเดือนนี้', 'value' => number_format($monthlyOilCost, 2), 'note' => 'บาท'],
        ['label' => 'ระยะทางเดือนนี้', 'value' => number_format($monthlyDistance, 2), 'note' => 'กม.'],
        ['label' => 'น้ำหนักอาหารเดือนนี้', 'value' => number_format($monthlyFoodWeight, 2), 'note' => 'กก.'],
        ['label' => 'เอกสารหมดอายุ', 'value' => number_format($expiredDocumentCount), 'note' => 'รายการ', 'class' => 'border-danger-subtle'],
        ['label' => 'เอกสารใกล้หมด', 'value' => number_format($expiringDocumentCount), 'note' => 'ภายใน 30 วัน', 'class' => 'border-warning-subtle'],
        ['label' => 'ตรวจรถไม่ผ่านเดือนนี้', 'value' => number_format($inspectionFailMonthCount), 'note' => 'ครั้ง'],
        ['label' => 'ยางต้องติดตาม', 'value' => number_format($tireWarningCount), 'note' => 'ตำแหน่ง'],
    ] as $metric)
        <div class="col-sm-6 col-xl-3">
            <div class="card metric-card h-100 {{ $metric['class'] ?? '' }}">
                <div class="card-body">
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
        <div class="card h-100">
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
        <div class="card h-100">
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
        <div class="card h-100">
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
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="section-title">เอกสารรถใกล้หมดอายุ</span>
                <a href="{{ route('vehicle-documents.index') }}" class="btn btn-sm btn-outline-secondary">ดูทั้งหมด</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm align-middle">
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
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="section-title">เที่ยวขนส่งล่าสุด</span>
                <a href="{{ route('transport-jobs.index') }}" class="btn btn-sm btn-primary">ดูทั้งหมด</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
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
        <div class="card mb-4">
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

        <div class="card">
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
@endsection
