<?php

namespace App\Http\Controllers;

use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $vendors = Vendor::query()
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->string('keyword');
                $query->where('vendor_name', 'like', "%{$keyword}%");
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('vendors.index', compact('vendors'));
    }

    public function create(): View
    {
        return view('vendors.create', ['vendor' => new Vendor()]);
    }

    public function store(VendorRequest $request): RedirectResponse
    {
        Vendor::create($request->validated());

        return redirect()->route('vendors.index')->with('success', 'บันทึกข้อมูลคู่สัญญาเรียบร้อยแล้ว');
    }

    public function edit(Vendor $vendor): View
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(VendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $vendor->update($request->validated());

        return redirect()->route('vendors.index')->with('success', 'อัปเดตข้อมูลคู่สัญญาเรียบร้อยแล้ว');
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        $vendor->delete();

        return redirect()->route('vendors.index')->with('success', 'ลบข้อมูลคู่สัญญาเรียบร้อยแล้ว');
    }
}
