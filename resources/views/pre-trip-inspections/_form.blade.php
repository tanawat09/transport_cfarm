@push('styles')
<style>
    .inspection-choice-group {
        display: inline-flex;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .inspection-choice-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .inspection-choice-label {
        min-width: 88px;
        border: 1px solid var(--bs-border-color);
        border-radius: .5rem;
        padding: .55rem .9rem;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
        background: #fff;
        transition: all .15s ease-in-out;
    }

    .inspection-choice-input:checked + .inspection-choice-label.pass {
        background: var(--bs-success);
        border-color: var(--bs-success);
        color: #fff;
    }

    .inspection-choice-input:checked + .inspection-choice-label.fail {
        background: var(--bs-danger);
        border-color: var(--bs-danger);
        color: #fff;
    }
</style>
@endpush

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">วันที่ตรวจ</label>
        <input type="date" name="inspection_date" value="{{ old('inspection_date', optional($inspection->inspection_date)->format('Y-m-d')) }}" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">เวลาตรวจ</label>
        <input type="time" name="inspection_time" value="{{ old('inspection_time', $inspection->inspection_time) }}" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">รถ</label>
        @if(!empty($lockedVehicle))
            <input type="hidden" name="vehicle_id" id="inspection_vehicle_id" value="{{ old('vehicle_id', $lockedVehicle->id) }}">
            <input type="text" class="form-control" value="{{ $lockedVehicle->registration_number }} - {{ $lockedVehicle->brand }}{{ $lockedVehicle->model ? ' / ' . $lockedVehicle->model : '' }}" readonly>
        @else
            <select name="vehicle_id" id="inspection_vehicle_id" class="form-select" required>
                <option value="">เลือกรถ</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" data-primary-driver-id="{{ $vehicle->primary_driver_id }}" @selected(old('vehicle_id', $inspection->vehicle_id) == $vehicle->id)>
                        {{ $vehicle->registration_number }} - {{ $vehicle->brand }}{{ $vehicle->model ? ' / ' . $vehicle->model : '' }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>
    <div class="col-md-3">
        <label class="form-label">พนักงานขับ</label>
        <select name="driver_id" id="inspection_driver_id" class="form-select">
            <option value="">เลือกพนักงานขับ</option>
            @foreach($drivers as $driver)
                <option value="{{ $driver->id }}" @selected(old('driver_id', $inspection->driver_id) == $driver->id)>
                    {{ $driver->employee_code }} - {{ $driver->full_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">เลขไมล์ปัจจุบัน</label>
        <input type="number" step="0.01" min="0" name="odometer_km" value="{{ old('odometer_km', $inspection->odometer_km) }}" class="form-control">
    </div>
</div>

<div class="card border mt-2">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="card-title mb-1">รายการตรวจเช็กก่อนวิ่ง</h5>
                <div class="text-muted small">เลือกผลตรวจให้ครบทุกหัวข้อ และระบุหมายเหตุเมื่อพบความผิดปกติ</div>
            </div>
            <div id="inspection_summary_badge" class="badge text-bg-secondary">รอประเมิน</div>
        </div>

        <div class="d-grid gap-3">
            @foreach($evaluationItems as $key => $label)
                @php
                    $statusField = $key . '_status';
                    $noteField = $key . '_note';
                    $currentStatus = old($statusField, $inspection->{$statusField});
                @endphp
                <div class="border rounded-3 p-3">
                    <div class="row g-3 align-items-start">
                        <div class="col-lg-7">
                            <label class="form-label fw-semibold">{{ $label }}</label>
                        </div>
                        <div class="col-lg-2">
                            <div class="inspection-choice-group">
                                <input
                                    class="inspection-choice-input inspection-status"
                                    type="radio"
                                    name="{{ $statusField }}"
                                    id="{{ $statusField }}_pass"
                                    value="{{ \App\Models\PreTripInspection::STATUS_PASS }}"
                                    @checked($currentStatus === \App\Models\PreTripInspection::STATUS_PASS)
                                    required
                                >
                                <label class="inspection-choice-label pass" for="{{ $statusField }}_pass">ผ่าน</label>

                                <input
                                    class="inspection-choice-input inspection-status"
                                    type="radio"
                                    name="{{ $statusField }}"
                                    id="{{ $statusField }}_fail"
                                    value="{{ \App\Models\PreTripInspection::STATUS_FAIL }}"
                                    @checked($currentStatus === \App\Models\PreTripInspection::STATUS_FAIL)
                                    required
                                >
                                <label class="inspection-choice-label fail" for="{{ $statusField }}_fail">ไม่ผ่าน</label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <input type="text" name="{{ $noteField }}" value="{{ old($noteField, $inspection->{$noteField}) }}" class="form-control" placeholder="หมายเหตุ">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div>
    <label class="form-label">หมายเหตุรวม</label>
    <textarea name="overall_note" rows="3" class="form-control">{{ old('overall_note', $inspection->overall_note) }}</textarea>
</div>

@push('scripts')
<script>
const inspectionVehicleField = document.getElementById('inspection_vehicle_id');
const inspectionDriverField = document.getElementById('inspection_driver_id');
const inspectionStatusFields = Array.from(document.querySelectorAll('.inspection-status'));
const inspectionSummaryBadge = document.getElementById('inspection_summary_badge');

function syncInspectionDriver(force = false) {
    if (!inspectionVehicleField) return;

    let driverId = '';

    if (inspectionVehicleField.tagName === 'SELECT') {
        const selectedVehicle = inspectionVehicleField.options[inspectionVehicleField.selectedIndex];
        if (!selectedVehicle) return;
        driverId = selectedVehicle.dataset.primaryDriverId || '';
    } else {
        @if(!empty($lockedVehicle))
        driverId = '{{ $lockedVehicle->primary_driver_id }}';
        @endif
    }

    if (!driverId) return;

    if (force || !inspectionDriverField.value) {
        inspectionDriverField.value = driverId;
    }
}

function updateInspectionSummary() {
    const statusNames = [...new Set(inspectionStatusFields.map((field) => field.name))];
    const values = statusNames.map((name) => {
        const checked = document.querySelector(`input[name="${name}"]:checked`);
        return checked ? checked.value : '';
    });

    if (values.some((value) => value === '')) {
        inspectionSummaryBadge.className = 'badge text-bg-secondary';
        inspectionSummaryBadge.textContent = 'รอประเมิน';
        return;
    }

    if (values.every((value) => value === '{{ \App\Models\PreTripInspection::STATUS_PASS }}')) {
        inspectionSummaryBadge.className = 'badge text-bg-success';
        inspectionSummaryBadge.textContent = 'พร้อมวิ่ง';
        return;
    }

    inspectionSummaryBadge.className = 'badge text-bg-danger';
    inspectionSummaryBadge.textContent = 'ไม่พร้อมวิ่ง';
}

if (inspectionVehicleField && inspectionVehicleField.tagName === 'SELECT') {
    inspectionVehicleField.addEventListener('change', () => syncInspectionDriver(false));
}
inspectionStatusFields.forEach((field) => field.addEventListener('change', updateInspectionSummary));

syncInspectionDriver(false);
updateInspectionSummary();
</script>
@endpush
