<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>พิมพ์ QR บันทึกการใช้รถ {{ $vehicle->registration_number }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f3f5f7; color: #16212b; }
        .sheet { width: 100%; min-height: calc(297mm - 24mm); display: flex; align-items: center; justify-content: center; padding: 12mm 0; }
        .label { width: 148mm; min-height: 210mm; background: #fff; border: 1.5mm solid #16212b; border-radius: 4mm; padding: 10mm; display: flex; flex-direction: column; align-items: center; justify-content: space-between; text-align: center; }
        .eyebrow { font-size: 12pt; font-weight: 700; letter-spacing: .02em; }
        .plate { font-size: 28pt; font-weight: 700; margin: 4mm 0 2mm; }
        .vehicle-meta { font-size: 14pt; color: #4a5560; margin-bottom: 6mm; }
        .qr-wrap { width: 88mm; height: 88mm; padding: 4mm; border: 1mm solid #d6dbe1; border-radius: 4mm; display: flex; align-items: center; justify-content: center; background: #fff; }
        .qr-wrap img { width: 100%; height: 100%; }
        .cta { margin-top: 6mm; font-size: 18pt; font-weight: 700; }
        .url { margin-top: 4mm; word-break: break-all; font-size: 10pt; color: #5d6873; }
        .footer-note { font-size: 12pt; line-height: 1.5; color: #2a3440; }
        .screen-actions { position: fixed; right: 20px; bottom: 20px; display: flex; gap: 10px; }
        .screen-actions button, .screen-actions a { border: 0; background: #17324d; color: #fff; padding: 12px 18px; border-radius: 10px; text-decoration: none; font-size: 14px; cursor: pointer; }
        .screen-actions a { background: #4a5560; }
        @media print {
            body { background: #fff; }
            .sheet { padding: 0; min-height: auto; }
            .screen-actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <section class="label">
            <div>
                <div class="eyebrow">ระบบบันทึกการใช้รถ</div>
                <div class="plate">{{ $vehicle->registration_number }}</div>
                <div class="vehicle-meta">{{ $vehicle->vehicle_type ?: '-' }} / {{ $vehicle->brand }}{{ $vehicle->model ? ' / ' . $vehicle->model : '' }}</div>
            </div>

            <div>
                <div class="qr-wrap">
                    <img src="{{ route('vehicles.usage-qr-code', $vehicle) }}" alt="QR Code {{ $vehicle->registration_number }}">
                </div>
                <div class="cta">สแกนเพื่อบันทึกการใช้รถ</div>
                <div class="url">{{ $vehicle->usageLogQrUrl() }}</div>
            </div>

            <div class="footer-note">
                ใช้สำหรับเปิดแบบฟอร์มบันทึกการใช้รถของรถคันนี้โดยตรง
            </div>
        </section>
    </div>

    <div class="screen-actions">
        <button type="button" onclick="window.print()">พิมพ์ QR</button>
        <a href="{{ route('vehicles.usage-qr-page', $vehicle) }}">กลับ</a>
    </div>
</body>
</html>
