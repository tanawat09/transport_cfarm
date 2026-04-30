@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card"><div class="card-body">
            <h2 class="h5 mb-3">ข้อมูลเอกสาร</h2>
            <table class="table table-sm mb-0">
                <tr><th>วันที่ขนส่ง</th><td>{{ $transportJob->transport_date?->format('d/m/Y') }}</td></tr>
                <tr><th>เลขที่เอกสาร</th><td>{{ $transportJob->document_no }}</td></tr>
                <tr><th>รถ</th><td>{{ $transportJob->vehicle?->registration_number }} - {{ $transportJob->vehicle?->brand }}</td></tr>
                <tr><th>พนักงานขับ</th><td>{{ $transportJob->driver?->full_name }}</td></tr>
                <tr><th>ฟาร์ม</th><td>{{ $transportJob->farm?->farm_name }}</td></tr>
                <tr><th>คู่สัญญา</th><td>{{ $transportJob->vendor?->vendor_name }}</td></tr>
                <tr><th>จำนวนอาหาร</th><td>{{ number_format($transportJob->food_weight_kg, 2) }} กก.</td></tr>
                <tr><th>ไมล์ต้น</th><td>{{ number_format($transportJob->odometer_start, 2) }}</td></tr>
                <tr><th>ไมล์ปลาย</th><td>{{ number_format($transportJob->odometer_end, 2) }}</td></tr>
            </table>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card"><div class="card-body">
            <h2 class="h5 mb-3">ผลคำนวณ</h2>
            <table class="table table-sm mb-0">
                <tr><th>ระยะทางจริง</th><td>{{ number_format($transportJob->actual_distance_km, 2) }} กม.</td></tr>
                <tr><th>ระยะทางมาตรฐาน</th><td>{{ number_format($transportJob->standard_distance_km, 2) }} กม.</td></tr>
                <tr><th>น้ำมันที่บริษัทกำหนด</th><td>{{ number_format($transportJob->company_oil_liters, 2) }} ลิตร</td></tr>
                <tr><th>ชดเชยน้ำมัน</th><td>{{ number_format($transportJob->oil_compensation_liters, 2) }} ลิตร</td></tr>
                <tr><th>น้ำมันอนุมัติรวม</th><td>{{ number_format($transportJob->approved_oil_liters, 2) }} ลิตร</td></tr>
                <tr><th>น้ำมันเติมจริง</th><td>{{ number_format($transportJob->actual_oil_liters, 2) }} ลิตร</td></tr>
                <tr><th>ราคา/ลิตร</th><td>{{ number_format($transportJob->oil_price_per_liter, 2) }} บาท</td></tr>
                <tr><th>ค่าน้ำมัน</th><td>{{ number_format($transportJob->total_oil_cost, 2) }} บาท</td></tr>
                <tr><th>ส่วนต่างน้ำมัน</th><td>{{ number_format($transportJob->oil_difference_liters, 2) }} ลิตร</td></tr>
                <tr><th>ส่วนต่างน้ำมันเป็นเงิน</th><td>{{ number_format($transportJob->oil_difference_amount, 2) }} บาท</td></tr>
                <tr><th>ส่วนต่างระยะทาง</th><td>{{ number_format($transportJob->distance_difference_km, 2) }} กม.</td></tr>
                <tr><th>อัตราเฉลี่ยน้ำมัน</th><td>{{ number_format($transportJob->average_fuel_rate_km_per_liter, 2) }} กม./ลิตร</td></tr>
            </table>
        </div></div>
    </div>
    <div class="col-12">
        <div class="card"><div class="card-body">
            <h2 class="h5 mb-3">รายละเอียดเพิ่มเติม</h2>
            <p class="mb-2"><strong>เหตุผลชดเชย:</strong> {{ $transportJob->oilCompensationReason?->reason_name ?? '-' }}</p>
            <p class="mb-2"><strong>รายละเอียดชดเชย:</strong> {{ $transportJob->oil_compensation_details ?: '-' }}</p>
            <p class="mb-0"><strong>หมายเหตุ:</strong> {{ $transportJob->notes ?: '-' }}</p>
        </div></div>
    </div>
</div>
@endsection
