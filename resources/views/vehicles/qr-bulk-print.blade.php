<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>พิมพ์ QR หลายคัน</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #eef2f6;
            color: #16212b;
        }

        .sheet {
            width: 100%;
            padding: 10mm;
        }

        .labels {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8mm;
        }

        .label {
            min-height: 125mm;
            background: #fff;
            border: 1mm solid #16212b;
            border-radius: 4mm;
            padding: 8mm;
            text-align: center;
            page-break-inside: avoid;
            break-inside: avoid;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .eyebrow {
            font-size: 11pt;
            font-weight: 700;
        }

        .plate {
            font-size: 22pt;
            font-weight: 700;
            margin: 2mm 0 1mm;
        }

        .vehicle-meta {
            min-height: 12mm;
            font-size: 10pt;
            color: #4a5560;
        }

        .qr-wrap {
            width: 58mm;
            height: 58mm;
            margin: 4mm auto;
            padding: 3mm;
            border: .7mm solid #d6dbe1;
            border-radius: 3mm;
            background: #fff;
        }

        .qr-wrap img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .cta {
            font-size: 13pt;
            font-weight: 700;
        }

        .url {
            margin-top: 2mm;
            word-break: break-all;
            font-size: 7pt;
            color: #5d6873;
        }

        .screen-actions {
            position: fixed;
            right: 20px;
            bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .screen-actions button,
        .screen-actions a {
            border: 0;
            background: #17324d;
            color: #fff;
            padding: 12px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .screen-actions a {
            background: #4a5560;
        }

        @media print {
            body {
                background: #fff;
            }

            .sheet {
                padding: 0;
            }

            .screen-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    @php
        $isUsage = $qrType === 'usage';
        $title = $isUsage ? 'ระบบบันทึกการใช้รถ' : 'ระบบตรวจเช็กรถก่อนวิ่ง';
        $cta = $isUsage ? 'สแกนเพื่อบันทึกการใช้รถ' : 'สแกนเพื่อตรวจรถคันนี้';
    @endphp

    <main class="sheet">
        <div class="labels">
            @foreach($vehicles as $vehicle)
                <section class="label">
                    <div>
                        <div class="eyebrow">{{ $title }}</div>
                        <div class="plate">{{ $vehicle->registration_number }}</div>
                        <div class="vehicle-meta">
                            {{ $vehicle->vehicle_type ?: '-' }}
                            @if($vehicle->brand)
                                / {{ $vehicle->brand }}{{ $vehicle->model ? ' / ' . $vehicle->model : '' }}
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="qr-wrap">
                            @if($isUsage)
                                <img src="{{ route('vehicles.usage-qr-code', $vehicle) }}" alt="QR Code {{ $vehicle->registration_number }}">
                            @else
                                <img src="{{ route('vehicles.inspection-qr-code', $vehicle) }}" alt="QR Code {{ $vehicle->registration_number }}">
                            @endif
                        </div>
                        <div class="cta">{{ $cta }}</div>
                        <div class="url">{{ $isUsage ? $vehicle->usageLogQrUrl() : $vehicle->inspectionQrUrl() }}</div>
                    </div>
                </section>
            @endforeach
        </div>
    </main>

    <div class="screen-actions">
        <button type="button" onclick="window.print()">พิมพ์ QR ทั้งหมด</button>
        <a href="{{ route('vehicles.index') }}">กลับ</a>
    </div>
</body>
</html>
