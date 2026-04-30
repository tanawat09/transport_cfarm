<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverRequest;
use App\Models\Driver;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request): View
    {
        $drivers = Driver::query()
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->string('keyword');
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('employee_code', 'like', "%{$keyword}%")
                        ->orWhere('full_name', 'like', "%{$keyword}%")
                        ->orWhere('driving_license_number', 'like', "%{$keyword}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('drivers.index', compact('drivers'));
    }

    public function create(): View
    {
        return view('drivers.create', ['driver' => new Driver()]);
    }

    public function store(DriverRequest $request): RedirectResponse
    {
        Driver::create($request->validated());

        return redirect()->route('drivers.index')->with('success', 'บันทึกข้อมูลพนักงานขับเรียบร้อยแล้ว');
    }

    public function edit(Driver $driver): View
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(DriverRequest $request, Driver $driver): RedirectResponse
    {
        $driver->update($request->validated());

        return redirect()->route('drivers.index')->with('success', 'อัปเดตข้อมูลพนักงานขับเรียบร้อยแล้ว');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'ลบข้อมูลพนักงานขับเรียบร้อยแล้ว');
    }
}
