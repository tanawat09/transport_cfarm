<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreTripInspectionRequest;
use App\Models\Driver;
use App\Models\PreTripInspection;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PreTripInspectionController extends Controller
{
    public function index(Request $request): View
    {
        return view('pre-trip-inspections.report', $this->buildReport($request));
    }

    public function create(Request $request): View
    {
        $selectedVehicleId = $request->integer('vehicle_id') ?: null;
        $selectedVehicle = $selectedVehicleId
            ? Vehicle::query()
                ->with('primaryDriver')
                ->whereKey($selectedVehicleId)
                ->where('status', 'active')
                ->where('vehicle_type', $this->tractorVehicleType())
                ->first()
            : null;

        return view('pre-trip-inspections.create', array_merge(
            $this->getFormData(),
            [
                'inspection' => new PreTripInspection([
                    'inspection_date' => now()->toDateString(),
                    'inspection_time' => now()->format('H:i'),
                    'vehicle_id' => $selectedVehicle?->id,
                    'driver_id' => $selectedVehicle?->primary_driver_id,
                ]),
                'statusOptions' => PreTripInspection::statusOptions(),
                'evaluationItems' => PreTripInspection::evaluationItems(),
                'lockedVehicle' => $selectedVehicle,
            ]
        ));
    }

    public function exportPdf(Request $request): Response
    {
        $report = $this->buildReport($request, false);

        $pdf = Pdf::loadView('pre-trip-inspections.pdf', array_merge($report, [
            'generatedAt' => now(),
        ]))->setPaper('a4', 'landscape');

        return $pdf->download('pre-trip-inspections-report-' . now()->format('Ymd_His') . '.pdf');
    }

    public function store(PreTripInspectionRequest $request): RedirectResponse
    {
        PreTripInspection::create($this->buildPayload($request->validated()));

        return redirect()->route('pre-trip-inspections.index')->with('success', 'บันทึกการตรวจเช็กรถก่อนวิ่งเรียบร้อยแล้ว');
    }

    public function show(PreTripInspection $preTripInspection): View
    {
        $preTripInspection->load(['vehicle', 'driver', 'user']);

        return view('pre-trip-inspections.show', [
            'inspection' => $preTripInspection,
            'evaluationItems' => PreTripInspection::evaluationItems(),
        ]);
    }

    public function edit(PreTripInspection $preTripInspection): View
    {
        return view('pre-trip-inspections.edit', array_merge(
            $this->getFormData(),
            [
                'inspection' => $preTripInspection,
                'statusOptions' => PreTripInspection::statusOptions(),
                'evaluationItems' => PreTripInspection::evaluationItems(),
            ]
        ));
    }

    public function update(PreTripInspectionRequest $request, PreTripInspection $preTripInspection): RedirectResponse
    {
        $preTripInspection->update($this->buildPayload($request->validated()));

        return redirect()->route('pre-trip-inspections.show', $preTripInspection)->with('success', 'อัปเดตผลตรวจเช็กรถก่อนวิ่งเรียบร้อยแล้ว');
    }

    public function destroy(PreTripInspection $preTripInspection): RedirectResponse
    {
        $preTripInspection->delete();

        return redirect()->route('pre-trip-inspections.index')->with('success', 'ลบรายการตรวจเช็กรถก่อนวิ่งเรียบร้อยแล้ว');
    }

    private function getFormData(): array
    {
        return [
            'vehicles' => Vehicle::query()
                ->with('primaryDriver')
                ->where('status', 'active')
                ->where('vehicle_type', $this->tractorVehicleType())
                ->orderBy('registration_number')
                ->get(),
            'drivers' => Driver::query()->where('status', 'active')->orderBy('full_name')->get(),
        ];
    }

    private function buildReport(Request $request, bool $paginate = true): array
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $baseQuery = PreTripInspection::query()
            ->with(['vehicle', 'driver', 'user'])
            ->whereHas('vehicle', fn ($query) => $query->where('vehicle_type', $this->tractorVehicleType()))
            ->when($dateFrom, fn ($query) => $query->whereDate('inspection_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('inspection_date', '<=', $dateTo))
            ->when(
                $request->filled('vehicle_id') && $this->tractorVehicles()->contains('id', $request->integer('vehicle_id')),
                fn ($query) => $query->where('vehicle_id', $request->integer('vehicle_id'))
            )
            ->when($request->filled('status'), fn ($query) => $query->where('is_ready_to_drive', $request->input('status') === 'ready'));

        $summaryQuery = (clone $baseQuery);
        $totalCount = (clone $summaryQuery)->count();
        $readyCount = (clone $summaryQuery)->where('is_ready_to_drive', true)->count();
        $notReadyCount = (clone $summaryQuery)->where('is_ready_to_drive', false)->count();

        $checkFailureStats = collect(PreTripInspection::CHECK_FIELDS)
            ->map(function (string $field) use ($summaryQuery) {
                return [
                    'field' => $field,
                    'label' => PreTripInspection::checkFieldLabel($field),
                    'count' => (clone $summaryQuery)->where($field, PreTripInspection::STATUS_FAIL)->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        $query = (clone $baseQuery)
            ->latest('inspection_date')
            ->latest('inspection_time')
            ->latest('id');

        return [
            'inspections' => $paginate ? $query->paginate(15)->withQueryString() : $query->get(),
            'vehicles' => $this->tractorVehicles(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalCount' => $totalCount,
            'readyCount' => $readyCount,
            'notReadyCount' => $notReadyCount,
            'readyPercent' => $totalCount > 0 ? round(($readyCount / $totalCount) * 100, 1) : 0,
            'checkFailureStats' => $checkFailureStats,
        ];
    }

    private function buildPayload(array $validated): array
    {
        $validated['user_id'] = auth()->id();
        $validated['is_ready_to_drive'] = collect(PreTripInspection::CHECK_FIELDS)
            ->every(fn (string $field) => ($validated[$field] ?? null) === PreTripInspection::STATUS_PASS);

        return $validated;
    }

    private function tractorVehicleType(): string
    {
        return 'ลากจูง';
    }

    private function tractorVehicles()
    {
        return Vehicle::query()
            ->where('status', 'active')
            ->where('vehicle_type', $this->tractorVehicleType())
            ->orderBy('registration_number')
            ->get();
    }
}
