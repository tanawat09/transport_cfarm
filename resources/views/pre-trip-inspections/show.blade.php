@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">สรุปผลตรวจ</h5>
                        <div class="text-muted">{{ $inspection->inspection_date?->format('d/m/Y') }} {{ \Illuminate\Support\Str::of($inspection->inspection_time)->substr(0, 5) }}</div>
                    </div>
                    <span class="badge {{ $inspection->is_ready_to_drive ? 'text-bg-success' : 'text-bg-danger' }}">
                        {{ $inspection->is_ready_to_drive ? 'พร้อมวิ่ง' : 'ไม่พร้อมวิ่ง' }}
                    </span>
                </div>
                <dl class="row mb-0">
                    <dt class="col-sm-4">รถ</dt>
                    <dd class="col-sm-8">{{ $inspection->vehicle?->registration_number }}</dd>
                    <dt class="col-sm-4">พนักงานขับ</dt>
                    <dd class="col-sm-8">{{ $inspection->driver?->full_name ?? '-' }}</dd>
                    <dt class="col-sm-4">เลขไมล์</dt>
                    <dd class="col-sm-8">{{ $inspection->odometer_km !== null ? number_format((float) $inspection->odometer_km, 2) : '-' }}</dd>
                    <dt class="col-sm-4">ผู้ตรวจ</dt>
                    <dd class="col-sm-8">{{ $inspection->user?->name }}</dd>
                    <dt class="col-sm-4">หมายเหตุรวม</dt>
                    <dd class="col-sm-8">{{ $inspection->overall_note ?: '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">รายละเอียดการตรวจ</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>หัวข้อตรวจ</th>
                                <th>ผลตรวจ</th>
                                <th>หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($evaluationItems as $key => $label)
                                @php
                                    $statusField = $key . '_status';
                                    $noteField = $key . '_note';
                                    $status = $inspection->{$statusField};
                                @endphp
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td>
                                        <span class="badge {{ $status === \App\Models\PreTripInspection::STATUS_PASS ? 'text-bg-success' : 'text-bg-danger' }}">
                                            {{ $inspection->statusLabel($status) }}
                                        </span>
                                    </td>
                                    <td>{{ $inspection->{$noteField} ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('pre-trip-inspections.edit', $inspection) }}" class="btn btn-warning">แก้ไข</a>
    <a href="{{ route('pre-trip-inspections.index') }}" class="btn btn-outline-secondary">กลับ</a>
</div>
@endsection
