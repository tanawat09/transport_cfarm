@php
    $selectedReasonId = old('oil_compensation_reason_id', $transportJob->oil_compensation_reason_id);
@endphp

@push('styles')
<style>
    .readonly-label {
        color: var(--bs-danger);
        font-weight: 600;
    }

    .readonly-input {
        color: var(--bs-danger);
        font-weight: 600;
        background-color: var(--bs-light);
        border-color: rgba(var(--bs-danger-rgb), 0.35);
    }
</style>
@endpush

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">วันที่ขนส่ง</label>
        <input type="date" name="transport_date" id="transport_date" value="{{ old('transport_date', optional($transportJob->transport_date)->format('Y-m-d') ?: now()->toDateString()) }}" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label class="form-label readonly-label">เลขที่เอกสาร</label>
        <input type="text" value="{{ $documentNo }}" id="document_no_preview" class="form-control readonly-input" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">รถ</label>
        <select name="vehicle_id" id="vehicle_id" class="form-select" required>
            <option value="">เลือกรถ</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" data-primary-driver-id="{{ $vehicle->primary_driver_id }}" @selected(old('vehicle_id', $transportJob->vehicle_id) == $vehicle->id)>
                    {{ $vehicle->registration_number }} - {{ $vehicle->brand }}{{ $vehicle->model ? ' / ' . $vehicle->model : '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">พนักงานขับ</label>
        <select name="driver_id" id="driver_id" class="form-select" required>
            <option value="">เลือกพนักงานขับ</option>
            @foreach($drivers as $driver)
                <option value="{{ $driver->id }}" @selected(old('driver_id', $transportJob->driver_id) == $driver->id)>
                    {{ $driver->employee_code }} - {{ $driver->full_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">ฟาร์ม</label>
        <select name="farm_id" id="farm_id" class="form-select" required>
            <option value="">เลือกฟาร์ม</option>
            @foreach($farms as $farm)
                <option value="{{ $farm->id }}" @selected(old('farm_id', $transportJob->farm_id) == $farm->id)>{{ $farm->farm_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">คู่สัญญา</label>
        <select name="vendor_id" id="vendor_id" class="form-select" required>
            <option value="">เลือกคู่สัญญา</option>
            @foreach($vendors as $vendor)
                <option value="{{ $vendor->id }}" @selected(old('vendor_id', $transportJob->vendor_id) == $vendor->id)>{{ $vendor->vendor_name }}</option>
            @endforeach
        </select>
        <div class="form-text">เมื่อเลือกฟาร์ม ระบบจะโหลดคู่สัญญาที่ผูกกับฟาร์มนั้นให้อัตโนมัติ</div>
    </div>
    <div class="col-md-4">
        <label class="form-label">จำนวนอาหาร (กิโลกรัม)</label>
        <input type="number" step="0.01" min="0" name="food_weight_kg" value="{{ old('food_weight_kg', $transportJob->food_weight_kg ?? 0) }}" class="form-control" required>
    </div>

    <input type="hidden" name="route_standard_id" id="route_standard_id" value="{{ old('route_standard_id', $transportJob->route_standard_id) }}">
    <div class="col-12">
        <div id="route_standard_alert" class="alert alert-info mb-0">เลือกฟาร์มและคู่สัญญาเพื่อโหลดมาตรฐานเส้นทางอัตโนมัติ</div>
    </div>

    <div class="col-md-3">
        <label class="form-label">ไมล์ต้น</label>
        <input type="number" step="0.01" min="0" name="odometer_start" id="odometer_start" value="{{ old('odometer_start', $transportJob->odometer_start ?? 0) }}" class="form-control" required>
        <div class="form-text" id="latest_mileage_hint">เลือกรถเพื่อดึงไมล์จากเที่ยวล่าสุด</div>
    </div>
    <div class="col-md-3">
        <label class="form-label">ไมล์ปลาย</label>
        <input type="number" step="0.01" min="0" name="odometer_end" id="odometer_end" value="{{ old('odometer_end', $transportJob->odometer_end ?? 0) }}" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label class="form-label readonly-label">ระยะทางจริง (กม.)</label>
        <input type="number" step="0.01" id="actual_distance_km" value="{{ old('actual_distance_km', $transportJob->actual_distance_km ?? 0) }}" class="form-control readonly-input" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label readonly-label">ระยะทางมาตรฐาน (กม.)</label>
        <input type="number" step="0.01" id="standard_distance_km" value="{{ old('standard_distance_km', $transportJob->standard_distance_km ?? 0) }}" class="form-control readonly-input" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label readonly-label">น้ำมันที่บริษัทกำหนด (ลิตร)</label>
        <input type="number" step="0.01" id="company_oil_liters" value="{{ old('company_oil_liters', $transportJob->company_oil_liters ?? 0) }}" class="form-control readonly-input" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label">ชดเชยน้ำมัน (ลิตร)</label>
        <input type="number" step="0.01" min="0" name="oil_compensation_liters" id="oil_compensation_liters" value="{{ old('oil_compensation_liters', $transportJob->oil_compensation_liters ?? 0) }}" class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label">เหตุผลชดเชย</label>
        <select name="oil_compensation_reason_id" id="oil_compensation_reason_id" class="form-select">
            <option value="">เลือกเหตุผล</option>
            @foreach($oilCompensationReasons as $reason)
                <option value="{{ $reason->id }}" data-requires-details="{{ $reason->requires_details ? 1 : 0 }}" @selected((string) $selectedReasonId === (string) $reason->id)>
                    {{ $reason->reason_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label readonly-label">น้ำมันอนุมัติรวม (ลิตร)</label>
        <input type="number" step="0.01" id="approved_oil_liters" value="{{ old('approved_oil_liters', $transportJob->approved_oil_liters ?? 0) }}" class="form-control readonly-input" readonly>
    </div>

    <div class="col-12">
        <label class="form-label">รายละเอียดชดเชย</label>
        <textarea name="oil_compensation_details" id="oil_compensation_details" rows="2" class="form-control">{{ old('oil_compensation_details', $transportJob->oil_compensation_details) }}</textarea>
    </div>

    <div class="col-md-3">
        <label class="form-label">น้ำมันเติมจริง (ลิตร)</label>
        <input type="number" step="0.01" min="0" name="actual_oil_liters" id="actual_oil_liters" value="{{ old('actual_oil_liters', $transportJob->actual_oil_liters ?? 0) }}" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">ราคา/ลิตร</label>
        <input type="number" step="0.01" min="0" name="oil_price_per_liter" id="oil_price_per_liter" value="{{ old('oil_price_per_liter', $transportJob->oil_price_per_liter ?? 0) }}" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label class="form-label readonly-label">ค่าน้ำมัน</label>
        <input type="number" step="0.01" id="total_oil_cost" value="{{ old('total_oil_cost', $transportJob->total_oil_cost ?? 0) }}" class="form-control readonly-input" readonly>
    </div>
    <div class="col-md-3">
        <label class="form-label readonly-label">อัตราเฉลี่ยน้ำมัน (กม./ลิตร)</label>
        <input type="number" step="0.01" id="average_fuel_rate_km_per_liter" value="{{ old('average_fuel_rate_km_per_liter', $transportJob->average_fuel_rate_km_per_liter ?? 0) }}" class="form-control readonly-input" readonly>
    </div>

    <div class="col-md-6">
        <label class="form-label readonly-label">ส่วนต่างน้ำมัน (ลิตร)</label>
        <input type="number" step="0.01" id="oil_difference_liters" value="{{ old('oil_difference_liters', $transportJob->oil_difference_liters ?? 0) }}" class="form-control readonly-input" readonly>
        <div class="form-text">ค่าบวก = ใช้น้ำมันเกิน, ศูนย์ = พอดี, ค่าลบ = ใช้น้ำมันต่ำกว่าที่อนุมัติ</div>
    </div>
    <div class="col-md-6">
        <label class="form-label readonly-label">ส่วนต่างน้ำมัน (บาท)</label>
        <input type="number" step="0.01" id="oil_difference_amount" value="{{ old('oil_difference_amount', $transportJob->oil_difference_amount ?? 0) }}" class="form-control readonly-input" readonly>
        <div class="form-text">คำนวณจาก ส่วนต่างน้ำมัน (ลิตร) x ราคา/ลิตร</div>
    </div>
    <div class="col-md-6">
        <label class="form-label readonly-label">ส่วนต่างระยะทาง</label>
        <input type="number" step="0.01" id="distance_difference_km" value="{{ old('distance_difference_km', $transportJob->distance_difference_km ?? 0) }}" class="form-control readonly-input" readonly>
    </div>

    <div class="col-12">
        <label class="form-label">หมายเหตุ</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $transportJob->notes) }}</textarea>
    </div>
</div>

@push('scripts')
<script>
const vehicleField = document.getElementById('vehicle_id');
const driverField = document.getElementById('driver_id');
const farmField = document.getElementById('farm_id');
const vendorField = document.getElementById('vendor_id');
const routeStandardIdField = document.getElementById('route_standard_id');
const companyOilField = document.getElementById('company_oil_liters');
const standardDistanceField = document.getElementById('standard_distance_km');
const compensationField = document.getElementById('oil_compensation_liters');
const approvedOilField = document.getElementById('approved_oil_liters');
const odometerStartField = document.getElementById('odometer_start');
const odometerEndField = document.getElementById('odometer_end');
const latestMileageHint = document.getElementById('latest_mileage_hint');
const actualDistanceField = document.getElementById('actual_distance_km');
const actualOilField = document.getElementById('actual_oil_liters');
const oilPriceField = document.getElementById('oil_price_per_liter');
const totalOilCostField = document.getElementById('total_oil_cost');
const averageFuelRateField = document.getElementById('average_fuel_rate_km_per_liter');
const oilDifferenceField = document.getElementById('oil_difference_liters');
const oilDifferenceAmountField = document.getElementById('oil_difference_amount');
const distanceDifferenceField = document.getElementById('distance_difference_km');
const reasonField = document.getElementById('oil_compensation_reason_id');
const detailsField = document.getElementById('oil_compensation_details');
const alertBox = document.getElementById('route_standard_alert');
const transportDateField = document.getElementById('transport_date');
const documentNoField = document.getElementById('document_no_preview');
const isEditMode = {{ $transportJob->exists ? 'true' : 'false' }};
const vendorPlaceholderLabel = 'เลือกคู่สัญญา';
const vendorLoadingLabel = 'กำลังโหลดคู่สัญญา...';
const vendorEmptyMessage = 'ไม่พบคู่สัญญาที่ใช้งานได้สำหรับฟาร์มนี้';
const vendorFetchErrorMessage = 'ไม่สามารถโหลดคู่สัญญาตามฟาร์มได้';
const defaultRouteMessage = 'เลือกฟาร์มและคู่สัญญาเพื่อโหลดมาตรฐานเส้นทางอัตโนมัติ';

function syncDriverWithVehicle(force = false) {
    const selectedVehicle = vehicleField.options[vehicleField.selectedIndex];
    if (!selectedVehicle) return;

    const driverId = selectedVehicle.dataset.primaryDriverId || '';
    if (!driverId) return;

    if (force || !driverField.value) {
        driverField.value = driverId;
    }
}

const num = (value) => parseFloat(value || 0) || 0;

function renderVendorOptions(vendors, selectedVendorId = '') {
    vendorField.innerHTML = '';
    vendorField.add(new Option(vendorPlaceholderLabel, ''));

    vendors.forEach((vendor) => {
        const option = new Option(vendor.vendor_name, String(vendor.id));
        if (String(selectedVendorId) === String(vendor.id)) {
            option.selected = true;
        }
        vendorField.add(option);
    });
}

function resetRouteStandardFields(message, alertClass = 'alert alert-info mb-0') {
    routeStandardIdField.value = '';
    companyOilField.value = '0.00';
    standardDistanceField.value = '0.00';
    alertBox.className = alertClass;
    alertBox.textContent = message;
    recalculate();
}

function toggleReasonRequirement() {
    const selected = reasonField.options[reasonField.selectedIndex];
    const compensation = num(compensationField.value);
    const needsDetails = selected && selected.dataset.requiresDetails === '1';

    reasonField.required = compensation > 0;
    detailsField.required = compensation > 0 && needsDetails;
}

function recalculate() {
    const actualDistance = Math.max(num(odometerEndField.value) - num(odometerStartField.value), 0);
    const approvedOil = num(companyOilField.value) + num(compensationField.value);
    const actualOil = num(actualOilField.value);
    const oilPrice = num(oilPriceField.value);
    const totalOilCost = actualOil * oilPrice;
    const oilDifference = actualOil - approvedOil;
    const oilDifferenceAmount = oilDifference * oilPrice;
    const distanceDifference = actualDistance - num(standardDistanceField.value);
    const averageFuelRate = actualOil > 0 ? actualDistance / actualOil : 0;

    actualDistanceField.value = actualDistance.toFixed(2);
    approvedOilField.value = approvedOil.toFixed(2);
    totalOilCostField.value = totalOilCost.toFixed(2);
    oilDifferenceField.value = oilDifference.toFixed(2);
    oilDifferenceAmountField.value = oilDifferenceAmount.toFixed(2);
    distanceDifferenceField.value = distanceDifference.toFixed(2);
    averageFuelRateField.value = averageFuelRate.toFixed(2);

    toggleReasonRequirement();
}

async function fetchFarmVendors(preserveSelected = true) {
    const selectedVendorId = preserveSelected ? vendorField.value : '';

    if (!farmField.value) {
        vendorField.disabled = false;
        renderVendorOptions([], '');
        resetRouteStandardFields(defaultRouteMessage);
        return;
    }

    vendorField.disabled = true;
    vendorField.innerHTML = '';
    vendorField.add(new Option(vendorLoadingLabel, ''));

    try {
        const response = await fetch(`{{ route('lookup.farm-vendors') }}?farm_id=${farmField.value}`);
        const data = await response.json();
        const vendors = Array.isArray(data.vendors) ? data.vendors : [];

        renderVendorOptions(vendors, selectedVendorId);
        vendorField.disabled = false;

        if (!vendorField.value && vendors.length === 1) {
            vendorField.value = String(vendors[0].id);
        }

        if (vendors.length === 0) {
            resetRouteStandardFields(vendorEmptyMessage, 'alert alert-warning mb-0');
            return;
        }

        await fetchRouteStandard();
    } catch (error) {
        vendorField.disabled = false;
        renderVendorOptions([], '');
        resetRouteStandardFields(vendorFetchErrorMessage, 'alert alert-danger mb-0');
    }
}

async function fetchLatestVehicleMileage(force = false) {
    if (!vehicleField.value) {
        latestMileageHint.textContent = 'เลือกรถเพื่อดึงไมล์จากเที่ยวล่าสุด';
        return;
    }

    try {
        const response = await fetch(`{{ route('lookup.latest-vehicle-mileage') }}?vehicle_id=${vehicleField.value}`);
        const data = await response.json();

        if (!response.ok) {
            if (!isEditMode && (force || num(odometerStartField.value) === 0)) {
                odometerStartField.value = '0.00';
            }
            latestMileageHint.textContent = data.message || 'ยังไม่พบประวัติเที่ยวล่าสุดของรถคันนี้';
            recalculate();
            return;
        }

        if (force || (!isEditMode && num(odometerStartField.value) === 0)) {
            odometerStartField.value = Number(data.odometer_start || 0).toFixed(2);
            recalculate();
        }

        const dateLabel = data.latest_transport_date || '-';
        const docLabel = data.latest_document_no || '-';
        latestMileageHint.textContent = `ดึงไมล์ต้นจากเที่ยวล่าสุด: ${Number(data.odometer_start || 0).toFixed(2)} | เอกสาร ${docLabel} | วันที่ ${dateLabel}`;
    } catch (error) {
        latestMileageHint.textContent = 'ไม่สามารถโหลดไมล์เที่ยวล่าสุดได้';
    }
}

async function fetchRouteStandard() {
    if (!farmField.value || !vendorField.value) {
        resetRouteStandardFields(defaultRouteMessage);
        return;
    }

    try {
        const response = await fetch(`{{ route('lookup.route-standard') }}?farm_id=${farmField.value}&vendor_id=${vendorField.value}`);
        const data = await response.json();

        if (!response.ok) {
            resetRouteStandardFields(data.message || 'ไม่พบมาตรฐานเส้นทางสำหรับฟาร์มและคู่สัญญาที่เลือก', 'alert alert-warning mb-0');
            return;
        }

        routeStandardIdField.value = data.id;
        companyOilField.value = Number(data.company_oil_liters).toFixed(2);
        standardDistanceField.value = Number(data.standard_distance_km).toFixed(2);
        alertBox.className = 'alert alert-success mb-0';
        alertBox.textContent = 'โหลดมาตรฐานเส้นทางเรียบร้อยแล้ว';
        recalculate();
    } catch (error) {
        resetRouteStandardFields('ไม่สามารถโหลดข้อมูลมาตรฐานเส้นทางได้', 'alert alert-danger mb-0');
    }
}

async function fetchDocumentNumber() {
    if (!transportDateField.value) return;

    try {
        const response = await fetch(`{{ route('lookup.document-number') }}?transport_date=${transportDateField.value}`);
        const data = await response.json();

        if (data.document_no) {
            documentNoField.value = data.document_no;
        }
    } catch (error) {
    }
}

vehicleField.addEventListener('change', async () => {
    syncDriverWithVehicle(false);
    await fetchLatestVehicleMileage(true);
});
farmField.addEventListener('change', () => fetchFarmVendors(false));
vendorField.addEventListener('change', fetchRouteStandard);
[odometerStartField, odometerEndField, compensationField, actualOilField, oilPriceField].forEach((field) => field.addEventListener('input', recalculate));
compensationField.addEventListener('input', toggleReasonRequirement);
reasonField.addEventListener('change', toggleReasonRequirement);
transportDateField.addEventListener('change', fetchDocumentNumber);

syncDriverWithVehicle(false);
fetchLatestVehicleMileage(false);
fetchFarmVendors(true);
fetchDocumentNumber();
recalculate();
</script>
@endpush
