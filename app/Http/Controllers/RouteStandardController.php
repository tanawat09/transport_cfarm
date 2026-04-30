<?php

namespace App\Http\Controllers;

use App\Http\Requests\RouteStandardRequest;
use App\Models\Farm;
use App\Models\RouteStandard;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RouteStandardController extends Controller
{
    public function index(Request $request): View
    {
        $routeStandards = RouteStandard::query()
            ->with(['farm', 'vendor'])
            ->when($request->filled('farm_id'), fn ($query) => $query->where('farm_id', $request->integer('farm_id')))
            ->when($request->filled('vendor_id'), fn ($query) => $query->where('vendor_id', $request->integer('vendor_id')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('route-standards.index', [
            'routeStandards' => $routeStandards,
            'farms' => Farm::query()->orderBy('farm_name')->get(),
            'vendors' => Vendor::query()->orderBy('vendor_name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('route-standards.create', [
            'routeStandard' => new RouteStandard(),
            'farms' => Farm::query()->orderBy('farm_name')->get(),
            'vendors' => Vendor::query()->orderBy('vendor_name')->get(),
        ]);
    }

    public function store(RouteStandardRequest $request): RedirectResponse
    {
        RouteStandard::create($request->validated());

        return redirect()->route('route-standards.index')->with('success', 'บันทึกมาตรฐานเส้นทางเรียบร้อยแล้ว');
    }

    public function edit(RouteStandard $routeStandard): View
    {
        return view('route-standards.edit', [
            'routeStandard' => $routeStandard,
            'farms' => Farm::query()->orderBy('farm_name')->get(),
            'vendors' => Vendor::query()->orderBy('vendor_name')->get(),
        ]);
    }

    public function update(RouteStandardRequest $request, RouteStandard $routeStandard): RedirectResponse
    {
        $routeStandard->update($request->validated());

        return redirect()->route('route-standards.index')->with('success', 'อัปเดตมาตรฐานเส้นทางเรียบร้อยแล้ว');
    }

    public function destroy(RouteStandard $routeStandard): RedirectResponse
    {
        $routeStandard->delete();

        return redirect()->route('route-standards.index')->with('success', 'ลบมาตรฐานเส้นทางเรียบร้อยแล้ว');
    }
}
