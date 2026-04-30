<table>
    <thead>
        <tr>
            <th colspan="24">รายละเอียดรายงานเที่ยวขนส่ง</th>
        </tr>
        <tr>
            <th>วันที่ขนส่ง</th>
            <th>เลขที่เอกสาร</th>
            <th>รถ</th>
            <th>พนักงานขับ</th>
            <th>ฟาร์ม</th>
            <th>คู่สัญญา</th>
            <th>จำนวนอาหาร (กก.)</th>
            <th>ไมล์ต้น</th>
            <th>ไมล์ปลาย</th>
            <th>ระยะทางจริง (กม.)</th>
            <th>ระยะทางมาตรฐาน (กม.)</th>
            <th>น้ำมันที่บริษัทกำหนด (ลิตร)</th>
            <th>ชดเชยน้ำมัน (ลิตร)</th>
            <th>เหตุผลชดเชย</th>
            <th>รายละเอียดชดเชย</th>
            <th>น้ำมันอนุมัติรวม (ลิตร)</th>
            <th>น้ำมันเติมจริง (ลิตร)</th>
            <th>ราคา/ลิตร</th>
            <th>ค่าน้ำมัน (บาท)</th>
            <th>ส่วนต่างน้ำมัน (ลิตร)</th>
            <th>ส่วนต่างน้ำมัน (บาท)</th>
            <th>ส่วนต่างระยะทาง (กม.)</th>
            <th>อัตราเฉลี่ยน้ำมัน (กม./ลิตร)</th>
            <th>หมายเหตุ</th>
        </tr>
    </thead>
    <tbody>
        @forelse($jobs as $job)
            <tr>
                <td>{{ $job->transport_date?->format('d/m/Y') }}</td>
                <td>{{ $job->document_no }}</td>
                <td>{{ trim(($job->vehicle?->registration_number ?? '').' '.($job->vehicle?->brand ?? '').' '.($job->vehicle?->model ?? '')) }}</td>
                <td>{{ trim(($job->driver?->full_name ?? '').($job->driver?->employee_code ? ' ('.$job->driver->employee_code.')' : '')) }}</td>
                <td>{{ $job->farm?->farm_name }}</td>
                <td>{{ $job->vendor?->vendor_name }}</td>
                <td>{{ number_format((float) $job->food_weight_kg, 2) }}</td>
                <td>{{ number_format((float) $job->odometer_start, 2) }}</td>
                <td>{{ number_format((float) $job->odometer_end, 2) }}</td>
                <td>{{ number_format((float) $job->actual_distance_km, 2) }}</td>
                <td>{{ number_format((float) $job->standard_distance_km, 2) }}</td>
                <td>{{ number_format((float) $job->company_oil_liters, 2) }}</td>
                <td>{{ number_format((float) $job->oil_compensation_liters, 2) }}</td>
                <td>{{ $job->oilCompensationReason?->reason_name ?: '-' }}</td>
                <td>{{ $job->oil_compensation_details ?: '-' }}</td>
                <td>{{ number_format((float) $job->approved_oil_liters, 2) }}</td>
                <td>{{ number_format((float) $job->actual_oil_liters, 2) }}</td>
                <td>{{ number_format((float) $job->oil_price_per_liter, 2) }}</td>
                <td>{{ number_format((float) $job->total_oil_cost, 2) }}</td>
                <td>{{ number_format((float) $job->oil_difference_liters, 2) }}</td>
                <td>{{ number_format((float) $job->oil_difference_amount, 2) }}</td>
                <td>{{ number_format((float) $job->distance_difference_km, 2) }}</td>
                <td>{{ number_format((float) $job->average_fuel_rate_km_per_liter, 2) }}</td>
                <td>{{ $job->notes ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="24">ไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</td>
            </tr>
        @endforelse
        <tr>
            <td colspan="6">สรุป</td>
            <td>{{ number_format($summary['total_food_weight_kg'], 2) }}</td>
            <td colspan="8"></td>
            <td>{{ number_format($summary['total_approved_oil_liters'], 2) }}</td>
            <td>{{ number_format($summary['total_actual_oil_liters'], 2) }}</td>
            <td></td>
            <td>{{ number_format($summary['total_oil_cost'], 2) }}</td>
            <td>{{ number_format($summary['total_oil_difference_liters'], 2) }}</td>
            <td>{{ number_format($summary['total_oil_difference_amount'], 2) }}</td>
            <td>{{ number_format($summary['total_distance_difference_km'], 2) }}</td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
