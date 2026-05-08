<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: 'ThaiPdf';
            src: url("{{ public_path('fonts/thsarabun.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'ThaiPdf';
            src: url("{{ public_path('fonts/thsarabun-bold.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @page {
            margin: 18px 22px;
        }

        body {
            font-family: 'ThaiPdf', sans-serif;
            font-size: 15px;
            color: #111827;
        }

        .header {
            border-bottom: 2px solid #17324d;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: 0;
            padding: 0;
            vertical-align: top;
        }

        .title {
            font-size: 26px;
            font-weight: bold;
            line-height: 1.1;
            margin-bottom: 4px;
        }

        .subtitle,
        .meta {
            color: #5b6472;
            font-size: 14px;
        }

        .meta-right {
            text-align: right;
        }

        .summary {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin: 6px 0 10px;
        }

        .summary td {
            width: 25%;
            border: 1px solid #d7dee8;
            border-radius: 10px;
            padding: 10px 12px;
            background: #f8fafc;
            vertical-align: top;
        }

        .summary-label {
            color: #667085;
            font-size: 14px;
        }

        .summary-value {
            margin-top: 4px;
            font-size: 24px;
            font-weight: bold;
            line-height: 1.1;
            color: #17324d;
        }

        .summary-note {
            margin-top: 2px;
            color: #7b8794;
            font-size: 13px;
        }

        .filter-box {
            margin: 6px 0 10px;
            padding: 10px 12px;
            border: 1px solid #d8e1ea;
            border-radius: 10px;
            background: #fbfdff;
        }

        .filter-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #17324d;
        }

        .filter-text {
            color: #5f6b78;
            font-size: 14px;
            line-height: 1.45;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.data th,
        table.data td {
            border: 1px solid #cfd8e3;
            padding: 6px 7px;
            vertical-align: top;
            word-wrap: break-word;
        }

        table.data th {
            background: #edf3f8;
            color: #243446;
            font-size: 14px;
            font-weight: bold;
            text-align: left;
        }

        table.data td {
            font-size: 14px;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #667085;
        }

        .total-row td {
            background: #f8fafc;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="title">รายงานการขนส่งอาหารไก่</div>
                    <div class="subtitle">สรุปเที่ยวขนส่ง ระยะทาง น้ำมัน และต้นทุนจากข้อมูลที่บันทึกในระบบ</div>
                </td>
                <td class="meta-right">
                    <div class="meta">ช่วงวันที่ {{ $filters['start_date'] ?? '-' }} ถึง {{ $filters['end_date'] ?? '-' }}</div>
                    <div class="meta">พิมพ์เมื่อ {{ ($generatedAt ?? now())->format('d/m/Y H:i') }} น.</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="summary-label">จำนวนเที่ยวขนส่ง</div>
                <div class="summary-value">{{ number_format($summary['total_jobs']) }}</div>
                <div class="summary-note">เที่ยว</div>
            </td>
            <td>
                <div class="summary-label">น้ำหนักอาหารรวม</div>
                <div class="summary-value">{{ number_format($summary['total_food_weight_kg'], 2) }}</div>
                <div class="summary-note">กิโลกรัม</div>
            </td>
            <td>
                <div class="summary-label">น้ำมันเติมจริงรวม</div>
                <div class="summary-value">{{ number_format($summary['total_actual_oil_liters'], 2) }}</div>
                <div class="summary-note">ลิตร</div>
            </td>
            <td>
                <div class="summary-label">ต้นทุนน้ำมันรวม</div>
                <div class="summary-value">{{ number_format($summary['total_oil_cost'], 2) }}</div>
                <div class="summary-note">บาท</div>
            </td>
        </tr>
    </table>

    <div class="filter-box">
        <div class="filter-title">เงื่อนไขรายงาน</div>
        <div class="filter-text">
            ทะเบียน: {{ filled($filters['vehicle_id'] ?? null) ? 'เลือกเฉพาะคันที่ต้องการ' : 'ทั้งหมด' }} |
            พนักงานขับ: {{ filled($filters['driver_id'] ?? null) ? 'เลือกเฉพาะคนที่ต้องการ' : 'ทั้งหมด' }} |
            ฟาร์ม: {{ filled($filters['farm_id'] ?? null) ? 'เลือกเฉพาะฟาร์มที่ต้องการ' : 'ทั้งหมด' }} |
            คู่สัญญา: {{ filled($filters['vendor_id'] ?? null) ? 'เลือกเฉพาะคู่สัญญาที่ต้องการ' : 'ทั้งหมด' }}
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 7%;">วันที่</th>
                <th style="width: 10%;">เลขที่เอกสาร</th>
                <th style="width: 8%;">ทะเบียน</th>
                <th style="width: 10%;">พนักงานขับ</th>
                <th style="width: 10%;">ฟาร์ม</th>
                <th style="width: 10%;">คู่สัญญา</th>
                <th class="text-end" style="width: 7%;">ระยะทางจริง</th>
                <th class="text-end" style="width: 7%;">น้ำมันเติมจริง</th>
                <th class="text-end" style="width: 7%;">น้ำมันอนุมัติ</th>
                <th class="text-end" style="width: 7%;">ส่วนต่างลิตร</th>
                <th class="text-end" style="width: 8%;">ส่วนต่างเงิน</th>
                <th class="text-end" style="width: 9%;">ค่าน้ำมัน</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jobs as $job)
                <tr>
                    <td>{{ $job->transport_date?->format('d/m/Y') }}</td>
                    <td>{{ $job->document_no ?: '-' }}</td>
                    <td>{{ $job->vehicle?->registration_number ?: '-' }}</td>
                    <td>{{ $job->driver?->full_name ?: '-' }}</td>
                    <td>{{ $job->farm?->farm_name ?: '-' }}</td>
                    <td>{{ $job->vendor?->vendor_name ?: '-' }}</td>
                    <td class="text-end">{{ number_format((float) $job->actual_distance_km, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $job->actual_oil_liters, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $job->approved_oil_liters, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $job->oil_difference_liters, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $job->oil_difference_amount, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $job->total_oil_cost, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center muted">ไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</td>
                </tr>
            @endforelse

            @if($jobs->isNotEmpty())
                <tr class="total-row">
                    <td colspan="7">สรุปรวม</td>
                    <td class="text-end">{{ number_format($summary['total_actual_oil_liters'], 2) }}</td>
                    <td class="text-end">{{ number_format($summary['total_approved_oil_liters'], 2) }}</td>
                    <td class="text-end">{{ number_format($summary['total_oil_difference_liters'], 2) }}</td>
                    <td class="text-end">{{ number_format($summary['total_oil_difference_amount'], 2) }}</td>
                    <td class="text-end">{{ number_format($summary['total_oil_cost'], 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
