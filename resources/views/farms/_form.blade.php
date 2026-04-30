<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">ชื่อฟาร์ม</label>
        <input type="text" name="farm_name" value="{{ old('farm_name', $farm->farm_name) }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">เจ้าของฟาร์ม</label>
        <input type="text" name="owner_name" value="{{ old('owner_name', $farm->owner_name) }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">เบอร์โทร</label>
        <input type="text" name="phone" value="{{ old('phone', $farm->phone) }}" class="form-control">
    </div>
    <div class="col-12">
        <label class="form-label">ที่อยู่</label>
        <textarea name="address" rows="3" class="form-control">{{ old('address', $farm->address) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">หมายเหตุ</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $farm->notes) }}</textarea>
    </div>
</div>
