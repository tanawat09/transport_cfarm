<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>พิมพ์สติ๊กเกอร์ QR ตรวจเช็กรถไถ {{ $vehicle->registration_number }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            background: #edf3f6;
            color: #17324d;
        }
        .sheet {
            min-height: calc(297mm - 24mm);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12mm 0;
        }
        .label {
            width: 165mm;
            min-height: 235mm;
            background: #ffffff;
            border: 1px solid #d7e1e8;
            border-radius: 22px;
            box-shadow: 0 18px 50px rgba(23, 50, 77, 0.12);
            overflow: hidden;
        }
        .label-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 24px;
            background: linear-gradient(135deg, #17324d 0%, #1f6f78 100%);
            color: #ffffff;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }
        .brand img {
            width: 110px;
            max-width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
        }
        .brand-title {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.2;
        }
        .brand-subtitle {
            font-size: 12px;
            opacity: 0.86;
            margin-top: 4px;
        }
        .header-chip {
            padding: 8px 14px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }
        .label-body { padding: 24px; }
        .hero {
            text-align: center;
            margin-bottom: 20px;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 14px;
            border-radius: 999px;
            background: #e7f5f7;
            color: #1f6f78;
            font-size: 13px;
            font-weight: 700;
        }
        .plate {
            margin: 14px 0 6px;
            font-size: 36px;
            font-weight: 800;
            color: #12263a;
        }
        .vehicle-meta {
            font-size: 16px;
            color: #526273;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 18px;
            align-items: stretch;
        }
        .panel {
            border: 1px solid #dce5eb;
            border-radius: 18px;
            background: #f9fbfc;
            padding: 18px;
        }
        .panel-title {
            font-size: 15px;
            font-weight: 800;
            color: #17324d;
            margin-bottom: 14px;
        }
        .meta-list { display: grid; gap: 12px; }
        .meta-row {
            display: grid;
            grid-template-columns: 110px 1fr;
            gap: 10px;
            align-items: start;
            font-size: 14px;
        }
        .meta-label {
            color: #607080;
            font-weight: 600;
        }
        .meta-value {
            color: #15293c;
            font-weight: 700;
            word-break: break-word;
        }
        .pin-box {
            margin-top: 16px;
            padding: 14px;
            border-radius: 16px;
            background: #17324d;
            color: #ffffff;
            text-align: center;
        }
        .pin-label {
            font-size: 12px;
            opacity: 0.84;
        }
        .pin-value {
            margin-top: 4px;
            font-size: 30px;
            font-weight: 800;
            letter-spacing: 2px;
        }
        .qr-panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: linear-gradient(180deg, #ffffff 0%, #f5f9fb 100%);
        }
        .qr-wrap {
            width: 82mm;
            height: 82mm;
            padding: 5mm;
            border: 1px solid #d5dee6;
            border-radius: 18px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-wrap img {
            width: 100%;
            height: 100%;
            display: block;
        }
        .cta {
            margin-top: 16px;
            font-size: 22px;
            font-weight: 800;
            color: #12263a;
        }
        .hint {
            margin-top: 8px;
            font-size: 13px;
            line-height: 1.6;
            color: #5b6a79;
        }
        .footer-note {
            margin-top: 18px;
            padding: 14px 16px;
            border-top: 1px dashed #d8e2e9;
            font-size: 12px;
            line-height: 1.7;
            color: #536474;
            text-align: center;
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
            border-radius: 12px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }
        .screen-actions a { background: #5a6a7a; }
        @media print {
            body { background: #ffffff; }
            .sheet { min-height: auto; padding: 0; }
            .label { box-shadow: none; }
            .screen-actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <section class="label">
            <header class="label-header">
                <div class="brand">
                    <img src="{{ asset('images/cfarm-logo.png') }}" alt="CFARM">
                    <div>
                        <div class="brand-title">CFARM Transport</div>
                        <div class="brand-subtitle">สติ๊กเกอร์ QR ตรวจเช็กการใช้งานรถไถ</div>
                    </div>
                </div>
                <div class="header-chip">รถไถ คูโบต้า</div>
            </header>

            <div class="label-body">
                <div class="hero">
                    <div class="eyebrow">สแกนเพื่อตรวจเช็กการใช้งานรถไถ</div>
                    <div class="plate">{{ $vehicle->registration_number }}</div>
                    <div class="vehicle-meta">
                        {{ $vehicle->vehicle_type ?: 'ไม่ระบุประเภทรถ' }}
                        @if($vehicle->brand)
                            / {{ $vehicle->brand }}{{ $vehicle->model ? ' / ' . $vehicle->model : '' }}
                        @endif
                    </div>
                </div>

                <div class="content-grid">
                    <div class="panel">
                        <div class="panel-title">ข้อมูลรถไถ</div>
                        <div class="meta-list">
                            <div class="meta-row">
                                <div class="meta-label">ทะเบียนรถ</div>
                                <div class="meta-value">{{ $vehicle->registration_number }}</div>
                            </div>
                            <div class="meta-row">
                                <div class="meta-label">ประเภทรถ</div>
                                <div class="meta-value">{{ $vehicle->vehicle_type ?: '-' }}</div>
                            </div>
                            <div class="meta-row">
                                <div class="meta-label">ยี่ห้อ / รุ่น</div>
                                <div class="meta-value">{{ $vehicle->brand ?: '-' }}{{ $vehicle->model ? ' / ' . $vehicle->model : '' }}</div>
                            </div>
                        </div>

                        <div class="pin-box">
                            <div class="pin-label">PIN สำหรับยืนยันตัวตน</div>
                            <div class="pin-value">{{ $qrToken->decryptedPin() }}</div>
                        </div>
                    </div>

                    <div class="panel qr-panel">
                        <div class="qr-wrap">
                            <img src="{{ route('vehicles.tractor-usage-inspection-qr-code', $vehicle) }}" alt="QR Code {{ $vehicle->registration_number }}">
                        </div>
                        <div class="cta">สแกนเพื่อตรวจรถไถ</div>
                        <div class="hint">กรอก PIN ก่อนเริ่มบันทึกข้อมูลทุกครั้ง</div>
                    </div>
                </div>

                <div class="footer-note">
                    QR นี้ใช้สำหรับแบบฟอร์มตรวจเช็กการใช้งานรถไถคูโบต้าคันนี้เท่านั้น
                </div>
            </div>
        </section>
    </div>

    <div class="screen-actions">
        <button type="button" onclick="window.print()">พิมพ์สติ๊กเกอร์</button>
        <a href="{{ route('vehicles.tractor-usage-inspection-qr-page', $vehicle) }}">กลับ</a>
    </div>
</body>
</html>
