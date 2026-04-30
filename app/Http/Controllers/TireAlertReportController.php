<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\TireAlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TireAlertReportController extends Controller
{
    public function __construct(
        protected TireAlertService $tireAlertService
    ) {
    }

    public function index(Request $request): View
    {
        $selectedVehicleType = $request->input('vehicle_type');
        $selectedVehicleId = $request->integer('vehicle_id') ?: null;
        $selectedStatus = $request->input('status');

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

        $rows = $this->tireAlertService->reportRows($selectedVehicleType, $selectedVehicleId, $selectedStatus);

        return view('tire-registrations.report', [
            'vehicleTypes' => $vehicleTypes,
            'vehicles' => $vehicles,
            'selectedVehicleType' => $selectedVehicleType,
            'selectedVehicleId' => $selectedVehicleId,
            'selectedStatus' => $selectedStatus,
            'rows' => $rows,
            'summary' => $this->tireAlertService->summary($rows),
        ]);
    }
}
