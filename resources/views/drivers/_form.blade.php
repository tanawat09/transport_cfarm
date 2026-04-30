<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">รหัสพนักงาน</label>
        <input type="text" name="employee_code" value="{{ old('employee_code', $driver->employee_code) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">ชื่อ-นามสกุล</label>
        <input type="text" name="full_name" value="{{ old('full_name', $driver->full_name) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">เบอร์โทร</label>
        <input type="text" name="phone" value="{{ old('phone', $driver->phone) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">เลขใบขับขี่</label>
        <input type="text" name="driving_license_number" value="{{ old('driving_license_number', $driver->driving_license_number) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">วันหมดอายุใบขับขี่</label>
        <input type="date" name="driving_license_expiry_date" value="{{ old('driving_license_expiry_date', optional($driver->driving_license_expiry_date)->format('Y-m-d')) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">สถานะ</label>
        <select name="status" class="form-select" required>
            @foreach(['active' => 'ใช้งาน', 'inactive' => 'ไม่ใช้งาน', 'suspended' => 'พักงาน'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $driver->status ?: 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">หมายเหตุ</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $driver->notes) }}</textarea>
    </div>
</div>
