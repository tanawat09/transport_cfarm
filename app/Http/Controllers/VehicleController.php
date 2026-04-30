<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $vehicles = Vehicle::query()
            ->with('primaryDriver')
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->string('keyword');
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('registration_number', 'like', "%{$keyword}%")
                        ->orWhere('brand', 'like', "%{$keyword}%")
                        ->orWhere('model', 'like', "%{$keyword}%")
                        ->orWhereHas('primaryDriver', function ($driverQuery) use ($keyword) {
                            $driverQuery->where('employee_code', 'like', "%{$keyword}%")
                                ->orWhere('full_name', 'like', "%{$keyword}%");
                        });
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('vehicles.index', compact('vehicles'));
    }

    public function create(): View
    {
        return view('vehicles.create', [
            'vehicle' => new Vehicle(),
            'drivers' => $this->getActiveDrivers(),
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
}
