<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">ชื่อคู่สัญญา</label>
        <input type="text" name="vendor_name" value="{{ old('vendor_name', $vendor->vendor_name) }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">สถานะ</label>
        <select name="status" class="form-select" required>
            @foreach(['active' => 'ใช้งาน', 'inactive' => 'ไม่ใช้งาน'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $vendor->status ?: 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">รายละเอียด</label>
        <textarea name="details" rows="4" class="form-control">{{ old('details', $vendor->details) }}</textarea>
    </div>
</div>
