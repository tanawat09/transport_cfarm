@extends('layouts.app')

@section('content')
@php
    $dateCellClass = function ($date) {
        if (! $date) {
            return '';
        }

        $days = now()->startOfDay()->diffInDays($date->copy()->startOfDay(), false);

        if ($days < 0) {
            return 'table-danger';
        }

        if ($days <= 30) {
            return 'table-warning';
        }

        return 'table-success';
    };

    $formatDate = fn ($date) => $date ? $date->format('j-n-y') : '-';
@endphp

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">ประเภทรถ</label>
                <select name="vehicle_type" class="form-select">
                    <option value="">ทั้งหมด</option>
                    @foreach($vehicleTypes as $vehicleType)
                        <option value="{{ $vehicleType }}" @selected(request('vehicle_type') === $vehicleType)>{{ $vehicleType }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">สถานะ</label>
                <select name="status" class="form-select">
                    <option value="">ทั้งหมด</option>
                    <option value="expiring" @selected(request('status') === 'expiring')>ใกล้หมดอายุ</option>
                    <option value="expired" @selected(request('status') === 'expired')>หมดอายุแล้ว</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">ค้นหา</button>
                <a href="{{ route('vehicle-documents.index') }}" class="btn btn-outline-secondary">ล้าง</a>
            </div>
            <div class="col text-end">
                <a href="{{ route('vehicle-documents.create') }}" class="btn btn-success">เพิ่มเอกสารรถ</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover align-middle text-nowrap">
            <thead class="table-light">
                <tr class="text-center">
                    <th>ประเภทรถ</th>
                    <th>ทะเบียน</th>
                    <th>จดทะเบียน</th>
                    <th>ทุนประกัน</th>
                    <th>บริษัทประกันภัยรถ</th>
                    <th>เลขที่กรมธรรม์</th>
                    <th class="table-success">ภาษี</th>
                    <th class="table-warning">พรบ</th>
                    <th class="table-primary">ประกัน</th>
                    <th>เตือน</th>
                    <th class="text-end">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $document)
                    <tr>
                        <td>{{ $document->vehicle?->vehicle_type ?: '-' }}</td>
                        <td class="fw-semibold">{{ $document->vehicle?->registration_number ?: '-' }}</td>
                        <td class="text-center">{{ $formatDate($document->vehicle?->registered_at) }}</td>
                        <td class="text-end">{{ $document->insurance_capital !== null ? number_format((float) $document->insurance_capital, 2) : '-' }}</td>
                        <td>{{ $document->provider_name ?: '-' }}</td>
                        <td>{{ $document->document_no ?: '-' }}</td>
                        <td class="text-center {{ $dateCellClass($document->tax_expires_at) }}">{{ $formatDate($document->tax_expires_at) }}</td>
                        <td class="text-center {{ $dateCellClass($document->compulsory_expires_at) }}">{{ $formatDate($document->compulsory_expires_at) }}</td>
                        <td class="text-center {{ $dateCellClass($document->insurance_expires_at) }}">{{ $formatDate($document->insurance_expires_at) }}</td>
                        <td>
                            <span class="badge {{ $document->statusBadgeClass() }}">{{ $document->statusLabel() }}</span>
                            <div class="small text-muted">{{ $document->is_alert_enabled ? 'Telegram เปิด' : 'Telegram ปิด' }}</div>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('vehicle-documents.edit', $document) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                            <form method="POST" action="{{ route('vehicle-documents.destroy', $document) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบเอกสารรถ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">ลบ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">ยังไม่มีข้อมูลเอกสารรถ</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $documents->links() }}
    </div>
</div>
@endsection
