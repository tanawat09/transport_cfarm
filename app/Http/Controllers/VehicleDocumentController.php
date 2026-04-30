<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleDocumentRequest;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VehicleDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $documents = VehicleDocument::query()
            ->with('vehicle')
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->string('keyword');
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('document_no', 'like', "%{$keyword}%")
                        ->orWhere('provider_name', 'like', "%{$keyword}%")
                        ->orWhereHas('vehicle', function ($vehicleQuery) use ($keyword) {
                            $vehicleQuery->where('registration_number', 'like', "%{$keyword}%")
                                ->orWhere('vehicle_type', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($request->filled('vehicle_type'), function ($query) use ($request) {
                $query->whereHas('vehicle', fn ($vehicleQuery) => $vehicleQuery->where('vehicle_type', $request->input('vehicle_type')));
            })
            ->when($request->filled('document_type'), fn ($query) => $query->where('document_type', $request->input('document_type')))
            ->when($request->input('status') === 'expired', fn ($query) => $query->whereDate('expires_at', '<', now()->toDateString()))
            ->when($request->input('status') === 'expiring', function ($query) {
                $query->whereDate('expires_at', '>=', now()->toDateString())
                    ->whereRaw('DATEDIFF(expires_at, CURDATE()) <= alert_before_days');
            })
            ->orderBy('expires_at')
            ->paginate(15)
            ->withQueryString();

        return view('vehicle-documents.index', [
            'documents' => $documents,
            'typeOptions' => VehicleDocument::typeOptions(),
            'vehicleTypes' => Vehicle::query()
                ->whereNotNull('vehicle_type')
                ->where('vehicle_type', '!=', '')
                ->distinct()
                ->orderBy('vehicle_type')
                ->pluck('vehicle_type'),
        ]);
    }

    public function create(): View
    {
        return view('vehicle-documents.create', [
            'document' => new VehicleDocument([
                'alert_before_days' => (int) env('VEHICLE_DOCUMENT_ALERT_DAYS', 30),
                'is_alert_enabled' => true,
            ]),
            'vehicles' => $this->vehicles(),
            'typeOptions' => VehicleDocument::typeOptions(),
        ]);
    }

    public function store(VehicleDocumentRequest $request): RedirectResponse
    {
        VehicleDocument::create($this->payload($request));

        return redirect()->route('vehicle-documents.index')->with('success', 'บันทึกเอกสารรถเรียบร้อยแล้ว');
    }

    public function edit(VehicleDocument $vehicleDocument): View
    {
        return view('vehicle-documents.edit', [
            'document' => $vehicleDocument,
            'vehicles' => $this->vehicles(),
            'typeOptions' => VehicleDocument::typeOptions(),
        ]);
    }

    public function update(VehicleDocumentRequest $request, VehicleDocument $vehicleDocument): RedirectResponse
    {
        $vehicleDocument->update($this->payload($request));

        return redirect()->route('vehicle-documents.index')->with('success', 'อัปเดตเอกสารรถเรียบร้อยแล้ว');
    }

    public function destroy(VehicleDocument $vehicleDocument): RedirectResponse
    {
        $vehicleDocument->delete();

        return redirect()->route('vehicle-documents.index')->with('success', 'ลบเอกสารรถเรียบร้อยแล้ว');
    }

    private function payload(VehicleDocumentRequest $request): array
    {
        $data = $request->validated();
        $data['is_alert_enabled'] = $request->boolean('is_alert_enabled');

        return $data;
    }

    private function vehicles()
    {
        return Vehicle::query()
            ->orderBy('registration_number')
            ->get();
    }
}
