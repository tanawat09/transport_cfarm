@extends('layouts.app')

@php
    $title = 'รายละเอียดตรวจเช็กรถไถ';
    $subtitle = 'สรุปผลการตรวจของรถไถคันนี้ พร้อมหัวข้อที่ผ่านและต้องติดตามเพิ่มเติม';
@endphp

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
                    <span class="badge {{ $inspection->is_ready_for_use ? 'text-bg-success' : 'text-bg-danger' }}">
                        {{ $inspection->is_ready_for_use ? 'พร้อมใช้งาน' : 'ต้องตรวจซ้ำ' }}
                    </span>
                </div>
                <dl class="row mb-0">
                    <dt class="col-sm-4">รถไถ</dt>
                    <dd class="col-sm-8">{{ $inspection->vehicle?->registration_number }}{{ $inspection->vehicle?->brand ? ' - ' . $inspection->vehicle?->brand : '' }}</dd>
                    <dt class="col-sm-4">ฟาร์ม</dt>
                    <dd class="col-sm-8">{{ $inspection->farm?->farm_name ?? '-' }}</dd>
                    <dt class="col-sm-4">ชั่วโมงงาน</dt>
                    <dd class="col-sm-8">{{ number_format((float) $inspection->working_hours, 2) }}</dd>
                    <dt class="col-sm-4">ผู้บันทึก</dt>
                    <dd class="col-sm-8">{{ $inspection->user?->name ?? '-' }}</dd>
                    <dt class="col-sm-4">หมายเหตุรวม</dt>
                    <dd class="col-sm-8">{{ $inspection->overall_note ?: '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">รายการตรวจเช็ก</h5>
                <div class="d-grid gap-3">
                    @foreach($checklistGroups as $group)
                        <div class="border rounded-4 p-3">
                            <div class="fw-bold mb-2">{{ $group['label'] }}</div>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>หัวข้อย่อย</th>
                                            <th>ผลตรวจ</th>
                                            <th>หมายเหตุ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group['items'] as $item)
                                            <tr>
                                                <td>{{ $item['label'] }}</td>
                                                <td>
                                                    <span class="badge {{ ($item['status'] ?? null) === \App\Models\TractorUsageInspection::STATUS_PASS ? 'text-bg-success' : 'text-bg-danger' }}">
                                                        {{ $inspection->statusLabel($item['status'] ?? null) }}
                                                    </span>
                                                </td>
                                                <td>{{ $item['note'] ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex flex-wrap gap-2">
    <a href="{{ route('tractor-usage-inspections.edit', $inspection) }}" class="btn btn-warning">แก้ไข</a>
    <a href="{{ route('tractor-usage-inspections.index') }}" class="btn btn-outline-secondary">กลับ</a>
</div>
@endsection
