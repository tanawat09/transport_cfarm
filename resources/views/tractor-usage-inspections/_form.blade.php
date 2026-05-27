@push('styles')
<style>
    .tractor-shell {
        display: grid;
        gap: 1rem;
    }

    .tractor-meta-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 1rem;
    }

    .tractor-card {
        border: 1px solid rgba(148, 163, 184, 0.2);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.05);
    }

    .tractor-card-body {
        padding: 1.1rem;
    }

    .tractor-meta-card {
        grid-column: span 3;
    }

    .tractor-meta-card.col-span-6 {
        grid-column: span 6;
    }

    .tractor-kicker {
        display: inline-flex;
        align-items: center;
        margin-bottom: .55rem;
        padding: .3rem .65rem;
        border-radius: 999px;
        background: rgba(31, 111, 120, 0.1);
        color: #184d57;
        font-size: .74rem;
        font-weight: 800;
    }

    .tractor-group-grid {
        display: grid;
        gap: 1rem;
    }

    .tractor-group-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
        color: #17324d;
    }

    .tractor-group-subtitle {
        margin: .35rem 0 0;
        color: #708092;
        font-size: .9rem;
    }

    .tractor-item-list {
        display: grid;
        gap: .9rem;
        margin-top: 1rem;
    }

    .tractor-item {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 16px;
        background: #fbfdff;
        padding: .95rem;
    }

    .tractor-item-title {
        margin: 0 0 .8rem;
        font-size: .96rem;
        font-weight: 700;
        color: #203040;
    }

    .tractor-choice-group {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }

    .tractor-choice-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .tractor-choice-label {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        border: 1px solid rgba(148, 163, 184, 0.26);
        border-radius: 14px;
        background: #fff;
        color: #2a3c50;
        font-weight: 700;
        cursor: pointer;
        transition: .18s ease;
    }

    .tractor-choice-input:checked + .tractor-choice-label.pass {
        border-color: rgba(22, 163, 74, 0.45);
        background: rgba(22, 163, 74, 0.12);
        color: #166534;
    }

    .tractor-choice-input:checked + .tractor-choice-label.fail {
        border-color: rgba(220, 38, 38, 0.45);
        background: rgba(220, 38, 38, 0.12);
        color: #991b1b;
    }

    .tractor-note {
        margin-top: .75rem;
    }

    .tractor-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }

    .tractor-summary-card {
        border-radius: 16px;
        padding: 1rem;
        background: linear-gradient(135deg, rgba(23, 50, 77, 0.96), rgba(31, 111, 120, 0.9));
        color: #fff;
    }

    .tractor-summary-card small {
        display: block;
        opacity: .78;
    }

    .tractor-summary-card strong {
        display: block;
        margin-top: .35rem;
        font-size: 1.55rem;
        font-weight: 800;
    }

    .tractor-summary-card span {
        display: block;
        margin-top: .35rem;
        opacity: .82;
        font-size: .88rem;
    }

    @media (max-width: 991.98px) {
        .tractor-meta-card,
        .tractor-meta-card.col-span-6 {
            grid-column: span 6;
        }
    }

    @media (max-width: 767.98px) {
        .tractor-meta-grid,
        .tractor-summary {
            grid-template-columns: 1fr;
        }

        .tractor-meta-card,
        .tractor-meta-card.col-span-6 {
            grid-column: span 12;
        }

        .tractor-choice-group {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@php
    $workingHoursValue = old('working_hours', $inspection->working_hours);
    $existingResults = $inspection->checklist_results ?? [];
@endphp

<div class="tractor-shell">
    <div class="tractor-summary">
        <div class="tractor-summary-card">
            <small>หมวดตรวจเช็ก</small>
            <strong>{{ count($checklistGroups) }}</strong>
            <span>ตรวจรายการหลักของรถไถให้ครบทุกส่วน</span>
        </div>
        <div class="tractor-summary-card">
            <small>รายการย่อย</small>
            <strong>{{ count(\App\Models\TractorUsageInspection::checklistItems()) }}</strong>
            <span>เลือกผลตรวจให้ครบทุกข้อก่อนบันทึก</span>
        </div>
        <div class="tractor-summary-card">
            <small>สถานะสรุป</small>
            <strong id="tractor_ready_counter">0</strong>
            <span id="tractor_ready_caption">ผ่านแล้ว 0 รายการ</span>
        </div>
    </div>

    <div class="tractor-meta-grid">
        <div class="tractor-card tractor-meta-card">
            <div class="tractor-card-body">
                <div class="tractor-kicker">วันที่ตรวจ</div>
                <label class="form-label">วันที่</label>
                <input
                    type="date"
                    name="inspection_date"
                    value="{{ old('inspection_date', optional($inspection->inspection_date)->format('Y-m-d') ?: now()->toDateString()) }}"
                    class="form-control"
                    required
                >
            </div>
        </div>
        <div class="tractor-card tractor-meta-card">
            <div class="tractor-card-body">
                <div class="tractor-kicker">เวลา</div>
                <label class="form-label">เวลา</label>
                <input
                    type="time"
                    name="inspection_time"
                    value="{{ old('inspection_time', $inspection->inspection_time ?: now()->format('H:i')) }}"
                    class="form-control"
                    required
                >
            </div>
        </div>
        <div class="tractor-card tractor-meta-card col-span-6">
            <div class="tractor-card-body">
                <div class="tractor-kicker">ชั่วโมงการใช้งาน</div>
                <label class="form-label">ชั่วโมงการทำงาน</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="working_hours"
                    value="{{ $workingHoursValue }}"
                    class="form-control"
                    placeholder="เช่น 1280.50"
                    required
                >
            </div>
        </div>
        <div class="tractor-card tractor-meta-card col-span-6">
            <div class="tractor-card-body">
                <div class="tractor-kicker">รถไถ</div>
                <label class="form-label">ทะเบียนรถ / เครื่องจักร</label>
                <select name="vehicle_id" id="tractor_vehicle_id" class="form-select" required>
                    <option value="">เลือกรถไถ</option>
                    @foreach($vehicles as $vehicle)
                        <option
                            value="{{ $vehicle->id }}"
                            @selected(old('vehicle_id', $inspection->vehicle_id) == $vehicle->id)
                        >
                            {{ $vehicle->registration_number }} - {{ $vehicle->vehicle_type }}{{ $vehicle->brand ? ' / ' . $vehicle->brand : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="tractor-card tractor-meta-card col-span-6">
            <div class="tractor-card-body">
                <div class="tractor-kicker">ฟาร์ม</div>
                <label class="form-label">ฟาร์ม</label>
                <select name="farm_id" id="tractor_farm_id" class="form-select" required>
                    <option value="">เลือกฟาร์ม</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}" @selected(old('farm_id', $inspection->farm_id) == $farm->id)>
                            {{ $farm->farm_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="tractor-group-grid">
        @foreach($checklistGroups as $groupIndex => $group)
            <section class="tractor-card">
                <div class="tractor-card-body">
                    <h3 class="tractor-group-title">{{ $groupIndex + 2 }}. {{ $group['label'] }}</h3>
                    <p class="tractor-group-subtitle">ตรวจให้ครบทุกหัวข้อย่อยของหมวดนี้ แล้วเพิ่มหมายเหตุเมื่อพบความผิดปกติ</p>

                    <div class="tractor-item-list">
                        @foreach($group['items'] as $item)
                            @php
                                $itemKey = $item['key'];
                                $statusValue = old("inspection_items.{$itemKey}.status", $existingResults[$itemKey]['status'] ?? null);
                                $noteValue = old("inspection_items.{$itemKey}.note", $existingResults[$itemKey]['note'] ?? null);
                            @endphp
                            <div class="tractor-item">
                                <h4 class="tractor-item-title">{{ $item['label'] }}</h4>
                                <div class="tractor-choice-group">
                                    @foreach($statusOptions as $statusKey => $statusLabel)
                                        <div>
                                            <input
                                                class="tractor-choice-input"
                                                type="radio"
                                                name="inspection_items[{{ $itemKey }}][status]"
                                                id="tractor_{{ $itemKey }}_{{ $statusKey }}"
                                                value="{{ $statusKey }}"
                                                data-tractor-status
                                                @checked($statusValue === $statusKey)
                                            >
                                            <label
                                                class="tractor-choice-label {{ $statusKey === \App\Models\TractorUsageInspection::STATUS_PASS ? 'pass' : 'fail' }}"
                                                for="tractor_{{ $itemKey }}_{{ $statusKey }}"
                                            >
                                                {{ $statusLabel }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="tractor-note">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="inspection_items[{{ $itemKey }}][note]"
                                        value="{{ $noteValue }}"
                                        placeholder="หมายเหตุเพิ่มเติม"
                                    >
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    <div class="tractor-card">
        <div class="tractor-card-body">
            <div class="tractor-kicker">หมายเหตุรวม</div>
            <label class="form-label">สรุปเพิ่มเติม</label>
            <textarea
                name="overall_note"
                rows="3"
                class="form-control"
                placeholder="บันทึกข้อสังเกตเพิ่มเติมของรถไถคันนี้"
            >{{ old('overall_note', $inspection->overall_note) }}</textarea>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const statusFields = Array.from(document.querySelectorAll('[data-tractor-status]'));
    const readyCounter = document.getElementById('tractor_ready_counter');
    const readyCaption = document.getElementById('tractor_ready_caption');
    const totalItems = {{ count(\App\Models\TractorUsageInspection::checklistItems()) }};

    const updateReadyCounter = () => {
        const passCount = statusFields.filter((field) => field.checked && field.value === '{{ \App\Models\TractorUsageInspection::STATUS_PASS }}').length;
        readyCounter.textContent = String(passCount);
        readyCaption.textContent = `ผ่านแล้ว ${passCount} จาก ${totalItems} รายการ`;
    };

    statusFields.forEach((field) => field.addEventListener('change', updateReadyCounter));

    updateReadyCounter();
});
</script>
@endpush
