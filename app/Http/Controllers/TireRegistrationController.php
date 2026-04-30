<?php

namespace App\Http\Controllers;

use App\Http\Requests\TireRegistrationRequest;
use App\Models\TireRegistration;
use App\Models\Vehicle;
use App\Services\VehicleCurrentMileageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TireRegistrationController extends Controller
{
    public function __construct(
        protected VehicleCurrentMileageService $vehicleCurrentMileageService
    ) {
    }

    public function index(Request $request): View
    {
        $selectedVehicleType = $request->input('vehicle_type');

        $vehicleTypes = Vehicle::query()
            ->whereNotNull('vehicle_type')
            ->where('vehicle_type', '!=', '')
            ->distinct()
            ->orderBy('vehicle_type')
            ->pluck('vehicle_type');

        $vehicles = Vehicle::query()
            ->when($selectedVehicleType, fn ($query) => $query->where('vehicle_type', $selectedVehicleType))
            ->orderBy('registration_number')
            ->get();

        $selectedVehicleId = $request->integer('vehicle_id') ?: old('vehicle_id');
        $selectedVehicle = $selectedVehicleId ? Vehicle::query()->find($selectedVehicleId) : null;

        $registrations = TireRegistration::query()
            ->with(['vehicle', 'creator'])
            ->when($selectedVehicleType, function ($query) use ($selectedVehicleType) {
                $query->whereHas('vehicle', fn ($vehicleQuery) => $vehicleQuery->where('vehicle_type', $selectedVehicleType));
            })
            ->when(! $selectedVehicleId, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($selectedVehicleId, fn ($query) => $query->where('vehicle_id', $selectedVehicleId))
            ->latest('installed_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $tireStatuses = [];
        $tireDetails = [];

        if ($selectedVehicleId) {
            $latestByPosition = TireRegistration::query()
                ->where('vehicle_id', $selectedVehicleId)
                ->orderByDesc('installed_at')
                ->orderByDesc('id')
                ->get()
                ->unique('tire_position');

            foreach ($latestByPosition as $registration) {
                $tireStatuses[$registration->tire_position] = $registration->condition_status;
                $tireDetails[$registration->tire_position] = [
                    'tire_serial_number' => $registration->tire_serial_number,
                    'condition_status' => $registration->condition_status,
                    'installed_at' => optional($registration->installed_at)->format('Y-m-d'),
                    'installed_mileage_km' => $registration->installed_mileage_km,
                    'standard_replacement_distance_km' => $registration->standard_replacement_distance_km,
                    'removed_mileage_km' => $registration->removed_mileage_km,
                    'distance_run_km' => $registration->distance_run_km,
                    'tread_depth_mm' => $registration->tread_depth_mm,
                    'brand' => $registration->brand,
                    'tire_size' => $registration->tire_size,
                    'vendor_name' => $registration->vendor_name,
                    'notes' => $registration->notes,
                ];
            }
        }

        return view('tire-registrations.index', [
            'vehicles' => $vehicles,
            'vehicleTypes' => $vehicleTypes,
            'selectedVehicleType' => $selectedVehicleType,
            'selectedVehicle' => $selectedVehicle,
            'selectedVehicleCurrentMileage' => $selectedVehicle ? $this->vehicleCurrentMileageService->resolve($selectedVehicle) : null,
            'registrations' => $registrations,
            'tireStatuses' => $tireStatuses,
            'tireDetails' => $tireDetails,
            'conditionOptions' => TireRegistration::conditionOptions(),
        ]);
    }

    public function store(TireRegistrationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (
            filled($data['installed_mileage_km'] ?? null) &&
            filled($data['removed_mileage_km'] ?? null) &&
            blank($data['distance_run_km'] ?? null)
        ) {
            $data['distance_run_km'] = max(0, (float) $data['removed_mileage_km'] - (float) $data['installed_mileage_km']);
        }

        $data['created_by'] = $request->user()->id;

        TireRegistration::create($data);

        $vehicle = Vehicle::find($data['vehicle_id']);

        return redirect()
            ->route('tire-registrations.index', [
                'vehicle_type' => $vehicle?->vehicle_type,
                'vehicle_id' => $data['vehicle_id'],
            ])
            ->with('success', 'บันทึกรหัสยางเรียบร้อยแล้ว');
    }

    public function destroy(Request $request, TireRegistration $tireRegistration): RedirectResponse
    {
        $vehicle = $tireRegistration->vehicle;
        $tireRegistration->delete();

        return redirect()
            ->route('tire-registrations.index', [
                'vehicle_type' => $request->input('vehicle_type', $vehicle?->vehicle_type),
                'vehicle_id' => $request->input('vehicle_id', $vehicle?->id),
            ])
            ->with('success', 'ลบประวัติยางเรียบร้อยแล้ว');
    }
}
