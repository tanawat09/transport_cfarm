@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end" id="vehicle-filter-form">
            <div class="col-md-4">
                <label class="form-label">ค้นหา</label>
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="ทะเบียนรถ, ประเภทรถ, ยี่ห้อ, รุ่น, รหัสคนขับ, ชื่อคนขับ">
            </div>
            <div class="col-md-4">
                <label class="form-label">ประเภทรถ</label>
                <select name="vehicle_type" class="form-select">
                    <option value="">ทุกประเภทรถ</option>
                    @foreach($vehicleTypes as $vehicleType)
                        <option value="{{ $vehicleType }}" @selected(request('vehicle_type') === $vehicleType)>{{ $vehicleType }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col text-end">
                <a href="{{ route('vehicles.create') }}" class="btn btn-success">เพิ่มข้อมูลรถ</a>
            </div>
        </form>
    </div>
</div>
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('vehicles.qr-print-bulk') }}" target="_blank" id="bulk-qr-form" class="d-flex flex-wrap align-items-end gap-3">
            <div>
                <label class="form-label">พิมพ์ QR หลายคัน</label>
                <select name="qr_type" class="form-select">
                    <option value="inspection">QR ตรวจรถ</option>
                    <option value="usage">QR ใช้รถ</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-dark">พิมพ์ QR รถที่เลือก</button>
            </div>
            <div class="text-muted small">
                เลือก checkbox ในตาราง แล้วกดพิมพ์ ระบบจะพิมพ์เฉพาะรถที่รองรับ QR ประเภทนั้น
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th style="width: 48px;">
                        <input type="checkbox" class="form-check-input" id="select-all-vehicles" aria-label="เลือกรถทั้งหมด">
                    </th>
                    <th>ทะเบียนรถ</th>
                    <th>วันที่จดทะเบียน</th>
                        <th>ประเภทรถ</th>
                        <th>ลากจูง</th>
                        <th>ยี่ห้อ</th>
                    <th>รุ่น</th>
                    <th>พนักงานขับประจำรถ</th>
                    <th class="text-end">ความจุ</th>
                    <th>สถานะ</th>
                    <th>QR ตรวจรถ / ใช้รถ</th>
                    <th class="text-end">จัดการ</th>
                </tr>
            </thead>
            <tbody>
            @forelse($vehicles as $vehicle)
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input vehicle-checkbox" name="vehicles[]" value="{{ $vehicle->id }}" form="bulk-qr-form" aria-label="เลือกรถ {{ $vehicle->registration_number }}">
                    </td>
                    <td>{{ $vehicle->registration_number }}</td>
                    <td>{{ $vehicle->registered_at ? $vehicle->registered_at->format('d/m/Y') : '-' }}</td>
                        <td>{{ $vehicle->vehicle_type ?: '-' }}</td>
                        <td>{{ $vehicle->towing_vehicle ?: '-' }}</td>
                        <td>{{ $vehicle->brand }}</td>
                    <td>{{ $vehicle->model ?: '-' }}</td>
                    <td>
                        @if($vehicle->primaryDriver)
                            <div>{{ $vehicle->primaryDriver->full_name }}</div>
                            <small class="text-muted">{{ $vehicle->primaryDriver->employee_code }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($vehicle->capacity_kg ?? 0, 2) }}</td>
                    <td>{{ $vehicle->status }}</td>
                    <td>
                        <div class="d-flex flex-wrap gap-2">
                            @if($vehicle->supportsPreTripInspectionQr())
                            <a href="{{ $vehicle->inspectionQrUrl() }}" class="btn btn-sm btn-outline-primary" target="_blank">เปิดฟอร์ม</a>
                            <a href="{{ route('vehicles.inspection-qr-page', $vehicle) }}" class="btn btn-sm btn-outline-secondary" target="_blank">ดู QR</a>
                            <a href="{{ route('vehicles.inspection-qr-print', $vehicle) }}" class="btn btn-sm btn-outline-dark" target="_blank">พิมพ์ QR</a>
                            @endif
                            @if($vehicle->supportsUsageLog())
                            <a href="{{ $vehicle->usageLogQrUrl() }}" class="btn btn-sm btn-outline-success" target="_blank">ฟอร์มใช้รถ</a>
                            <a href="{{ route('vehicles.usage-qr-page', $vehicle) }}" class="btn btn-sm btn-outline-success" target="_blank">QR ใช้รถ</a>
                            <a href="{{ route('vehicles.usage-qr-print', $vehicle) }}" class="btn btn-sm btn-outline-dark" target="_blank">พิมพ์ QR ใช้รถ</a>
                            @endif
                        </div>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                        <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบข้อมูลรถ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">ลบ</button>
                        </form>
                    </td>
                </tr>
            @empty
                    <tr><td colspan="12" class="text-center text-muted">ยังไม่มีข้อมูลรถ</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $vehicles->links() }}
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('vehicle-filter-form');
        const keywordInput = filterForm ? filterForm.querySelector('input[name="keyword"]') : null;
        const vehicleTypeSelect = filterForm ? filterForm.querySelector('select[name="vehicle_type"]') : null;
        const selectAll = document.getElementById('select-all-vehicles');
        const checkboxes = Array.from(document.querySelectorAll('.vehicle-checkbox'));
        const bulkForm = document.getElementById('bulk-qr-form');
        let keywordTimer = null;

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
            });
        }

        if (bulkForm) {
            bulkForm.addEventListener('submit', function (event) {
                if (!checkboxes.some((checkbox) => checkbox.checked)) {
                    event.preventDefault();
                    alert('กรุณาเลือกรถอย่างน้อย 1 คัน');
                }
            });
        }
    });
</script>
@endsection
