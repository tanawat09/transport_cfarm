<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">ทะเบียนรถ</label>
        <select name="vehicle_id" class="form-select" required>
            <option value="">เลือกรถ</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" @selected((string) old('vehicle_id', $document->vehicle_id) === (string) $vehicle->id)>
                    {{ $vehicle->registration_number }} - {{ $vehicle->brand }}{{ $vehicle->model ? ' / ' . $vehicle->model : '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">ชนิดเอกสาร</label>
        <select name="document_type" class="form-select" required>
            <option value="">เลือกชนิดเอกสาร</option>
            @foreach($typeOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('document_type', $document->document_type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">เลขที่เอกสาร/กรมธรรม์</label>
        <input type="text" name="document_no" value="{{ old('document_no', $document->document_no) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">บริษัท/ผู้ให้บริการ</label>
        <input type="text" name="provider_name" value="{{ old('provider_name', $document->provider_name) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">ทุนประกัน</label>
        <input type="number" step="0.01" min="0" name="insurance_capital" value="{{ old('insurance_capital', $document->insurance_capital) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">วันที่เริ่ม</label>
        <input type="date" name="issued_at" value="{{ old('issued_at', optional($document->issued_at)->format('Y-m-d')) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">วันหมดอายุ</label>
        <input type="date" name="expires_at" value="{{ old('expires_at', optional($document->expires_at)->format('Y-m-d')) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">ภาษี</label>
        <input type="date" name="tax_expires_at" value="{{ old('tax_expires_at', optional($document->tax_expires_at)->format('Y-m-d')) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">พรบ</label>
        <input type="date" name="compulsory_expires_at" value="{{ old('compulsory_expires_at', optional($document->compulsory_expires_at)->format('Y-m-d')) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">ประกัน</label>
        <input type="date" name="insurance_expires_at" value="{{ old('insurance_expires_at', optional($document->insurance_expires_at)->format('Y-m-d')) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">แจ้งเตือนก่อนหมดอายุ (วัน)</label>
        <input type="number" name="alert_before_days" min="1" max="365" value="{{ old('alert_before_days', $document->alert_before_days ?? 30) }}" class="form-control" required>
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch pb-2">
            <input type="checkbox" name="is_alert_enabled" value="1" id="is_alert_enabled" class="form-check-input" @checked(old('is_alert_enabled', $document->is_alert_enabled ?? true))>
            <label for="is_alert_enabled" class="form-check-label">แจ้งเตือนผ่าน Telegram</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label">หมายเหตุ</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $document->notes) }}</textarea>
    </div>
</div>
