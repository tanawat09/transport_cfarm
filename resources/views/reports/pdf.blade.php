<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 6px; }
        th { background: #e9eef5; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <h2>รายงานเที่ยวขนส่ง</h2>
    <p>ช่วงวันที่: {{ $filters['start_date'] ?? '-' }} ถึง {{ $filters['end_date'] ?? '-' }}</p>
    <table>
        <thead>
            <tr>
                <th>วันที่</th>
                <th>เลขที่เอกสาร</th>
                <th>รถ</th>
                <th>พนักงานขับ</th>
                <th>ฟาร์ม</th>
                <th>คู่สัญญา</th>
                <th class="text-end">ระยะทางจริง</th>
                <th class="text-end">น้ำมันจริง</th>
                <th class="text-end">น้ำมันอนุมัติ</th>
                <th class="text-end">ส่วนต่างน้ำมัน (ลิตร)</th>
                <th class="text-end">ส่วนต่างน้ำมัน (บาท)</th>
                <th class="text-end">ค่าน้ำมัน</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobs as $job)
                <tr>
                    <td>{{ $job->transport_date?->format('d/m/Y') }}</td>
                    <td>{{ $job->document_no }}</td>
                    <td>{{ $job->vehicle?->registration_number }}</td>
                    <td>{{ $job->driver?->full_name }}</td>
                    <td>{{ $job->farm?->farm_name }}</td>
                    <td>{{ $job->vendor?->vendor_name }}</td>
                    <td class="text-end">{{ number_format($job->actual_distance_km, 2) }}</td>
                    <td class="text-end">{{ number_format($job->actual_oil_liters, 2) }}</td>
                    <td class="text-end">{{ number_format($job->approved_oil_liters, 2) }}</td>
                    <td class="text-end">{{ number_format($job->oil_difference_liters, 2) }}</td>
                    <td class="text-end">{{ number_format($job->oil_difference_amount, 2) }}</td>
                    <td class="text-end">{{ number_format($job->total_oil_cost, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="8"><strong>สรุป</strong></td>
                <td class="text-end"><strong>{{ number_format($summary['total_approved_oil_liters'], 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($summary['total_oil_difference_liters'], 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($summary['total_oil_difference_amount'], 2) }}</strong></td>
                <td class="text-end"><strong>{{ number_format($summary['total_oil_cost'], 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
