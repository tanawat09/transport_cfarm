<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleQrAccessLog;
use App\Models\VehicleQrToken;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $vehicleTypes = Vehicle::query()
            ->whereNotNull('vehicle_type')
            ->where('vehicle_type', '<>', '')
            ->distinct()
            ->orderBy('vehicle_type')
            ->pluck('vehicle_type');

        $vehicles = Vehicle::query()
            ->with('primaryDriver')
            ->when($request->filled('vehicle_type'), fn ($query) => $query->where('vehicle_type', $request->input('vehicle_type')))
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->string('keyword');
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('registration_number', 'like', "%{$keyword}%")
                        ->orWhere('brand', 'like', "%{$keyword}%")
                        ->orWhere('model', 'like', "%{$keyword}%")
                        ->orWhere('towing_vehicle', 'like', "%{$keyword}%")
                        ->orWhere('vehicle_type', 'like', "%{$keyword}%")
                        ->orWhereHas('primaryDriver', function ($driverQuery) use ($keyword) {
                            $driverQuery->where('employee_code', 'like', "%{$keyword}%")
                                ->orWhere('full_name', 'like', "%{$keyword}%");
                        });
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('vehicles.index-modern', compact('vehicles', 'vehicleTypes'));
    }

    public function create(): View
    {
        return view('vehicles.create', [
            'vehicle' => new Vehicle(),
            'drivers' => $this->getActiveDrivers(),
            'semiTrailerVehicles' => $this->getSemiTrailerVehicles(),
        ]);
    }

    public function store(VehicleRequest $request): RedirectResponse
    {
        Vehicle::create($request->validated());

        return redirect()->route('vehicles.index')->with('success', 'บันทึกข้อมูลรถเรียบร้อยแล้ว');
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('vehicles.edit', [
            'vehicle' => $vehicle,
            'drivers' => $this->getActiveDrivers(),
            'semiTrailerVehicles' => $this->getSemiTrailerVehicles($vehicle),
        ]);
    }

    public function inspectionQrPage(Vehicle $vehicle): View
    {
        abort_unless($vehicle->supportsPreTripInspectionQr(), 404);

        return view('vehicles.qr', $this->qrViewData($vehicle, VehicleQrToken::TYPE_INSPECTION));
    }

    public function inspectionQrPrint(Vehicle $vehicle): View
    {
        abort_unless($vehicle->supportsPreTripInspectionQr(), 404);

        return view('vehicles.qr-print', $this->qrViewData($vehicle, VehicleQrToken::TYPE_INSPECTION));
    }

    public function bulkQrPrint(Request $request): View|RedirectResponse
    {
        $qrType = $request->input('qr_type') === 'usage' ? 'usage' : 'inspection';
        $vehicleIds = collect($request->input('vehicles', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($vehicleIds->isEmpty()) {
            return redirect()->route('vehicles.index')->with('error', 'กรุณาเลือกรถอย่างน้อย 1 คัน');
        }

        $vehicles = Vehicle::query()
            ->whereKey($vehicleIds)
            ->orderBy('registration_number')
            ->get()
            ->filter(fn (Vehicle $vehicle) => $qrType === 'usage'
                ? $vehicle->supportsUsageLog()
                : $vehicle->supportsPreTripInspectionQr())
            ->values();

        if ($vehicles->isEmpty()) {
            return redirect()->route('vehicles.index')->with('error', 'รถที่เลือกไม่รองรับ QR ประเภทนี้');
        }

        return view('vehicles.qr-bulk-print', compact('vehicles', 'qrType'));
    }

    public function usageQrPage(Vehicle $vehicle): View
    {
        abort_unless($vehicle->supportsUsageLog(), 404);

        return view('vehicles.usage-qr', $this->qrViewData($vehicle, VehicleQrToken::TYPE_USAGE));
    }

    public function usageQrPrint(Vehicle $vehicle): View
    {
        abort_unless($vehicle->supportsUsageLog(), 404);

        return view('vehicles.usage-qr-print', $this->qrViewData($vehicle, VehicleQrToken::TYPE_USAGE));
    }

    public function inspectionQrCode(Vehicle $vehicle): Response
    {
        abort_unless($vehicle->supportsPreTripInspectionQr(), 404);

        return $this->qrResponse($vehicle->issueQrToken(VehicleQrToken::TYPE_INSPECTION)->publicUrl());
    }

    public function usageQrCode(Vehicle $vehicle): Response
    {
        abort_unless($vehicle->supportsUsageLog(), 404);

        return $this->qrResponse($vehicle->issueQrToken(VehicleQrToken::TYPE_USAGE)->publicUrl());
    }

    public function toggleQrToken(Request $request, Vehicle $vehicle, string $accessType): RedirectResponse
    {
        abort_unless($this->isSupportedQrType($vehicle, $accessType), 404);

        $qrToken = $vehicle->issueQrToken($accessType);
        $qrToken->update(['is_active' => ! $qrToken->is_active]);

        $this->recordAdminQrEvent($qrToken, $qrToken->is_active
            ? VehicleQrAccessLog::EVENT_TOKEN_ENABLED
            : VehicleQrAccessLog::EVENT_TOKEN_DISABLED);

        return back()->with('success', $qrToken->is_active
            ? 'เปิดใช้งาน QR เรียบร้อยแล้ว'
            : 'ปิดใช้งาน QR เรียบร้อยแล้ว');
    }

    public function regenerateQrToken(Request $request, Vehicle $vehicle, string $accessType): RedirectResponse
    {
        abort_unless($this->isSupportedQrType($vehicle, $accessType), 404);

        $qrToken = $vehicle->issueQrToken($accessType);
        $newPin = $qrToken->regenerate()['plain_pin'];
        $this->recordAdminQrEvent($qrToken, VehicleQrAccessLog::EVENT_TOKEN_REGENERATED);

        return back()->with('success', "สร้าง QR ใหม่เรียบร้อยแล้ว รหัส PIN ใหม่คือ {$newPin}");
    }

    private function qrResponse(string $url): Response
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel' => QRCode::ECC_M,
            'scale' => 8,
            'imageBase64' => false,
        ]);

        $svg = (new QRCode($options))->render($url);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    public function update(VehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($request->validated());

        return redirect()->route('vehicles.index')->with('success', 'อัปเดตข้อมูลรถเรียบร้อยแล้ว');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'ลบข้อมูลรถเรียบร้อยแล้ว');
    }

    private function getActiveDrivers()
    {
        return Driver::query()
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();
    }

    private function getSemiTrailerVehicles(?Vehicle $currentVehicle = null)
    {
        return Vehicle::query()
            ->where(function ($query) use ($currentVehicle) {
                $query->where(function ($semiTrailerQuery) use ($currentVehicle) {
                    $semiTrailerQuery
                        ->where(function ($typeQuery) {
                            $typeQuery->where('vehicle_type', Vehicle::TYPE_SEMI_TRAILER_FEED)
                                ->orWhere('vehicle_type', 'like', '%กึ่งพ่วง%')
                                ->orWhere('vehicle_type', 'like', '%อาหารสัตว์%');
                        })
                        ->where('status', 'active')
                        ->when($currentVehicle, fn ($currentQuery) => $currentQuery->whereKeyNot($currentVehicle->id));
                });

                if ($currentVehicle && filled($currentVehicle->towing_vehicle)) {
                    $query->orWhere('registration_number', $currentVehicle->towing_vehicle);
                }
            })
            ->orderBy('registration_number')
            ->get(['id', 'registration_number', 'vehicle_type', 'brand', 'model']);
    }

    private function qrViewData(Vehicle $vehicle, string $accessType): array
    {
        $qrToken = $vehicle->issueQrToken($accessType);

        return [
            'vehicle' => $vehicle,
            'qrToken' => $qrToken,
            'recentQrLogs' => $qrToken->accessLogs()->latest('happened_at')->limit(10)->get(),
        ];
    }

    private function isSupportedQrType(Vehicle $vehicle, string $accessType): bool
    {
        return match ($accessType) {
            VehicleQrToken::TYPE_INSPECTION => $vehicle->supportsPreTripInspectionQr(),
            VehicleQrToken::TYPE_USAGE => $vehicle->supportsUsageLog(),
            default => false,
        };
    }

    private function recordAdminQrEvent(VehicleQrToken $qrToken, string $eventType): void
    {
        VehicleQrAccessLog::create([
            'vehicle_qr_token_id' => $qrToken->id,
            'vehicle_id' => $qrToken->vehicle_id,
            'access_type' => $qrToken->access_type,
            'event_type' => $eventType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => [
                'admin_user_id' => auth()->id(),
            ],
            'happened_at' => now(),
        ]);
    }
}
