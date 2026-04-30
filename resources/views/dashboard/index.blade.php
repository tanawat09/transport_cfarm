@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">รถพร้อมใช้งาน</div>
            <div class="display-6 fw-semibold">{{ number_format($vehicleCount) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">พนักงานขับพร้อมงาน</div>
            <div class="display-6 fw-semibold">{{ number_format($driverCount) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">ฟาร์ม</div>
            <div class="display-6 fw-semibold">{{ number_format($farmCount) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">คู่สัญญาใช้งาน</div>
            <div class="display-6 fw-semibold">{{ number_format($vendorCount) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">เที่ยววันนี้</div>
            <div class="display-6 fw-semibold">{{ number_format($todayJobsCount) }}</div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">ค่าน้ำมันเดือนนี้</div>
            <div class="h4 fw-semibold mb-0">{{ number_format($monthlyOilCost, 2) }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">เที่ยวขนส่งล่าสุด</span>
        <a href="{{ route('transport-jobs.index') }}" class="btn btn-sm btn-primary">ดูทั้งหมด</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>เลขที่เอกสาร</th>
                        <th>รถ</th>
                        <th>พนักงานขับ</th>
                        <th>ฟาร์ม</th>
                        <th>คู่สัญญา</th>
                        <th class="text-end">ค่าน้ำมัน</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentJobs as $job)
                        <tr>
                            <td>{{ $job->transport_date?->format('d/m/Y') }}</td>
                            <td>{{ $job->document_no }}</td>
                            <td>{{ $job->vehicle?->registration_number }}</td>
                            <td>{{ $job->driver?->full_name }}</td>
                            <td>{{ $job->farm?->farm_name }}</td>
                            <td>{{ $job->vendor?->vendor_name }}</td>
                            <td class="text-end">{{ number_format($job->total_oil_cost, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">ยังไม่มีข้อมูลเที่ยวขนส่ง</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
