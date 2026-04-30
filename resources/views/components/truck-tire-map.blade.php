@props([
    'tireStatuses' => [],
    'vehicleType' => null,
])

@php
    $validStatuses = ['normal', 'warning', 'replace', 'repair', 'empty'];
    $statusClass = function (string $tireId) use ($tireStatuses, $validStatuses): string {
        $status = $tireStatuses[$tireId] ?? 'empty';

        return in_array($status, $validStatuses, true) ? $status : 'empty';
    };

    $vehicleTypeText = (string) $vehicleType;
    $isSemiTrailer = str_contains($vehicleTypeText, 'รถกึ่งพ่วงบรรทุกอาหารสัตว์');
    $isTractor = str_contains($vehicleTypeText, 'ลากจูง');

    if ($isSemiTrailer) {
        $viewBox = '0 0 300 190';
        $svgLabel = 'แผนผังยางรถกึ่งพ่วงบรรทุกอาหารสัตว์ 12 ตำแหน่ง';
        $svgMinWidth = '460px';
        $driveLine = ['x1' => 20, 'y1' => 95, 'x2' => 280, 'y2' => 95];

        $tires = [
            ['id' => 'T11', 'x' => 18, 'y' => 24, 'w' => 56, 'h' => 20],
            ['id' => 'T12', 'x' => 18, 'y' => 48, 'w' => 56, 'h' => 20],
            ['id' => 'T13', 'x' => 18, 'y' => 122, 'w' => 56, 'h' => 20],
            ['id' => 'T14', 'x' => 18, 'y' => 146, 'w' => 56, 'h' => 20],
            ['id' => 'T15', 'x' => 122, 'y' => 24, 'w' => 56, 'h' => 20],
            ['id' => 'T16', 'x' => 122, 'y' => 48, 'w' => 56, 'h' => 20],
            ['id' => 'T17', 'x' => 122, 'y' => 122, 'w' => 56, 'h' => 20],
            ['id' => 'T18', 'x' => 122, 'y' => 146, 'w' => 56, 'h' => 20],
            ['id' => 'T19', 'x' => 226, 'y' => 24, 'w' => 56, 'h' => 20],
            ['id' => 'T20', 'x' => 226, 'y' => 48, 'w' => 56, 'h' => 20],
            ['id' => 'T21', 'x' => 226, 'y' => 122, 'w' => 56, 'h' => 20],
            ['id' => 'T22', 'x' => 226, 'y' => 146, 'w' => 56, 'h' => 20],
        ];

        $axles = [
            ['x' => 46, 'y1' => 44, 'y2' => 146, 'hub' => 95],
            ['x' => 150, 'y1' => 44, 'y2' => 146, 'hub' => 95],
            ['x' => 254, 'y1' => 44, 'y2' => 146, 'hub' => 95],
        ];
    } elseif ($isTractor) {
        $viewBox = '0 0 360 190';
        $svgLabel = 'แผนผังยางรถลากจูง 10 ล้อ';
        $svgMinWidth = '520px';
        $driveLine = ['x1' => 20, 'y1' => 95, 'x2' => 340, 'y2' => 95];

        $tires = [
            ['id' => 'T01', 'x' => 22, 'y' => 42, 'w' => 58, 'h' => 20],
            ['id' => 'T02', 'x' => 22, 'y' => 128, 'w' => 58, 'h' => 20],
            ['id' => 'T03', 'x' => 138, 'y' => 24, 'w' => 58, 'h' => 20],
            ['id' => 'T04', 'x' => 138, 'y' => 48, 'w' => 58, 'h' => 20],
            ['id' => 'T05', 'x' => 138, 'y' => 122, 'w' => 58, 'h' => 20],
            ['id' => 'T06', 'x' => 138, 'y' => 146, 'w' => 58, 'h' => 20],
            ['id' => 'T07', 'x' => 242, 'y' => 24, 'w' => 58, 'h' => 20],
            ['id' => 'T08', 'x' => 242, 'y' => 48, 'w' => 58, 'h' => 20],
            ['id' => 'T09', 'x' => 242, 'y' => 122, 'w' => 58, 'h' => 20],
            ['id' => 'T10', 'x' => 242, 'y' => 146, 'w' => 58, 'h' => 20],
        ];

        $axles = [
            ['x' => 51, 'y1' => 62, 'y2' => 128, 'hub' => 95],
            ['x' => 167, 'y1' => 44, 'y2' => 146, 'hub' => 95],
            ['x' => 271, 'y1' => 44, 'y2' => 146, 'hub' => 95],
        ];
    } else {
        $viewBox = '0 0 300 180';
        $svgLabel = 'แผนผังยางรถ 4 ล้อ';
        $svgMinWidth = '420px';
        $driveLine = ['x1' => 74, 'y1' => 90, 'x2' => 226, 'y2' => 90];

        $tires = [
            ['id' => 'T01', 'x' => 45, 'y' => 42, 'w' => 58, 'h' => 24],
            ['id' => 'T02', 'x' => 45, 'y' => 114, 'w' => 58, 'h' => 24],
            ['id' => 'T03', 'x' => 197, 'y' => 42, 'w' => 58, 'h' => 24],
            ['id' => 'T04', 'x' => 197, 'y' => 114, 'w' => 58, 'h' => 24],
        ];

        $axles = [
            ['x' => 74, 'y1' => 54, 'y2' => 126, 'hub' => 90],
            ['x' => 226, 'y1' => 54, 'y2' => 126, 'hub' => 90],
        ];
    }
@endphp

<div class="truck-tire-map-shell" data-truck-tire-map>
    <style>
        .truck-tire-map-shell {
            width: 100%;
            color: #17212b;
        }

        .truck-tire-map-card {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #d9e2ec;
            border-radius: 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f4f8fb 100%);
            box-shadow: 0 18px 44px rgba(16, 38, 58, .1);
            padding: 20px;
        }

        .truck-tire-map-svg {
            display: block;
            width: 100%;
            min-width: {{ $svgMinWidth }};
            height: auto;
        }

        .vehicle-body {
            fill: #eef5fa;
            stroke: #94a3b8;
            stroke-width: 3;
        }

        .drive-line {
            stroke: #334155;
            stroke-width: 3;
            stroke-linecap: round;
        }

        .axle-line {
            stroke: #334155;
            stroke-width: 3;
        }

        .hub {
            fill: #334155;
        }

        .tire-position {
            cursor: pointer;
            outline: none;
        }

        .tire-body {
            stroke: #1e293b;
            stroke-width: 2.5;
            transition: fill .16s ease, stroke .16s ease, filter .16s ease;
        }

        .tire-position:hover .tire-body,
        .tire-position:focus .tire-body {
            stroke: #2563eb;
            filter: drop-shadow(0 4px 8px rgba(37, 99, 235, .24));
        }

        .tire-position.is-selected .tire-body {
            stroke: #0d6efd;
            stroke-width: 3.5;
            filter: drop-shadow(0 6px 12px rgba(13, 110, 253, .32));
        }

        .tire-label {
            fill: #0f172a;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7px;
            font-weight: 800;
            text-anchor: middle;
            dominant-baseline: central;
            pointer-events: none;
        }

        .normal .tire-body { fill: #31c26b; }
        .warning .tire-body { fill: #ffd34d; }
        .replace .tire-body { fill: #ef4444; }
        .repair .tire-body { fill: #fb923c; }
        .empty .tire-body { fill: #1a1a1a; }

        .truck-tire-map-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 14px;
            margin-top: 14px;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            color: #425466;
            font-size: 14px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
        }

        .legend-dot {
            width: 16px;
            height: 16px;
            border: 1px solid #182631;
            border-radius: 5px;
        }

        .legend-dot.normal { background: #31c26b; }
        .legend-dot.warning { background: #ffd34d; }
        .legend-dot.replace { background: #ef4444; }
        .legend-dot.repair { background: #fb923c; }
        .legend-dot.empty { background: #1a1a1a; }

        @media (max-width: 768px) {
            .truck-tire-map-card {
                padding: 14px;
            }
        }
    </style>

    <div class="truck-tire-map-card">
        <svg class="truck-tire-map-svg" viewBox="{{ $viewBox }}" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="{{ $svgLabel }}">
            @unless($isSemiTrailer || $isTractor)
                <rect class="vehicle-body" x="70" y="50" width="160" height="80" rx="12" />
            @endunless

            <line
                class="drive-line"
                x1="{{ $driveLine['x1'] }}"
                y1="{{ $driveLine['y1'] }}"
                x2="{{ $driveLine['x2'] }}"
                y2="{{ $driveLine['y2'] }}"
            />

            @foreach ($axles as $index => $axle)
                <g id="AXLE_{{ $index + 1 }}" data-axle="AXLE_{{ $index + 1 }}">
                    <line class="axle-line" x1="{{ $axle['x'] }}" y1="{{ $axle['y1'] }}" x2="{{ $axle['x'] }}" y2="{{ $axle['y2'] }}" />
                    <circle class="hub" cx="{{ $axle['x'] }}" cy="{{ $axle['hub'] }}" r="6" />
                </g>
            @endforeach

            @foreach ($tires as $tire)
                <g id="{{ $tire['id'] }}" class="tire-position {{ $statusClass($tire['id']) }}" data-tire-id="{{ $tire['id'] }}" tabindex="0" role="button" aria-label="{{ $tire['id'] }}">
                    <rect class="tire-body" x="{{ $tire['x'] }}" y="{{ $tire['y'] }}" width="{{ $tire['w'] }}" height="{{ $tire['h'] }}" rx="3" />
                    <text class="tire-label" x="{{ $tire['x'] + ($tire['w'] / 2) }}" y="{{ $tire['y'] + ($tire['h'] / 2) }}">{{ $tire['id'] }}</text>
                </g>
            @endforeach
        </svg>
    </div>

    <div class="truck-tire-map-legend" aria-label="คำอธิบายสถานะยาง">
        <span class="legend-item"><span class="legend-dot normal"></span>ปกติ</span>
        <span class="legend-item"><span class="legend-dot warning"></span>เฝ้าระวัง</span>
        <span class="legend-item"><span class="legend-dot replace"></span>ต้องเปลี่ยน</span>
        <span class="legend-item"><span class="legend-dot repair"></span>ซ่อม</span>
        <span class="legend-item"><span class="legend-dot empty"></span>ไม่มีข้อมูล</span>
    </div>

</div>
