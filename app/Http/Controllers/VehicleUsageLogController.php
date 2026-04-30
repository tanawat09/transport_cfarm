<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleUsageLogRequest;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleUsageLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VehicleUsageLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = VehicleUsageLog::query()
            ->with(['vehicle', 'driver', 'user'])
            ->when($request->filled('vehicle_id'), fn ($query) => $query->where('vehicle_id', $request->integer('vehicle_id')))
            ->when($request->filled('usage_month'), fn ($query) => $query->where('usage_month', $request->input('usage_month')))
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->string('keyword');
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('purpose', 'like', "%{$keyword}%")
                        ->orWhere('destination', 'like', "%{$keyword}%")
                        ->orWhere('driver_name', 'like', "%{$keyword}%")
                        ->orWhereHas('vehicle', fn ($vehicleQuery) => $vehicleQuery->where('registration_number', 'like', "%{$keyword}%"))
                        ->orWhereHas('driver', fn ($driverQuery) => $driverQuery->where('full_name', 'like', "%{$keyword}%"));
                });
            })
            ->latest('usage_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('vehicle-usage-logs.index', [
            'logs' => $logs,
            'vehicles' => $this->usageVehicles(),
        ]);
    }

    public function create(Request $request): View
    {
        $selectedVehicle = $request->integer('vehicle_id')
            ? Vehicle::query()->with('primaryDriver')->whereKey($request->integer('vehicle_id'))->first()
            : null;

        abort_if($selectedVehicle && ! $selectedVehicle->supportsUsageLog(), 404);

        $latestLog = $selectedVehicle
            ? VehicleUsageLog::query()
                ->where('vehicle_id', $selectedVehicle->id)
                ->whereNotNull('odometer_end')
                ->latest('usage_date')
                ->latest('id')
                ->first()
            : null;

        return view('vehicle-usage-logs.create', [
            'log' => new VehicleUsageLog([
                'usage_date' => now()->toDateString(),
                'vehicle_id' => $selectedVehicle?->id,
                'driver_name' => $selectedVehicle?->primaryDriver?->full_name,
                'odometer_start' => $latestLog?->odometer_end,
            ]),
            'vehicles' => $this->usageVehicles(),
            'drivers' => Driver::query()->where('status', 'active')->orderBy('full_name')->get(),
            'lockedVehicle' => $selectedVehicle,
            'latestLog' => $latestLog,
        ]);
    }

    public function store(VehicleUsageLogRequest $request): RedirectResponse
    {
        VehicleUsageLog::create($this->payload($request->validated()));

        return redirect()
            ->route('vehicle-usage-logs.index', [
                'vehicle_id' => $request->integer('vehicle_id'),
                'usage_month' => substr((string) $request->input('usage_date'), 0, 7),
            ])
            ->with('success', 'บันทึกการใช้รถเรียบร้อยแล้ว');
    }

    public function destroy(VehicleUsageLog $vehicleUsageLog): RedirectResponse
    {
        $vehicleId = $vehicleUsageLog->vehicle_id;
        $usageMonth = $vehicleUsageLog->usage_month;
        $vehicleUsageLog->delete();

        return redirect()
            ->route('vehicle-usage-logs.index', [
                'vehicle_id' => $vehicleId,
                'usage_month' => $usageMonth,
            ])
            ->with('success', 'ลบบันทึกการใช้รถเรียบร้อยแล้ว');
    }

    private function payload(array $validated): array
    {
        $distance = 0;

        if (filled($validated['odometer_start'] ?? null) && filled($validated['odometer_end'] ?? null)) {
            $distance = max(0, (float) $validated['odometer_end'] - (float) $validated['odometer_start']);
        }

        $fuelTotal = 0;

        if (filled($validated['fuel_liters'] ?? null) && filled($validated['fuel_price_per_liter'] ?? null)) {
            $fuelTotal = (float) $validated['fuel_liters'] * (float) $validated['fuel_price_per_liter'];
        }

        return array_merge($validated, [
            'usage_month' => substr((string) $validated['usage_date'], 0, 7),
            'distance_km' => $distance,
            'fuel_total_amount' => $fuelTotal,
            'user_id' => auth()->id(),
        ]);
    }

    private function usageVehicles()
    {
        return Vehicle::query()
            ->whereIn('vehicle_type', Vehicle::USAGE_LOG_VEHICLE_TYPES)
            ->where('status', 'active')
            ->orderBy('registration_number')
            ->get();
    }
}
