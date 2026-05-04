@extends('layouts.app')

@php
    $title = 'จัดการรถ';
    $subtitle = 'ค้นหา กรอง และจัดการข้อมูลทะเบียนรถ พร้อมพิมพ์ QR สำหรับตรวจรถและบันทึกการใช้รถ';
    $statusLabels = [
        'active' => 'ใช้งาน',
        'inactive' => 'ไม่ใช้งาน',
        'maintenance' => 'ซ่อมบำรุง',
    ];
    $statusClasses = [
        'active' => 'vehicle-status-active',
        'inactive' => 'vehicle-status-inactive',
        'maintenance' => 'vehicle-status-maintenance',
    ];
@endphp

@push('styles')
<style>
    .vehicle-filter-card,
    .vehicle-qr-card,
    .vehicle-list-card {
        overflow: hidden;
    }

    .vehicle-filter-title,
    .vehicle-section-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        color: #17212b;
    }

    .vehicle-section-subtitle {
        margin: .2rem 0 0;
        color: #6b7b8c;
        font-size: .88rem;
    }

    .vehicle-add-button {
        min-height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .vehicle-qr-card {
        border-color: rgba(31, 111, 120, .18);
        background: linear-gradient(135deg, rgba(31, 111, 120, .08), rgba(255, 255, 255, .96) 42%);
    }

    .selected-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 64px;
        min-height: 34px;
        padding: 6px 12px;
        border-radius: 999px;
        color: #184d57;
        background: rgba(31, 111, 120, .12);
        font-weight: 800;
    }

    .vehicle-list-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 1.15rem 1.25rem;
        border-bottom: 1px solid rgba(148, 163, 184, .16);
    }

    .vehicle-table-wrap {
        overflow-x: auto;
    }

    .vehicle-table {
        min-width: 1160px;
    }

    .vehicle-table tbody td {
        vertical-align: middle;
    }

    .vehicle-registration {
        color: #102638;
        font-size: 1rem;
        font-weight: 900;
    }

    .vehicle-muted {
        color: #6b7b8c;
        font-size: .82rem;
    }

    .vehicle-type-badge,
    .vehicle-status-badge {
        display: inline-flex;
        align-items: center;
        max-width: 260px;
        padding: 7px 11px;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 800;
        line-height: 1.2;
        white-space: normal;
    }

    .vehicle-type-badge {
        color: #184d57;
        background: rgba(31, 111, 120, .12);
    }

    .vehicle-status-badge {
        border: 1px solid transparent;
    }

    .vehicle-status-active {
        color: #0f5132;
        background: #d1e7dd;
        border-color: #badbcc;
    }

    .vehicle-status-inactive {
        color: #475569;
        background: #e2e8f0;
        border-color: #cbd5e1;
    }

    .vehicle-status-maintenance {
        color: #7a4b00;
        background: #fff3cd;
        border-color: #ffecb5;
    }

    .vehicle-driver-name {
        font-weight: 800;
        color: #253344;
    }

    .vehicle-action-cell {
        min-width: 220px;
    }

    .vehicle-actions,
    .vehicle-qr-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .vehicle-actions {
        justify-content: flex-end;
    }

    .vehicle-empty-state {
        padding: 46px 16px;
        text-align: center;
    }

    .vehicle-empty-state strong {
        display: block;
        margin-bottom: 6px;
        color: #253344;
        font-size: 1.05rem;
    }

    .vehicle-pagination {
        padding: 1rem 1.25rem 1.25rem;
    }

    @media (max-width: 767.98px) {
        .vehicle-list-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .vehicle-actions {
            justify-content: flex-start;
        }

        .vehicle-add-button,
        .vehicle-qr-card .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="card vehicle-filter-card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end" id="vehicle-filter-form">
            <div class="col-12 col-lg-3">
                <h2 class="vehicle-filter-title">ค้นหาข้อมูลรถ</h2>
                <p class="vehicle-section-subtitle">เลือกประเภทรถหรือพิมพ์คำค้น ระบบจะกรองให้อัตโนมัติ</p>
            </div>
            <div class="col-md-5 col-lg-4">
                <label class="form-label" for="vehicle-keyword">ค้นหา</label>
                <input
                    type="text"
                    id="vehicle-keyword"
                    name="keyword"
                    value="{{ request('keyword') }}"
                    class="form-control"
                    placeholder="ทะเบียนรถ, ประเภทรถ, ยี่ห้อ, รุ่น, รหัสหรือชื่อพนักงานขับ">
            </div>
            <div class="col-md-4 col-lg-3">
                <label class="form-label" for="vehicle-type-filter">ประเภทรถ</label>
                <select id="vehicle-type-filter" name="vehicle_type" class="form-select">
                    <option value="">ทุกประเภทรถ</option>
                    @foreach($vehicleTypes as $vehicleType)
                        <option value="{{ $vehicleType }}" @selected(request('vehicle_type') === $vehicleType)>{{ $vehicleType }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-lg-2 text-md-end">
                <a href="{{ route('vehicles.create') }}" class="btn btn-primary vehicle-add-button">เพิ่มข้อมูลรถ</a>
            </div>
        </form>
    </div>
</div>

<div class="card vehicle-qr-card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('vehicles.qr-print-bulk') }}" target="_blank" id="bulk-qr-form" class="row g-3 align-items-end">
            <div class="col-lg-4">
                <h2 class="vehicle-section-title">พิมพ์ QR หลายคัน</h2>
                <p class="vehicle-section-subtitle">ติ๊กเลือกรถจากตาราง แล้วเลือกประเภท QR ที่ต้องการพิมพ์</p>
            </div>
            <div class="col-md-4 col-lg-3">
                <label class="form-label" for="qr-type">ประเภท QR</label>
                <select id="qr-type" name="qr_type" class="form-select">
                    <option value="inspection">QR ตรวจรถ</option>
                    <option value="usage">QR ใช้รถ</option>
                </select>
            </div>
            <div class="col-md-4 col-lg-3">
                <label class="form-label d-block">จำนวนที่เลือก</label>
                <span class="selected-count" id="selected-vehicle-count">0 คัน</span>
            </div>
            <div class="col-md-4 col-lg-2 text-md-end">
                <button type="submit" class="btn btn-dark">พิมพ์ QR</button>
            </div>
        </form>
    </div>
</div>

<div class="card vehicle-list-card">
    <div class="vehicle-list-header">
        <div>
            <h2 class="vehicle-section-title">รายการรถ</h2>
            <p class="vehicle-section-subtitle">จัดการทะเบียนรถ ข้อมูลประจำรถ พนักงานขับ และ QR ที่เกี่ยวข้อง</p>
        </div>
        <span class="selected-count">{{ number_format($vehicles->total()) }} คัน</span>
    </div>

    <div class="vehicle-table-wrap">
        <table class="table table-hover align-middle vehicle-table">
            <thead>
                <tr>
                    <th style="width: 52px;">
                        <input type="checkbox" class="form-check-input" id="select-all-vehicles" aria-label="เลือกรถทั้งหมด">
                    </th>
                    <th>ทะเบียนรถ</th>
                    <th>ประเภทรถ</th>
                    <th>รายละเอียดรถ</th>
                    <th>พนักงานขับประจำรถ</th>
                    <th class="text-end">ความจุ</th>
                    <th>สถานะ</th>
                    <th>QR ตรวจรถ / ใช้รถ</th>
                    <th class="text-end">จัดการ</th>
                </tr>
            </thead>
            <tbody>
            @forelse($vehicles as $vehicle)
                @php
                    $status = $vehicle->status ?: 'active';
                    $statusLabel = $statusLabels[$status] ?? $status;
                    $statusClass = $statusClasses[$status] ?? 'vehicle-status-inactive';
                @endphp
                <tr>
                    <td>
                        <input
                            type="checkbox"
                            class="form-check-input vehicle-checkbox"
                            name="vehicles[]"
                            value="{{ $vehicle->id }}"
                            form="bulk-qr-form"
                            aria-label="เลือกรถ {{ $vehicle->registration_number }}">
                    </td>
                    <td>
                        <div class="vehicle-registration">{{ $vehicle->registration_number }}</div>
                        <div class="vehicle-muted">
                            {{ $vehicle->registered_at ? 'จดทะเบียน ' . $vehicle->registered_at->format('d/m/Y') : 'ยังไม่ระบุวันที่จดทะเบียน' }}
                        </div>
                    </td>
                    <td>
                        <span class="vehicle-type-badge">{{ $vehicle->vehicle_type ?: 'ไม่ระบุประเภท' }}</span>
                        @if($vehicle->towing_vehicle)
                            <div class="vehicle-muted mt-2">รถกึ่งพ่วง: <strong>{{ $vehicle->towing_vehicle }}</strong></div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold">{{ $vehicle->brand ?: '-' }}</div>
                        <div class="vehicle-muted">รุ่น: {{ $vehicle->model ?: '-' }}</div>
                    </td>
                    <td>
                        @if($vehicle->primaryDriver)
                            <div class="vehicle-driver-name">{{ $vehicle->primaryDriver->full_name }}</div>
                            <div class="vehicle-muted">{{ $vehicle->primaryDriver->employee_code }}</div>
                        @else
                            <span class="vehicle-muted">ยังไม่ได้กำหนด</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="fw-bold">{{ number_format($vehicle->capacity_kg ?? 0, 2) }}</div>
                        <div class="vehicle-muted">กก.</div>
                    </td>
                    <td>
                        <span class="vehicle-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>
                    <td class="vehicle-action-cell">
                        <div class="vehicle-qr-actions">
                            @if($vehicle->supportsPreTripInspectionQr())
                                <a href="{{ $vehicle->inspectionQrUrl() }}" class="btn btn-sm btn-outline-primary" target="_blank">ฟอร์มตรวจ</a>
                                <a href="{{ route('vehicles.inspection-qr-page', $vehicle) }}" class="btn btn-sm btn-outline-secondary" target="_blank">ดู QR</a>
                                <a href="{{ route('vehicles.inspection-qr-print', $vehicle) }}" class="btn btn-sm btn-outline-dark" target="_blank">พิมพ์ตรวจ</a>
                            @endif

                            @if($vehicle->supportsUsageLog())
                                <a href="{{ $vehicle->usageLogQrUrl() }}" class="btn btn-sm btn-outline-success" target="_blank">ฟอร์มใช้รถ</a>
                                <a href="{{ route('vehicles.usage-qr-page', $vehicle) }}" class="btn btn-sm btn-outline-success" target="_blank">QR ใช้รถ</a>
                                <a href="{{ route('vehicles.usage-qr-print', $vehicle) }}" class="btn btn-sm btn-outline-dark" target="_blank">พิมพ์ใช้รถ</a>
                            @endif

                            @if(! $vehicle->supportsPreTripInspectionQr() && ! $vehicle->supportsUsageLog())
                                <span class="vehicle-muted">ไม่รองรับ QR</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-end vehicle-action-cell">
                        <div class="vehicle-actions">
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                            <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" onsubmit="return confirm('ยืนยันการลบข้อมูลรถ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">ลบ</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        <div class="vehicle-empty-state">
                            <strong>ยังไม่มีข้อมูลรถ</strong>
                            <span class="vehicle-muted">เพิ่มข้อมูลรถคันแรก หรือปรับเงื่อนไขการค้นหาอีกครั้ง</span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($vehicles->hasPages())
        <div class="vehicle-pagination">
            {{ $vehicles->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('vehicle-filter-form');
        const keywordInput = filterForm ? filterForm.querySelector('input[name="keyword"]') : null;
        const vehicleTypeSelect = filterForm ? filterForm.querySelector('select[name="vehicle_type"]') : null;
        const selectAll = document.getElementById('select-all-vehicles');
        const checkboxes = Array.from(document.querySelectorAll('.vehicle-checkbox'));
        const bulkForm = document.getElementById('bulk-qr-form');
        const selectedCount = document.getElementById('selected-vehicle-count');
        let keywordTimer = null;

        function updateSelectedCount() {
            const count = checkboxes.filter((checkbox) => checkbox.checked).length;

            if (selectedCount) {
                selectedCount.textContent = `${count} คัน`;
            }

            if (selectAll) {
                selectAll.checked = count > 0 && count === checkboxes.length;
                selectAll.indeterminate = count > 0 && count < checkboxes.length;
            }
        }

        if (keywordInput && filterForm) {
            keywordInput.addEventListener('input', function () {
                clearTimeout(keywordTimer);
                keywordTimer = setTimeout(function () {
                    filterForm.submit();
                }, 600);
            });
        }

        if (vehicleTypeSelect && filterForm) {
            vehicleTypeSelect.addEventListener('change', function () {
                filterForm.submit();
            });
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach((checkbox) => checkbox.checked = selectAll.checked);
                updateSelectedCount();
            });
        }

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        if (bulkForm) {
            bulkForm.addEventListener('submit', function (event) {
                if (!checkboxes.some((checkbox) => checkbox.checked)) {
                    event.preventDefault();
                    alert('กรุณาเลือกรถอย่างน้อย 1 คัน');
                }
            });
        }

        updateSelectedCount();
    });
</script>
@endpush
