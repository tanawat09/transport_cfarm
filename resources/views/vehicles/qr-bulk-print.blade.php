<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>พิมพ์สติ๊กเกอร์ QR หลายคัน</title>
    <style>
        @page { size: A4 portrait; margin: 8mm; }
        * { box-sizing: border-box; }
        :root {
            --ink: #14273a;
            --muted: #667789;
            --line: #d6e0e8;
            --soft-line: #eef3f6;
            --panel: #f8fbfd;
            --brand: #123653;
            --brand-2: #17636d;
            --accent: #b98a32;
        }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            background: linear-gradient(180deg, #eef3f6 0%, #f7fafc 100%);
            color: var(--ink);
        }
        .sheet {
            width: 100%;
            max-width: 194mm;
            margin: 0 auto;
            padding: 8mm;
        }
        .page {
            min-height: 281mm;
            page-break-after: always;
            display: flex;
            flex-direction: column;
        }
        .page:last-child {
            page-break-after: auto;
        }
        .page-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 6mm;
            padding: 12px 16px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(18, 54, 83, 0.16);
            position: relative;
            overflow: hidden;
        }
        .page-title::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), rgba(255, 255, 255, 0.2), transparent);
        }
        .page-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }
        .page-brand img {
            width: 78px;
            height: auto;
            display: block;
            object-fit: contain;
            filter: drop-shadow(0 1px 0 rgba(255, 255, 255, 0.2));
        }
        .page-heading {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.2;
        }
        .page-subheading {
            margin-top: 4px;
            font-size: 11px;
            opacity: 0.82;
        }
        .page-chip {
            padding: 7px 12px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.24);
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }
        .labels {
            height: 244mm;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-template-rows: repeat(2, 119mm);
            gap: 6mm;
            align-content: center;
        }
        .label {
            height: 119mm;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 12px 26px rgba(18, 54, 83, 0.08);
            overflow: hidden;
            page-break-inside: avoid;
            break-inside: avoid;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .label::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, var(--accent), var(--brand-2));
            z-index: 2;
        }
        .label-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 9px 12px 9px 16px;
            background: linear-gradient(180deg, #fbfdff 0%, #f4f8fb 100%);
            border-bottom: 1px solid #e2e9ee;
        }
        .label-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }
        .label-brand img {
            width: 56px;
            height: auto;
            display: block;
            object-fit: contain;
        }
        .label-brand-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--brand);
            line-height: 1.2;
        }
        .label-brand-subtitle {
            font-size: 9px;
            color: #607283;
            margin-top: 2px;
            line-height: 1.3;
        }
        .label-chip {
            padding: 5px 8px;
            border-radius: 6px;
            background: #eef5f7;
            border: 1px solid #d7e5ea;
            color: var(--brand);
            font-size: 8px;
            font-weight: 800;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .label-body {
            flex: 1;
            padding: 11px 16px 14px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            position: relative;
            isolation: isolate;
        }
        .plate {
            font-size: 24px;
            font-weight: 900;
            line-height: 1.05;
            color: var(--ink);
        }
        .vehicle-kicker {
            margin-top: 4px;
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--accent);
        }
        .vehicle-meta {
            margin-top: 4px;
            padding: 4px 8px;
            border-radius: 6px;
            background: var(--panel);
            border: 1px solid var(--soft-line);
            font-size: 9px;
            line-height: 1.45;
            color: var(--muted);
            max-width: 88%;
        }
        .vehicle-meta strong {
            color: var(--brand);
        }
        .qr-wrap {
            width: 45mm;
            height: 45mm;
            margin-top: 8px;
            padding: 3.5mm;
            border: 2px solid #d3e0e8;
            border-radius: 8px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 18px rgba(18, 54, 83, 0.08);
        }
        .qr-wrap img {
            width: 100%;
            height: 100%;
            display: block;
        }
        .cta {
            margin-top: 7px;
            font-size: 14px;
            font-weight: 900;
            color: var(--brand);
            line-height: 1.2;
        }
        .hint {
            margin-top: 3px;
            font-size: 8px;
            line-height: 1.4;
            color: #718394;
            max-width: 86%;
        }
        .pin-box {
            width: 100%;
            margin-top: 7px;
            padding: 8px 10px 10px;
            border-radius: 8px;
            background: linear-gradient(135deg, #102f49 0%, var(--brand-2) 100%);
            color: #ffffff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.14);
        }
        .pin-label {
            font-size: 8px;
            opacity: 0.78;
            line-height: 1.3;
        }
        .pin-value {
            margin-top: 4px;
            font-size: 23px;
            font-weight: 900;
            letter-spacing: 1px;
            line-height: 1;
        }
        .screen-actions button,
        .screen-actions a {
            border: 0;
            background: var(--brand);
            color: #ffffff;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(16, 38, 58, 0.16);
        }
        .screen-actions {
            position: fixed;
            right: 20px;
            bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .screen-actions a {
            background: #5a6a7a;
        }
        @media print {
            body { background: #ffffff; }
            .sheet { padding: 0; max-width: none; }
            .label, .page-title { box-shadow: none; }
            .screen-actions { display: none; }
        }
    </style>
</head>
<body>
    @php
        $isUsage = $qrType === 'usage';
        $isTractorUsageInspection = $qrType === 'tractor_usage_inspection';
        $title = $isTractorUsageInspection
            ? 'สติ๊กเกอร์ QR ตรวจเช็กการใช้งานรถไถ'
            : ($isUsage ? 'สติ๊กเกอร์ QR บันทึกการใช้รถ' : 'สติ๊กเกอร์ QR ตรวจรถก่อนวิ่ง');
        $subtitle = 'พิมพ์ 4 ดวงต่อ A4 สำหรับแปะรถ';
        $cta = $isTractorUsageInspection ? 'สแกนเพื่อตรวจรถไถ' : ($isUsage ? 'สแกนเพื่อใช้รถ' : 'สแกนเพื่อตรวจรถ');
        $chip = $isTractorUsageInspection ? 'ชุดสติ๊กเกอร์ตรวจรถไถ' : ($isUsage ? 'ชุดสติ๊กเกอร์ใช้รถ' : 'ชุดสติ๊กเกอร์ตรวจรถ');
        $labelChip = $isTractorUsageInspection ? 'ตรวจเช็กการใช้งานรถไถ' : ($isUsage ? 'บันทึกการใช้รถ' : 'ตรวจรถก่อนวิ่ง');
    @endphp

    <main class="sheet">
        @foreach($vehicles->chunk(4) as $pageIndex => $vehicleChunk)
            <section class="page">
                <header class="page-title">
                    <div class="page-brand">
                        <img src="{{ asset('images/cfarm-logo.png') }}" alt="CFARM">
                        <div>
                            <div class="page-heading">{{ $title }}</div>
                            <div class="page-subheading">{{ $subtitle }}</div>
                        </div>
                    </div>
                    <div class="page-chip">{{ $chip }} ชุดที่ {{ $pageIndex + 1 }}</div>
                </header>

                <div class="labels">
                    @foreach($vehicleChunk as $vehicle)
                        @php
                            $token = match ($qrType) {
                                'usage' => $vehicle->issueQrToken(\App\Models\VehicleQrToken::TYPE_USAGE),
                                'tractor_usage_inspection' => $vehicle->issueQrToken(\App\Models\VehicleQrToken::TYPE_TRACTOR_USAGE_INSPECTION),
                                default => $vehicle->issueQrToken(\App\Models\VehicleQrToken::TYPE_INSPECTION),
                            };
                        @endphp
                        <article class="label">
                            <div class="label-top">
                                <div class="label-brand">
                                    <img src="{{ asset('images/cfarm-logo.png') }}" alt="CFARM">
                                    <div>
                                        <div class="label-brand-title">{{ $vehicle->registration_number }}</div>
                                        <div class="label-brand-subtitle">{{ $vehicle->vehicle_type ?: 'ไม่ระบุประเภทรถ' }}</div>
                                    </div>
                                </div>
                                <div class="label-chip">{{ $labelChip }}</div>
                            </div>

                            <div class="label-body">
                                <div class="plate">{{ $vehicle->registration_number }}</div>
                                <div class="vehicle-kicker">CFARM TRANSPORT QR</div>
                                <div class="vehicle-meta">
                                    <strong>ประเภทรถ:</strong> {{ $vehicle->vehicle_type ?: '-' }}<br>
                                    <strong>ยี่ห้อ / รุ่น:</strong> {{ $vehicle->brand ?: '-' }}{{ $vehicle->model ? ' / ' . $vehicle->model : '' }}
                                </div>

                                <div class="qr-wrap">
                                    @if($isTractorUsageInspection)
                                        <img src="{{ route('vehicles.tractor-usage-inspection-qr-code', $vehicle) }}" alt="QR Code {{ $vehicle->registration_number }}">
                                    @elseif($isUsage)
                                        <img src="{{ route('vehicles.usage-qr-code', $vehicle) }}" alt="QR Code {{ $vehicle->registration_number }}">
                                    @else
                                        <img src="{{ route('vehicles.inspection-qr-code', $vehicle) }}" alt="QR Code {{ $vehicle->registration_number }}">
                                    @endif
                                </div>

                                <div class="cta">{{ $cta }}</div>
                                <div class="hint">กรอก PIN ก่อนเริ่มบันทึกข้อมูลทุกครั้ง</div>

                                <div class="pin-box">
                                    <div class="pin-label">PIN สำหรับยืนยันตัวตน</div>
                                    <div class="pin-value">{{ $token->decryptedPin() }}</div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </main>

    <div class="screen-actions">
        <button type="button" onclick="window.print()">พิมพ์สติ๊กเกอร์ทั้งหมด</button>
        <a href="{{ route('vehicles.index') }}">กลับ</a>
    </div>
</body>
</html>
