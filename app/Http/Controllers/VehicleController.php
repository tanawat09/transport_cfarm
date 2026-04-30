<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Driver;
use App\Models\Vehicle;
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

        return view('vehicles.index', compact('vehicles', 'vehicleTypes'));
    }

    public function create(): View
    {
        return view('vehicles.create', [
            'vehicle' => new Vehicle(),
            'drivers' => $this->getActiveDrivers(),
            'towingVehicles' => $this->getTowingVehicles(),
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
            'towingVehicles' => $this->getTowingVehicles($vehicle),
        ]);
    }

    public function inspectionQrPage(Vehicle $vehicle): View
    {
        abort_unless($vehicle->supportsPreTripInspectionQr(), 404);

        return view('vehicles.qr', compact('vehicle'));
    }

    public function inspectionQrPrint(Vehicle $vehicle): View
    {
        abort_unless($vehicle->supportsPreTripInspectionQr(), 404);

        return view('vehicles.qr-print', compact('vehicle'));
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

        return view('vehicles.usage-qr', compact('vehicle'));
    }

    public function usageQrPrint(Vehicle $vehicle): View
    {
        abort_unless($vehicle->supportsUsageLog(), 404);

        return view('vehicles.usage-qr-print', compact('vehicle'));
    }

    public function inspectionQrCode(Vehicle $vehicle): Response
    {
        abort_unless($vehicle->supportsPreTripInspectionQr(), 404);

        return $this->qrResponse($vehicle->inspectionQrUrl());
    }

    public function usageQrCode(Vehicle $vehicle): Response
    {
        abort_unless($vehicle->supportsUsageLog(), 404);

        return $this->qrResponse($vehicle->usageLogQrUrl());
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

    private function getTowingVehicles(?Vehicle $currentVehicle = null)
    {
        return Vehicle::query()
            ->where('vehicle_type', 'ลากจูง')
            ->where('status', 'active')
            ->when($currentVehicle, fn ($query) => $query->whereKeyNot($currentVehicle->id))
            ->orderBy('registration_number')
            ->get(['id', 'registration_number']);
    }
}
