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

        body {
            font-family: 'ThaiPdf', sans-serif;
            font-size: 15px;
            color: #111827;
        }

        h1 {
            font-size: 24px;
            margin: 0 0 4px;
        }

        .muted {
            color: #6b7280;
        }

        .header {
            border-bottom: 2px solid #17324d;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .summary td {
            border: 1px solid #d1d5db;
            padding: 8px;
            width: 25%;
            vertical-align: top;
        }

        .summary .label {
            color: #6b7280;
            font-size: 14px;
        }

        .summary .value {
            font-size: 22px;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th,
        table.data td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            vertical-align: top;
        }

        table.data th {
            background: #e9eef5;
            font-weight: bold;
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
        }

        .success {
            background: #198754;
        }

        .danger {
            background: #dc3545;
        }

        .failure-box {
            margin: 8px 0 12px;
            padding: 8px;
            border: 1px solid #d1d5db;
            background: #f8fafc;
        }

        .failure-item {
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>รายงานตรวจเช็กรถก่อนวิ่ง</h1>
        <div class="muted">
            ช่วงวันที่ {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
            ถึง {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
            | พิมพ์เมื่อ {{ $generatedAt->format('d/m/Y H:i') }}
        </div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">ตรวจทั้งหมด</div>
                <div class="value">{{ number_format($totalCount) }}</div>
            </td>
            <td>
                <div class="label">พร้อมวิ่ง</div>
                <div class="value">{{ number_format($readyCount) }}</div>
            </td>
            <td>
                <div class="label">ไม่พร้อมวิ่ง</div>
                <div class="value">{{ number_format($notReadyCount) }}</div>
            </td>
            <td>
                <div class="label">อัตราพร้อมวิ่ง</div>
                <div class="value">{{ number_format($readyPercent, 1) }}%</div>
            </td>
        </tr>
    </table>

    <div class="failure-box">
        <strong>หัวข้อที่ไม่ผ่านบ่อย</strong>
        @foreach($checkFailureStats->where('count', '>', 0)->take(5) as $stat)
            <div class="failure-item">{{ $stat['label'] }}: {{ number_format($stat['count']) }} รายการ</div>
        @endforeach
        @if($checkFailureStats->where('count', '>', 0)->isEmpty())
            <div class="muted">ไม่มีหัวข้อที่ไม่ผ่านในช่วงรายงานนี้</div>
        @endif
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 9%;">วันที่</th>
                <th style="width: 7%;">เวลา</th>
                <th style="width: 11%;">ทะเบียนรถ</th>
                <th style="width: 15%;">พนักงานขับ</th>
                <th style="width: 9%;" class="text-end">เลขไมล์</th>
                <th style="width: 12%;">ผู้ตรวจ</th>
                <th style="width: 11%;">สถานะ</th>
                <th>หมายเหตุ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inspections as $inspection)
                <tr>
                    <td>{{ $inspection->inspection_date?->format('d/m/Y') }}</td>
                    <td>{{ \Illuminate\Support\Str::of($inspection->inspection_time)->substr(0, 5) }}</td>
                    <td>{{ $inspection->vehicle?->registration_number ?: '-' }}</td>
                    <td>{{ $inspection->driver?->full_name ?? '-' }}</td>
                    <td class="text-end">{{ $inspection->odometer_km !== null ? number_format((float) $inspection->odometer_km, 2) : '-' }}</td>
                    <td>{{ $inspection->user?->name ?: '-' }}</td>
                    <td>
                        <span class="badge {{ $inspection->is_ready_to_drive ? 'success' : 'danger' }}">
                            {{ $inspection->is_ready_to_drive ? 'พร้อมวิ่ง' : 'ไม่พร้อมวิ่ง' }}
                        </span>
                    </td>
                    <td>{{ $inspection->overall_note ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">ไม่มีข้อมูลในช่วงรายงานนี้</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
