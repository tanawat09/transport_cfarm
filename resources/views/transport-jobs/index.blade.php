@extends('layouts.app')

@section('content')
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">คำค้น</label>
            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="เลขที่เอกสาร">
        </div>
        <div class="col-md-3">
            <label class="form-label">วันที่เริ่มต้น</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">วันที่สิ้นสุด</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">ค้นหา</button>
            <a href="{{ route('transport-jobs.index') }}" class="btn btn-outline-secondary">ล้าง</a>
        </div>
        <div class="col text-end"><a href="{{ route('transport-jobs.create') }}" class="btn btn-success">บันทึกเที่ยวขนส่ง</a></div>
    </form>
</div></div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>วันที่</th>
                <th>เลขที่เอกสาร</th>
                <th>รถ</th>
                <th>พนักงานขับ</th>
                <th>ฟาร์ม</th>
                <th>คู่สัญญา</th>
                <th class="text-end">ค่าน้ำมัน</th>
                <th class="text-end">ส่วนต่างน้ำมัน (ลิตร)</th>
                <th class="text-end">ส่วนต่างน้ำมัน (บาท)</th>
                <th class="text-end">จัดการ</th>
            </tr>
        </thead>
        <tbody>
        @forelse($jobs as $job)
            <tr>
                <td>{{ $job->transport_date?->format('d/m/Y') }}</td>
                <td>{{ $job->document_no }}</td>
                <td>{{ $job->vehicle?->registration_number }}</td>
                <td>{{ $job->driver?->full_name }}</td>
                <td>{{ $job->farm?->farm_name }}</td>
                <td>{{ $job->vendor?->vendor_name }}</td>
                <td class="text-end">{{ number_format($job->total_oil_cost, 2) }}</td>
                <td class="text-end">{{ number_format($job->oil_difference_liters, 2) }}</td>
                <td class="text-end">{{ number_format($job->oil_difference_amount, 2) }}</td>
                <td class="text-end">
                    <a href="{{ route('transport-jobs.show', $job) }}" class="btn btn-sm btn-info text-white">ดู</a>
                    <a href="{{ route('transport-jobs.edit', $job) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                    <form method="POST" action="{{ route('transport-jobs.destroy', $job) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบเที่ยวขนส่ง?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">ลบ</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="10" class="text-center text-muted">ยังไม่มีข้อมูลเที่ยวขนส่ง</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $jobs->links() }}
</div></div>
@endsection
