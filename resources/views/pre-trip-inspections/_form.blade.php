@php
    $totalChecklistItems = count($checklistItems);
    $lockedVehicleLabel = !empty($lockedVehicle)
        ? trim($lockedVehicle->registration_number . ' - ' . ($lockedVehicle->brand ?: '-') . ($lockedVehicle->model ? ' / ' . $lockedVehicle->model : ''))
        : null;
@endphp

@push('styles')
<style>
    .inspection-shell {
        display: block;
        min-width: 0;
    }

    .inspection-main {
        display: grid;
        gap: 1.25rem;
        min-width: 0;
    }

    .inspection-panel {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 22px;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 251, 253, 0.98) 100%);
        box-shadow: 0 18px 32px rgba(15, 23, 42, 0.06);
        min-width: 0;
        overflow: hidden;
    }

    .inspection-panel-body {
        padding: 1.35rem;
    }

    .inspection-panel-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
        color: #19324a;
    }

    .inspection-panel-subtitle {
        margin: .4rem 0 0;
        color: #728295;
        font-size: .92rem;
        line-height: 1.6;
    }

    .inspection-meta-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 1.2rem;
    }

    .inspection-field-card {
        padding: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.78);
        min-width: 0;
    }

    .inspection-field-card.col-span-3 { grid-column: span 3; }
    .inspection-field-card.col-span-4 { grid-column: span 4; }
    .inspection-field-card.col-span-6 { grid-column: span 6; }

    .inspection-field-card .form-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .inspection-field-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .28rem .6rem;
        border-radius: 999px;
        background: rgba(31, 111, 120, 0.08);
        color: #1b5d65;
        font-size: .72rem;
        font-weight: 700;
    }

    .inspection-overview-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .8rem;
        margin-top: 1rem;
    }

    .inspection-overview-card {
        padding: .95rem 1rem;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(15, 34, 53, 0.96) 0%, rgba(31, 111, 120, 0.92) 100%);
        color: #fff;
        min-height: 110px;
    }

    .inspection-overview-card small {
        display: block;
        color: rgba(255, 255, 255, 0.72);
        font-size: .78rem;
        margin-bottom: .3rem;
    }

    .inspection-overview-card strong {
        display: block;
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .inspection-overview-card span {
        display: block;
        margin-top: .35rem;
        color: rgba(255, 255, 255, 0.76);
        font-size: .82rem;
        overflow-wrap: anywhere;
    }

    .inspection-checklist-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .inspection-summary-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 124px;
        padding: .7rem 1rem;
        border-radius: 999px;
        font-weight: 800;
        letter-spacing: .01em;
        background: #dfe7ef;
        color: #516274;
    }

    .inspection-summary-pill.is-pending {
        background: #dfe7ef;
        color: #516274;
    }

    .inspection-summary-pill.is-pass {
        background: rgba(34, 197, 94, 0.15);
        color: #166534;
    }

    .inspection-summary-pill.is-fail {
        background: rgba(239, 68, 68, 0.14);
        color: #b91c1c;
    }

    .inspection-checklist-grid {
        display: grid;
        gap: 1rem;
    }

    .inspection-item-card {
        position: relative;
        padding: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.84);
        transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
        min-width: 0;
    }

    .inspection-item-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.06);
    }

    .inspection-item-card.is-pass {
        border-color: rgba(22, 163, 74, 0.28);
        box-shadow: 0 16px 28px rgba(34, 197, 94, 0.08);
    }

    .inspection-item-card.is-fail {
        border-color: rgba(220, 38, 38, 0.26);
        box-shadow: 0 16px 28px rgba(239, 68, 68, 0.08);
    }

    .inspection-item-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: .85rem;
    }

    .inspection-item-key {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: rgba(31, 111, 120, 0.1);
        color: #1d5b62;
        font-size: .84rem;
        font-weight: 800;
        flex: 0 0 auto;
    }

    .inspection-item-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        color: #1d2939;
        overflow-wrap: anywhere;
    }

    .inspection-item-help {
        margin: .3rem 0 0;
        color: #77879a;
        font-size: .87rem;
        line-height: 1.55;
    }

    .inspection-item-status-text {
        font-size: .78rem;
        font-weight: 700;
        color: #8090a3;
        white-space: nowrap;
    }

    .inspection-item-grid {
        display: grid;
        grid-template-columns: minmax(240px, 280px) minmax(0, 1fr);
        gap: 1rem;
        align-items: center;
        min-width: 0;
    }

    .inspection-choice-group {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }

    .inspection-choice-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .inspection-choice-label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 54px;
        border: 1px solid rgba(148, 163, 184, 0.28);
        border-radius: 16px;
        padding: .75rem 1rem;
        font-weight: 800;
        text-align: center;
        cursor: pointer;
        background: #fff;
        color: #243446;
        transition: all .15s ease-in-out;
        box-shadow: inset 0 1px 1px rgba(15, 23, 42, 0.03);
        width: 100%;
    }

    .inspection-choice-label:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 18px rgba(15, 23, 42, 0.06);
    }

    .inspection-choice-label.pass::before,
    .inspection-choice-label.fail::before {
        margin-right: .45rem;
        font-size: 1rem;
        line-height: 1;
    }

    .inspection-choice-label.pass::before {
        content: '✓';
    }

    .inspection-choice-label.fail::before {
        content: '✕';
    }

    .inspection-choice-input:checked + .inspection-choice-label.pass {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        border-color: #16a34a;
        color: #fff;
    }

    .inspection-choice-input:checked + .inspection-choice-label.fail {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-color: #dc2626;
        color: #fff;
    }

    .inspection-note-input {
        min-height: 54px;
        width: 100%;
    }

    .inspection-overall-note {
        min-height: 130px;
        resize: vertical;
        width: 100%;
    }

    .inspection-field-card .form-control,
    .inspection-field-card .form-select,
    .inspection-item-card .form-control {
        width: 100%;
        max-width: 100%;
    }

    .inspection-item-top > .d-flex {
        min-width: 0;
    }

    @media (max-width: 991.98px) {
        .inspection-meta-grid,
        .inspection-overview-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .inspection-field-card.col-span-3,
        .inspection-field-card.col-span-4,
        .inspection-field-card.col-span-6 {
            grid-column: span 1;
        }

        .inspection-item-grid {
            grid-template-columns: 1fr;
            gap: .85rem;
        }
    }

    @media (max-width: 767.98px) {
        .inspection-panel-body {
            padding: .95rem;
        }

        .inspection-meta-grid,
        .inspection-overview-grid {
            grid-template-columns: 1fr;
        }

        .inspection-shell,
        .inspection-main,
        .inspection-checklist-grid {
            gap: .9rem;
        }

        .inspection-checklist-header,
        .inspection-item-top {
            flex-direction: column;
            align-items: stretch;
        }

        .inspection-panel-title {
            font-size: 1rem;
        }

        .inspection-panel-subtitle,
        .inspection-item-help {
            font-size: .84rem;
        }

        .inspection-field-card {
            padding: .9rem;
            border-radius: 16px;
        }

        .inspection-field-card .form-label {
            font-size: .9rem;
            align-items: flex-start;
            flex-direction: column;
        }

        .inspection-overview-card {
            min-height: auto;
            padding: .9rem;
        }

        .inspection-overview-card strong {
            font-size: 1.2rem;
        }

        .inspection-summary-pill {
            min-width: 100%;
            min-height: 48px;
            font-size: .94rem;
        }

        .inspection-item-card {
            padding: .9rem;
            border-radius: 18px;
        }

        .inspection-item-key {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            font-size: .78rem;
        }

        .inspection-item-title {
            font-size: .95rem;
            line-height: 1.45;
        }

        .inspection-item-status-text {
            white-space: normal;
        }

        .inspection-choice-group {
            grid-template-columns: 1fr;
            gap: .6rem;
        }

        .inspection-choice-label {
            min-height: 56px;
            font-size: .98rem;
            border-radius: 14px;
        }

        .inspection-note-input,
        .inspection-overall-note {
            border-radius: 14px;
        }
    }

    @media (max-width: 575.98px) {
        .inspection-panel {
            border-radius: 16px;
        }

        .inspection-panel-body {
            padding: .75rem;
        }

        .inspection-meta-grid,
        .inspection-checklist-grid {
            gap: .7rem;
            margin-top: .85rem;
        }

        .inspection-overview-grid {
            gap: .65rem;
            margin-top: .85rem;
        }

        .inspection-overview-card {
            border-radius: 14px;
        }

        .inspection-overview-card small {
            font-size: .72rem;
        }

        .inspection-overview-card strong {
            font-size: 1.05rem;
        }

        .inspection-overview-card span {
            font-size: .76rem;
        }

        .inspection-field-card {
            padding: .75rem;
            border-radius: 14px;
        }

        .inspection-field-chip {
            padding: .22rem .5rem;
            font-size: .68rem;
        }

        .inspection-checklist-header {
            gap: .7rem;
            margin-bottom: .75rem;
        }

        .inspection-item-card {
            padding: .75rem;
            border-radius: 16px;
        }

        .inspection-item-top {
            gap: .65rem;
            margin-bottom: .7rem;
        }

        .inspection-item-top > .d-flex {
            gap: .65rem !important;
        }

        .inspection-item-key {
            width: 32px;
            height: 32px;
        }

        .inspection-item-title {
            font-size: .9rem;
        }

        .inspection-item-help,
        .inspection-item-status-text {
            font-size: .78rem;
        }

        .inspection-choice-label {
            min-height: 52px;
            padding: .65rem .85rem;
            font-size: .94rem;
        }

        .inspection-note-input {
            min-height: 50px;
        }

        .inspection-overall-note {
            min-height: 110px;
        }
    }
</style>
@endpush

<div class="inspection-shell">
    <div class="inspection-main">
        <section class="inspection-panel">
            <div class="inspection-panel-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h2 class="inspection-panel-title">ข้อมูลก่อนตรวจเช็ก</h2>
                        <p class="inspection-panel-subtitle">กรอกวันเวลา เลือกรถ และตรวจสอบข้อมูลพื้นฐานให้ครบก่อนประเมินความพร้อมของรถ</p>
                    </div>
                    @if($lockedVehicleLabel)
                        <div class="inspection-field-chip">เปิดจาก QR ของ {{ $lockedVehicle->registration_number }}</div>
                    @endif
                </div>

                <div class="inspection-overview-grid">
                    <div class="inspection-overview-card">
                        <small>รถที่กำลังตรวจ</small>
                        <strong id="inspection_vehicle_preview">{{ $lockedVehicle->registration_number ?? ($inspection->vehicle?->registration_number ?? 'ยังไม่ได้เลือก') }}</strong>
                        <span id="inspection_vehicle_preview_detail">{{ $lockedVehicleLabel ?: (($inspection->vehicle?->brand ?: '-') . ($inspection->vehicle?->model ? ' / ' . $inspection->vehicle?->model : '')) }}</span>
                    </div>
                    <div class="inspection-overview-card">
                        <small>ผู้ขับที่รับผิดชอบ</small>
                        <strong id="inspection_driver_preview">{{ $inspection->driver?->full_name ?? 'รอเลือกพนักงานขับ' }}</strong>
                        <span id="inspection_driver_preview_detail">{{ $inspection->driver?->employee_code ? 'รหัส ' . $inspection->driver?->employee_code : 'ระบบจะช่วยดึงคนขับประจำรถให้อัตโนมัติ' }}</span>
                    </div>
                    <div class="inspection-overview-card">
                        <small>ความพร้อมล่าสุด</small>
                        <strong id="inspection_header_status">รอประเมิน</strong>
                        <span id="inspection_header_status_detail">เลือกรายการให้ครบทั้ง {{ $totalChecklistItems }} หัวข้อก่อนบันทึก</span>
                    </div>
                </div>

                <div class="inspection-meta-grid">
                    <div class="inspection-field-card col-span-3">
                        <label class="form-label" for="inspection_date">
                            <span>วันที่ตรวจ</span>
                            <span class="inspection-field-chip">จำเป็น</span>
                        </label>
                        <input id="inspection_date" type="date" name="inspection_date" value="{{ old('inspection_date', optional($inspection->inspection_date)->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="inspection-field-card col-span-3">
                        <label class="form-label" for="inspection_time">
                            <span>เวลาตรวจ</span>
                            <span class="inspection-field-chip">จำเป็น</span>
                        </label>
                        <input id="inspection_time" type="time" name="inspection_time" value="{{ old('inspection_time', $inspection->inspection_time) }}" class="form-control" required>
                    </div>
                    <div class="inspection-field-card col-span-3">
                        <label class="form-label" for="inspection_vehicle_id">
                            <span>ทะเบียนรถ</span>
                            <span class="inspection-field-chip">{{ $lockedVehicleLabel ? 'ล็อกไว้' : 'เลือกจากรายการ' }}</span>
                        </label>
                        @if(!empty($lockedVehicle))
                            <input type="hidden" name="vehicle_id" id="inspection_vehicle_id" value="{{ old('vehicle_id', $lockedVehicle->id) }}">
                            <input type="text" class="form-control" value="{{ $lockedVehicleLabel }}" readonly>
                        @else
                            <select name="vehicle_id" id="inspection_vehicle_id" class="form-select" required>
                                <option value="">เลือกทะเบียนรถ</option>
                                @foreach($vehicles as $vehicle)
                                    <option
                                        value="{{ $vehicle->id }}"
                                        data-primary-driver-id="{{ $vehicle->primary_driver_id }}"
                                        data-registration-number="{{ $vehicle->registration_number }}"
                                        data-vehicle-detail="{{ trim(($vehicle->brand ?: '-') . ($vehicle->model ? ' / ' . $vehicle->model : '')) }}"
                                        @selected(old('vehicle_id', $inspection->vehicle_id) == $vehicle->id)
                                    >
                                        {{ $vehicle->registration_number }} - {{ $vehicle->brand }}{{ $vehicle->model ? ' / ' . $vehicle->model : '' }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="inspection-field-card col-span-3">
                        <label class="form-label" for="inspection_driver_id">
                            <span>พนักงานขับ</span>
                            <span class="inspection-field-chip">ดึงอัตโนมัติได้</span>
                        </label>
                        <select name="driver_id" id="inspection_driver_id" class="form-select">
                            <option value="">เลือกพนักงานขับ</option>
                            @foreach($drivers as $driver)
                                <option
                                    value="{{ $driver->id }}"
                                    data-driver-name="{{ $driver->full_name }}"
                                    data-driver-code="{{ $driver->employee_code }}"
                                    @selected(old('driver_id', $inspection->driver_id) == $driver->id)
                                >
                                    {{ $driver->employee_code }} - {{ $driver->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="inspection-field-card col-span-6">
                        <label class="form-label" for="odometer_km">
                            <span>เลขไมล์ปัจจุบัน</span>
                            <span class="inspection-field-chip">ถ้ามี</span>
                        </label>
                        <input id="odometer_km" type="number" step="0.01" min="0" name="odometer_km" value="{{ old('odometer_km', $inspection->odometer_km) }}" class="form-control" placeholder="เช่น 225566.00">
                    </div>
                </div>
            </div>
        </section>

        <section class="inspection-panel">
            <div class="inspection-panel-body">
                <div class="inspection-checklist-header">
                    <div>
                        <h2 class="inspection-panel-title">รายการตรวจเช็กก่อนวิ่ง</h2>
                        <p class="inspection-panel-subtitle">กดเลือกผลตรวจเป็น ผ่าน หรือ ไม่ผ่าน ให้ครบทุกข้อ และใส่หมายเหตุเมื่อพบสิ่งผิดปกติ</p>
                    </div>
                    <div id="inspection_summary_badge" class="inspection-summary-pill is-pending">รอประเมิน</div>
                </div>

                <div class="inspection-checklist-grid">
                    @foreach($checklistItems as $loopIndex => $item)
                        @php
                            $key = $item->key;
                            $statusField = "inspection_items.{$key}.status";
                            $noteField = "inspection_items.{$key}.note";
                            $legacyStatusField = $key . '_status';
                            $legacyNoteField = $key . '_note';
                            $storedResult = $inspection->checklist_results[$key] ?? [];
                            $currentStatus = old($statusField, $storedResult['status'] ?? $inspection->{$legacyStatusField});
                            $currentNote = old($noteField, $storedResult['note'] ?? $inspection->{$legacyNoteField});
                        @endphp
                        <div class="inspection-item-card" data-inspection-name="inspection_items[{{ $key }}][status]">
                            <div class="inspection-item-top">
                                <div class="d-flex gap-3">
                                    <div class="inspection-item-key">{{ str_pad((string) ($loopIndex + 1), 2, '0', STR_PAD_LEFT) }}</div>
                                    <div>
                                        <h3 class="inspection-item-title">{{ $item->label }}</h3>
                                        <p class="inspection-item-help">ตรวจสภาพและยืนยันผลก่อนออกรถทุกครั้ง</p>
                                    </div>
                                </div>
                                <div class="inspection-item-status-text" data-inspection-status-text>ยังไม่ได้เลือกผลตรวจ</div>
                            </div>

                            <div class="inspection-item-grid">
                                <div class="inspection-choice-group">
                                    <div>
                                        <input
                                            class="inspection-choice-input inspection-status"
                                            type="radio"
                                            name="inspection_items[{{ $key }}][status]"
                                            id="inspection_{{ $key }}_pass"
                                            value="{{ \App\Models\PreTripInspection::STATUS_PASS }}"
                                            @checked($currentStatus === \App\Models\PreTripInspection::STATUS_PASS)
                                            required
                                        >
                                        <label class="inspection-choice-label pass" for="inspection_{{ $key }}_pass">ผ่าน</label>
                                    </div>
                                    <div>
                                        <input
                                            class="inspection-choice-input inspection-status"
                                            type="radio"
                                            name="inspection_items[{{ $key }}][status]"
                                            id="inspection_{{ $key }}_fail"
                                            value="{{ \App\Models\PreTripInspection::STATUS_FAIL }}"
                                            @checked($currentStatus === \App\Models\PreTripInspection::STATUS_FAIL)
                                            required
                                        >
                                        <label class="inspection-choice-label fail" for="inspection_{{ $key }}_fail">ไม่ผ่าน</label>
                                    </div>
                                </div>
                                <div>
                                    <input type="text" name="inspection_items[{{ $key }}][note]" value="{{ $currentNote }}" class="form-control inspection-note-input" placeholder="บันทึกจุดที่ต้องแก้ไขหรือข้อสังเกต">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="inspection-panel">
            <div class="inspection-panel-body">
                <h2 class="inspection-panel-title">หมายเหตุรวม</h2>
                <p class="inspection-panel-subtitle">ใช้สรุปประเด็นสำคัญเพิ่มเติม เช่น ข้อควรระวังหรือสิ่งที่ต้องติดตามหลังตรวจ</p>
                <textarea name="overall_note" rows="4" class="form-control inspection-overall-note mt-3">{{ old('overall_note', $inspection->overall_note) }}</textarea>
            </div>
        </section>
    </div>

</div>

@push('scripts')
<script>
const inspectionVehicleField = document.getElementById('inspection_vehicle_id');
const inspectionDriverField = document.getElementById('inspection_driver_id');
const inspectionStatusFields = Array.from(document.querySelectorAll('.inspection-status'));
const inspectionSummaryBadge = document.getElementById('inspection_summary_badge');
const inspectionHeaderStatus = document.getElementById('inspection_header_status');
const inspectionHeaderStatusDetail = document.getElementById('inspection_header_status_detail');
const inspectionVehiclePreview = document.getElementById('inspection_vehicle_preview');
const inspectionVehiclePreviewDetail = document.getElementById('inspection_vehicle_preview_detail');
const inspectionDriverPreview = document.getElementById('inspection_driver_preview');
const inspectionDriverPreviewDetail = document.getElementById('inspection_driver_preview_detail');
const inspectionItemCards = Array.from(document.querySelectorAll('.inspection-item-card'));

function getSelectedVehicleOption() {
    if (!inspectionVehicleField) return null;

    if (inspectionVehicleField.tagName === 'SELECT') {
        return inspectionVehicleField.options[inspectionVehicleField.selectedIndex] || null;
    }

    return null;
}

function syncInspectionDriver(force = false) {
    if (!inspectionVehicleField || !inspectionDriverField) return;

    let driverId = '';

    if (inspectionVehicleField.tagName === 'SELECT') {
        const selectedVehicle = getSelectedVehicleOption();
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

function updateVehiclePreview() {
    if (!inspectionVehiclePreview || !inspectionVehiclePreviewDetail || !inspectionVehicleField) return;

    if (inspectionVehicleField.tagName === 'SELECT') {
        const selectedVehicle = getSelectedVehicleOption();

        if (!selectedVehicle || !selectedVehicle.value) {
            inspectionVehiclePreview.textContent = 'ยังไม่ได้เลือก';
            inspectionVehiclePreviewDetail.textContent = 'เลือกทะเบียนรถก่อนเริ่มตรวจ';
            return;
        }

        inspectionVehiclePreview.textContent = selectedVehicle.dataset.registrationNumber || selectedVehicle.textContent.trim();
        inspectionVehiclePreviewDetail.textContent = selectedVehicle.dataset.vehicleDetail || '-';
        return;
    }

    @if(!empty($lockedVehicle))
    inspectionVehiclePreview.textContent = '{{ $lockedVehicle->registration_number }}';
    inspectionVehiclePreviewDetail.textContent = @json(trim(($lockedVehicle->brand ?: '-') . ($lockedVehicle->model ? ' / ' . $lockedVehicle->model : '')));
    @endif
}

function updateDriverPreview() {
    if (!inspectionDriverPreview || !inspectionDriverPreviewDetail || !inspectionDriverField) return;

    const selectedDriver = inspectionDriverField.options[inspectionDriverField.selectedIndex];

    if (!selectedDriver || !selectedDriver.value) {
        inspectionDriverPreview.textContent = 'รอเลือกพนักงานขับ';
        inspectionDriverPreviewDetail.textContent = 'ระบบจะช่วยดึงคนขับประจำรถให้อัตโนมัติ';
        return;
    }

    inspectionDriverPreview.textContent = selectedDriver.dataset.driverName || selectedDriver.textContent.trim();
    inspectionDriverPreviewDetail.textContent = selectedDriver.dataset.driverCode
        ? `รหัส ${selectedDriver.dataset.driverCode}`
        : 'บันทึกชื่อพนักงานขับสำหรับรายการนี้';
}

function updateInspectionItemStates() {
    inspectionItemCards.forEach((card) => {
        card.classList.remove('is-pass', 'is-fail');

        const fieldName = card.dataset.inspectionName;
        const checked = fieldName ? document.querySelector(`input[name="${fieldName}"]:checked`) : null;
        const statusText = card.querySelector('[data-inspection-status-text]');

        if (!checked) {
            if (statusText) {
                statusText.textContent = 'ยังไม่ได้เลือกผลตรวจ';
            }
            return;
        }

        if (checked.value === '{{ \App\Models\PreTripInspection::STATUS_PASS }}') {
            card.classList.add('is-pass');
            if (statusText) {
                statusText.textContent = 'สถานะ: ผ่าน';
            }
            return;
        }

        card.classList.add('is-fail');
        if (statusText) {
            statusText.textContent = 'สถานะ: ไม่ผ่าน';
        }
    });
}

function updateInspectionSummary() {
    const statusNames = [...new Set(inspectionStatusFields.map((field) => field.name))];
    const values = statusNames.map((name) => {
        const checked = document.querySelector(`input[name="${name}"]:checked`);
        return checked ? checked.value : '';
    });

    const totalCount = statusNames.length;
    const passCount = values.filter((value) => value === '{{ \App\Models\PreTripInspection::STATUS_PASS }}').length;
    const failCount = values.filter((value) => value === '{{ \App\Models\PreTripInspection::STATUS_FAIL }}').length;
    const pendingCount = totalCount - passCount - failCount;

    if (pendingCount > 0) {
        inspectionSummaryBadge.className = 'inspection-summary-pill is-pending';
        inspectionSummaryBadge.textContent = 'รอประเมิน';
        inspectionHeaderStatus.textContent = 'รอประเมิน';
        inspectionHeaderStatusDetail.textContent = `ยังเหลืออีก ${pendingCount} หัวข้อที่ต้องเลือกผลตรวจ`;
        updateInspectionItemStates();
        return;
    }

    if (failCount === 0) {
        inspectionSummaryBadge.className = 'inspection-summary-pill is-pass';
        inspectionSummaryBadge.textContent = 'พร้อมวิ่ง';
        inspectionHeaderStatus.textContent = 'พร้อมวิ่ง';
        inspectionHeaderStatusDetail.textContent = `ผ่านครบ ${passCount} จาก ${totalCount} หัวข้อ รถพร้อมออกงาน`;
        updateInspectionItemStates();
        return;
    }

    inspectionSummaryBadge.className = 'inspection-summary-pill is-fail';
    inspectionSummaryBadge.textContent = 'ไม่พร้อมวิ่ง';
    inspectionHeaderStatus.textContent = 'ไม่พร้อมวิ่ง';
    inspectionHeaderStatusDetail.textContent = `พบ ${failCount} หัวข้อที่ไม่ผ่าน ควรแก้ไขก่อนออกรถ`;

    updateInspectionItemStates();
}

if (inspectionVehicleField && inspectionVehicleField.tagName === 'SELECT') {
    inspectionVehicleField.addEventListener('change', () => {
        syncInspectionDriver(false);
        updateVehiclePreview();
        updateDriverPreview();
    });
}

if (inspectionDriverField) {
    inspectionDriverField.addEventListener('change', updateDriverPreview);
}

inspectionStatusFields.forEach((field) => field.addEventListener('change', updateInspectionSummary));

syncInspectionDriver(false);
updateVehiclePreview();
updateDriverPreview();
updateInspectionSummary();
</script>
@endpush
