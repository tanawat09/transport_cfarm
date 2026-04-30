@extends('layouts.app')

@php
    $title = 'การจัดการยาง';
    $subtitle = 'บันทึกรหัสยาง ตำแหน่งติดตั้ง ระยะเปลี่ยนยางมาตรฐาน และตรวจสอบประวัติการใช้งานของรถแต่ละคัน';
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('tire-registrations.index') }}" id="tire-filter-form" class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label for="vehicle_type_filter" class="form-label">ประเภทรถ</label>
                        <select name="vehicle_type" id="vehicle_type_filter" class="form-select" data-auto-submit>
                            <option value="">ทั้งหมด</option>
                            @foreach($vehicleTypes as $vehicleType)
                                <option value="{{ $vehicleType }}" @selected($selectedVehicleType === $vehicleType)>{{ $vehicleType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label for="vehicle_id_filter" class="form-label">เลือกรถ</label>
                        <select name="vehicle_id" id="vehicle_id_filter" class="form-select" data-auto-submit>
                            <option value="">เลือกทะเบียนรถ</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" data-vehicle-type="{{ $vehicle->vehicle_type }}" @selected(($selectedVehicle?->id ?? old('vehicle_id')) == $vehicle->id)>
                                    {{ $vehicle->registration_number }} - {{ $vehicle->vehicle_type ?: '-' }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('tire-registrations.report', ['vehicle_type' => $selectedVehicleType, 'vehicle_id' => $selectedVehicle?->id]) }}" class="btn btn-primary">รายงานยางใกล้เปลี่ยน</a>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('tire-registrations.index') }}" class="btn btn-outline-secondary">ล้าง</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                    <div>
                        <h5 class="mb-1">แผนผังตำแหน่งยาง</h5>
                        <div class="text-muted small">
                            @if($selectedVehicle)
                                {{ $selectedVehicle->registration_number }} - {{ $selectedVehicle->vehicle_type ?: '-' }} - {{ $selectedVehicle->brand }} {{ $selectedVehicle->model }}
                            @else
                                เลือกรถก่อน แล้วคลิกตำแหน่งยางจากแผนผัง
                            @endif
                        </div>
                    </div>
                    <div id="selected_tire_text" class="alert alert-info mb-0 py-2 px-3">
                        {{ old('tire_position') ? 'เลือกตำแหน่งยาง: ' . old('tire_position') : 'ยังไม่ได้เลือกตำแหน่งยาง' }}
                    </div>
                </div>

                <x-truck-tire-map :tire-statuses="$tireStatuses" :vehicle-type="$selectedVehicle?->vehicle_type ?? $selectedVehicleType" />
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="mb-3">บันทึกรหัสยาง</h5>
                <form method="POST" action="{{ route('tire-registrations.store') }}" class="row g-3">
                    @csrf
                    <input type="hidden" name="tire_position" id="tire_position" value="{{ old('tire_position') }}">

                    <div class="col-md-6">
                        <label for="tire_position_display" class="form-label">ตำแหน่งยาง</label>
                        <input type="text" id="tire_position_display" class="form-control fw-semibold" value="{{ old('tire_position') ?: 'ยังไม่ได้เลือกตำแหน่งยาง' }}" readonly>
                    </div>

                    <div class="col-12">
                        <label for="vehicle_id" class="form-label">ทะเบียนรถ</label>
                        <select name="vehicle_id" id="vehicle_id" class="form-select" required>
                            <option value="">เลือกทะเบียนรถ</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" data-vehicle-type="{{ $vehicle->vehicle_type }}" @selected(($selectedVehicle?->id ?? old('vehicle_id')) == $vehicle->id)>
                                    {{ $vehicle->registration_number }} - {{ $vehicle->vehicle_type ?: '-' }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($selectedVehicleCurrentMileage !== null)
                        <div class="col-12">
                            <div class="alert alert-light border mb-0">
                                ไมล์ปัจจุบันของรถคันนี้: <strong>{{ number_format($selectedVehicleCurrentMileage, 2) }}</strong> กม.
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <label for="tire_serial_number" class="form-label">รหัสยาง</label>
                        <input type="text" name="tire_serial_number" id="tire_serial_number" class="form-control" value="{{ old('tire_serial_number') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="condition_status" class="form-label">สถานะยาง</label>
                        <select name="condition_status" id="condition_status" class="form-select" required>
                            @foreach($conditionOptions as $value => $label)
                                @if($value !== 'empty')
                                    <option value="{{ $value }}" @selected(old('condition_status', 'normal') === $value)>{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="installed_at" class="form-label">วันที่ติดตั้ง</label>
                        <input type="date" name="installed_at" id="installed_at" class="form-control" value="{{ old('installed_at', now()->toDateString()) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="installed_mileage_km" class="form-label">ไมล์ที่ติดตั้ง</label>
                        <input type="number" step="0.01" min="0" name="installed_mileage_km" id="installed_mileage_km" class="form-control" value="{{ old('installed_mileage_km') }}">
                    </div>

                    <div class="col-md-6">
                        <label for="standard_replacement_distance_km" class="form-label">ระยะเปลี่ยนยางมาตรฐาน (กม.)</label>
                        <input type="number" step="0.01" min="0" name="standard_replacement_distance_km" id="standard_replacement_distance_km" class="form-control" value="{{ old('standard_replacement_distance_km') }}">
                    </div>

                    <div class="col-md-6">
                        <label for="brand" class="form-label">ยี่ห้อ</label>
                        <input type="text" name="brand" id="brand" class="form-control" value="{{ old('brand') }}">
                    </div>

                    <div class="col-md-6">
                        <label for="tire_size" class="form-label">ขนาดยาง</label>
                        <input type="text" name="tire_size" id="tire_size" class="form-control" value="{{ old('tire_size', '295/80 R22.5') }}">
                    </div>

                    <div class="col-md-6">
                        <label for="vendor_name" class="form-label">ผู้จำหน่าย/ร้านค้า</label>
                        <input type="text" name="vendor_name" id="vendor_name" class="form-control" value="{{ old('vendor_name') }}">
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">หมายเหตุ</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-success">บันทึกรหัสยาง</button>
                        <a href="{{ route('tire-registrations.index', $selectedVehicle ? ['vehicle_type' => $selectedVehicle->vehicle_type, 'vehicle_id' => $selectedVehicle->id] : []) }}" class="btn btn-outline-secondary">ล้างฟอร์ม</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body table-responsive">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="mb-0">ประวัติยาง</h5>
                    @if($selectedVehicle)
                        <span class="text-muted small">ทะเบียนรถ: {{ $selectedVehicle->registration_number }}</span>
                    @elseif($selectedVehicleType)
                        <span class="text-muted small">ประเภทรถ: {{ $selectedVehicleType }}</span>
                    @endif
                </div>

                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>รหัสยาง</th>
                            <th>ประเภทรถ</th>
                            <th>ทะเบียนรถ</th>
                            <th>ตำแหน่ง</th>
                            <th>วันที่ติดตั้ง</th>
                            <th class="text-end">ไมล์ที่ติดตั้ง</th>
                            <th class="text-end">ระยะเปลี่ยนยางมาตรฐาน</th>
                            <th>สถานะ</th>
                            <th>ผู้จำหน่าย</th>
                            <th>บันทึกโดย</th>
                            <th>หมายเหตุ</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $registration)
                            <tr>
                                <td class="fw-semibold">{{ $registration->tire_serial_number }}</td>
                                <td>{{ $registration->vehicle?->vehicle_type ?: '-' }}</td>
                                <td>{{ $registration->vehicle?->registration_number ?: '-' }}</td>
                                <td>{{ $registration->tire_position }}</td>
                                <td>{{ optional($registration->installed_at)->format('d/m/Y') ?: '-' }}</td>
                                <td class="text-end">{{ $registration->installed_mileage_km !== null ? number_format($registration->installed_mileage_km, 2) : '-' }}</td>
                                <td class="text-end">{{ $registration->standard_replacement_distance_km !== null ? number_format($registration->standard_replacement_distance_km, 2) : '-' }}</td>
                                <td>{{ $conditionOptions[$registration->condition_status] ?? $registration->condition_status }}</td>
                                <td>{{ $registration->vendor_name ?: '-' }}</td>
                                <td>{{ $registration->creator?->name ?: '-' }}</td>
                                <td>{{ $registration->notes ?: '-' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('tire-registrations.destroy', $registration) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบประวัติยางรายการนี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="vehicle_type" value="{{ $selectedVehicle?->vehicle_type ?? $selectedVehicleType }}">
                                        <input type="hidden" name="vehicle_id" value="{{ $selectedVehicle?->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">ลบ</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">{{ $selectedVehicle ? 'ยังไม่มีข้อมูลรหัสยางของรถคันนี้' : 'กรุณาเลือกทะเบียนรถก่อนเพื่อดูประวัติยาง' }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $registrations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('tire-filter-form');
        const tireDetails = @json($tireDetails);
        const tireMapRoot = document.querySelector('[data-truck-tire-map]');
        const tireInput = document.getElementById('tire_position');
        const tirePositionDisplay = document.getElementById('tire_position_display');
        const selectedText = document.getElementById('selected_tire_text');
        const vehicleFormField = document.getElementById('vehicle_id');
        const vehicleTypeFilterField = document.getElementById('vehicle_type_filter');
        const vehicleIdFilterField = document.getElementById('vehicle_id_filter');

        if (filterForm) {
            filterForm.querySelectorAll('[data-auto-submit]').forEach(function (field) {
                field.addEventListener('change', function () {
                    if (field.name === 'vehicle_type') {
                        const vehicleField = filterForm.querySelector('[name="vehicle_id"]');

                        if (vehicleField) {
                            vehicleField.value = '';
                        }
                    }

                    filterForm.submit();
                });
            });
        }

        if (vehicleFormField && filterForm && vehicleTypeFilterField && vehicleIdFilterField) {
            vehicleFormField.addEventListener('change', function () {
                const selectedOption = vehicleFormField.options[vehicleFormField.selectedIndex];
                const vehicleType = selectedOption?.dataset.vehicleType || '';

                vehicleTypeFilterField.value = vehicleType;
                vehicleIdFilterField.value = vehicleFormField.value;
                filterForm.submit();
            });
        }

        const fields = [
            'tire_serial_number',
            'condition_status',
            'installed_at',
            'installed_mileage_km',
            'standard_replacement_distance_km',
            'brand',
            'tire_size',
            'vendor_name',
            'notes',
        ];

        function setFieldValue(fieldName, value) {
            const field = document.getElementById(fieldName);

            if (! field) {
                return;
            }

            field.value = value ?? '';
        }

        function applySelectedTire(tireId) {
            if (! tireId) {
                return;
            }

            if (tireInput) {
                tireInput.value = tireId;
            }

            if (tirePositionDisplay) {
                tirePositionDisplay.value = tireId;
            }

            if (selectedText) {
                selectedText.textContent = `เลือกตำแหน่งยาง: ${tireId}`;
            }
        }

        if (tireMapRoot) {
            const tires = Array.from(tireMapRoot.querySelectorAll('.tire-position'));

            function selectTireElement(tire) {
                const tireId = tire.dataset.tireId;

                tires.forEach(function (item) {
                    item.classList.remove('is-selected');
                });

                tire.classList.add('is-selected');
                applySelectedTire(tireId);

                document.dispatchEvent(new CustomEvent('tire-position-selected', {
                    detail: { tireId },
                }));
            }

            tires.forEach(function (tire) {
                tire.addEventListener('click', function () {
                    selectTireElement(tire);
                });

                tire.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        selectTireElement(tire);
                    }
                });
            });

            if (tireInput && tireInput.value) {
                const selectedTire = tireMapRoot.querySelector(`.tire-position[data-tire-id="${tireInput.value}"]`);

                if (selectedTire) {
                    selectedTire.classList.add('is-selected');
                    applySelectedTire(tireInput.value);
                }
            }
        }

        document.addEventListener('tire-position-selected', function (event) {
            const tireId = event.detail?.tireId;
            const detail = tireDetails[tireId] || {};

            fields.forEach(function (fieldName) {
                setFieldValue(fieldName, detail[fieldName]);
            });

            if (! detail.tire_size) {
                setFieldValue('tire_size', '295/80 R22.5');
            }

            if (! detail.condition_status) {
                setFieldValue('condition_status', 'normal');
            }

            if (! detail.installed_at) {
                setFieldValue('installed_at', '{{ now()->toDateString() }}');
            }
        });
    });
</script>
@endpush
