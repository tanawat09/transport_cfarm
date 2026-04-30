<?php

namespace App\Http\Controllers;

use App\Http\Requests\FarmRequest;
use App\Models\Farm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    public function index(Request $request): View
    {
        $farms = Farm::query()
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->string('keyword');
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('farm_name', 'like', "%{$keyword}%")
                        ->orWhere('owner_name', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('farms.index', compact('farms'));
    }

    public function create(): View
    {
        return view('farms.create', ['farm' => new Farm()]);
    }

    public function store(FarmRequest $request): RedirectResponse
    {
        Farm::create($request->validated());

        return redirect()->route('farms.index')->with('success', 'บันทึกข้อมูลฟาร์มเรียบร้อยแล้ว');
    }

    public function edit(Farm $farm): View
    {
        return view('farms.edit', compact('farm'));
    }

    public function update(FarmRequest $request, Farm $farm): RedirectResponse
    {
        $farm->update($request->validated());

        return redirect()->route('farms.index')->with('success', 'อัปเดตข้อมูลฟาร์มเรียบร้อยแล้ว');
    }

    public function destroy(Farm $farm): RedirectResponse
    {
        $farm->delete();

        return redirect()->route('farms.index')->with('success', 'ลบข้อมูลฟาร์มเรียบร้อยแล้ว');
    }
}
