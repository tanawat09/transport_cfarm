<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">ฟาร์ม</label>
        <select name="farm_id" class="form-select" required>
            <option value="">เลือกฟาร์ม</option>
            @foreach($farms as $farm)
                <option value="{{ $farm->id }}" @selected(old('farm_id', $routeStandard->farm_id) == $farm->id)>{{ $farm->farm_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">คู่สัญญา</label>
        <select name="vendor_id" class="form-select" required>
            <option value="">เลือกคู่สัญญา</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}" @selected(old('vendor_id', $routeStandard->vendor_id) == $vendor->id)>{{ $vendor->vendor_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">น้ำมันที่บริษัทกำหนด (ลิตร)</label>
        <input type="number" step="0.01" min="0" name="company_oil_liters" value="{{ old('company_oil_liters', $routeStandard->company_oil_liters) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">ระยะทางมาตรฐาน (กม.)</label>
        <input type="number" step="0.01" min="0" name="standard_distance_km" value="{{ old('standard_distance_km', $routeStandard->standard_distance_km) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">สถานะ</label>
        <select name="status" class="form-select" required>
            @foreach(['active' => 'ใช้งาน', 'inactive' => 'ไม่ใช้งาน'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $routeStandard->status ?: 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">หมายเหตุ</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $routeStandard->notes) }}</textarea>
    </div>
</div>
