<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">ทะเบียนรถ</label>
        <input type="text" name="registration_number" value="{{ old('registration_number', $vehicle->registration_number) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">วันที่จดทะเบียน</label>
        <input type="date" name="registered_at" value="{{ old('registered_at', optional($vehicle->registered_at)->format('Y-m-d')) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">ประเภทรถ</label>
        <select name="vehicle_type" class="form-select">
            <option value="">เลือกประเภทรถ</option>
            @foreach([
                'รถยนต์บรรทุกส่วนบุคคล',
                'รถกึ่งพ่วงบรรทุกอาหารสัตว์',
                'ลากจูง',
                'รถไถ',
                'รถยนต์นั่งส่วนบุคคลไม่เกิน 7 คน',
                'รถไถ คูโบต้า',
            ] as $vehicleType)
                <option value="{{ $vehicleType }}" @selected(old('vehicle_type', $vehicle->vehicle_type) === $vehicleType)>{{ $vehicleType }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">ยี่ห้อ</label>
        <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">รุ่น</label>
        <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">ลากจูง</label>
        <select name="towing_vehicle" class="form-select">
            <option value="">เลือกทะเบียนลากจูง</option>
            @foreach($towingVehicles as $towingVehicle)
                <option value="{{ $towingVehicle->registration_number }}" @selected(old('towing_vehicle', $vehicle->towing_vehicle) === $towingVehicle->registration_number)>
                    {{ $towingVehicle->registration_number }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">ความจุ (กก.)</label>
        <input type="number" step="0.01" min="0" name="capacity_kg" value="{{ old('capacity_kg', $vehicle->capacity_kg) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">อัตราสิ้นเปลืองมาตรฐาน (กม./ลิตร)</label>
        <input type="number" step="0.01" min="0" name="standard_fuel_rate_km_per_liter" value="{{ old('standard_fuel_rate_km_per_liter', $vehicle->standard_fuel_rate_km_per_liter) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">พนักงานขับประจำรถ</label>
        <select name="primary_driver_id" class="form-select">
            <option value="">เลือกพนักงานขับ</option>
            @foreach($drivers as $driver)
                <option value="{{ $driver->id }}" @selected((string) old('primary_driver_id', $vehicle->primary_driver_id) === (string) $driver->id)>
                    {{ $driver->employee_code }} - {{ $driver->full_name }}
                </option>
            @endforeach
        </select>
        <div class="form-text">ใช้กำหนดคนขับประจำรถ เพื่อช่วยเลือกค่าเริ่มต้นตอนบันทึกเที่ยวขนส่ง</div>
    </div>
    <div class="col-md-4">
        <label class="form-label">สถานะ</label>
        <select name="status" class="form-select" required>
            @foreach(['active' => 'ใช้งาน', 'inactive' => 'ไม่ใช้งาน', 'maintenance' => 'ซ่อมบำรุง'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $vehicle->status ?: 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">หมายเหตุ</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $vehicle->notes) }}</textarea>
    </div>
</div>
