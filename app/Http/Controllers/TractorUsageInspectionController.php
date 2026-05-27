<?php

namespace App\Http\Controllers;

use App\Http\Requests\TractorUsageInspectionRequest;
use App\Models\Farm;
use App\Models\TractorUsageInspection;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TractorUsageInspectionController extends Controller
{
    public function index(Request $request): View
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $tractorVehicles = $this->tractorVehicles();
        $tractorIds = $tractorVehicles->pluck('id');

        $baseQuery = TractorUsageInspection::query()
            ->with(['vehicle', 'farm', 'user'])
            ->whereIn('vehicle_id', $tractorIds)
            ->when($dateFrom, fn ($query) => $query->whereDate('inspection_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('inspection_date', '<=', $dateTo))
            ->when(
                $request->filled('vehicle_id') && $tractorIds->contains($request->integer('vehicle_id')),
                fn ($query) => $query->where('vehicle_id', $request->integer('vehicle_id'))
            )
            ->when($request->filled('farm_id'), fn ($query) => $query->where('farm_id', $request->integer('farm_id')))
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('is_ready_for_use', $request->input('status') === 'ready')
            );

        $summaryInspections = (clone $baseQuery)->get();
        $totalCount = $summaryInspections->count();
        $readyCount = $summaryInspections->where('is_ready_for_use', true)->count();
        $notReadyCount = $summaryInspections->where('is_ready_for_use', false)->count();

        $failureStats = collect(TractorUsageInspection::checklistItems())
            ->map(function (array $item) use ($summaryInspections) {
                return [
                    'label' => $item['label'],
                    'count' => $summaryInspections->filter(function (TractorUsageInspection $inspection) use ($item) {
                        return ($inspection->checklist_results[$item['key']]['status'] ?? null) === TractorUsageInspection::STATUS_FAIL;
                    })->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();

        $inspections = (clone $baseQuery)
            ->latest('inspection_date')
            ->latest('inspection_time')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('tractor-usage-inspections.index', [
            'inspections' => $inspections,
            'vehicles' => $tractorVehicles,
            'farms' => Farm::query()->orderBy('farm_name')->get(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalCount' => $totalCount,
            'readyCount' => $readyCount,
            'notReadyCount' => $notReadyCount,
            'readyPercent' => $totalCount > 0 ? round(($readyCount / $totalCount) * 100, 1) : 0,
            'failureStats' => $failureStats,
        ]);
    }

    public function create(Request $request): View
    {
        $selectedVehicleId = $request->integer('vehicle_id') ?: null;
        $selectedVehicle = $selectedVehicleId
            ? Vehicle::query()
                ->with('primaryDriver')
                ->whereKey($selectedVehicleId)
                ->where('status', 'active')
                ->whereIn('vehicle_type', $this->tractorVehicleTypes())
                ->first()
            : null;

        return view('tractor-usage-inspections.create', array_merge($this->formData(), [
            'inspection' => new TractorUsageInspection([
                'inspection_date' => now()->toDateString(),
                'inspection_time' => now()->format('H:i'),
                'vehicle_id' => $selectedVehicle?->id,
                'farm_id' => $request->integer('farm_id') ?: null,
            ]),
            'statusOptions' => TractorUsageInspection::statusOptions(),
            'checklistGroups' => TractorUsageInspection::checklistGroups(),
            'lockedVehicle' => $selectedVehicle,
        ]));
    }

    public function store(TractorUsageInspectionRequest $request): RedirectResponse
    {
        TractorUsageInspection::create($this->buildPayload($request->validated()));

        return redirect()
            ->route('tractor-usage-inspections.index')
            ->with('success', 'บันทึกการตรวจเช็กการใช้งานรถไถเรียบร้อยแล้ว');
    }

    public function show(TractorUsageInspection $tractorUsageInspection): View
    {
        $tractorUsageInspection->load(['vehicle', 'farm', 'user']);

        return view('tractor-usage-inspections.show', [
            'inspection' => $tractorUsageInspection,
            'checklistGroups' => $tractorUsageInspection->checklistItemsForDisplay(),
        ]);
    }

    public function edit(TractorUsageInspection $tractorUsageInspection): View
    {
        return view('tractor-usage-inspections.edit', array_merge($this->formData(), [
            'inspection' => $tractorUsageInspection,
            'statusOptions' => TractorUsageInspection::statusOptions(),
            'checklistGroups' => TractorUsageInspection::checklistGroups(),
            'lockedVehicle' => null,
        ]));
    }

    public function update(
        TractorUsageInspectionRequest $request,
        TractorUsageInspection $tractorUsageInspection
    ): RedirectResponse {
        $tractorUsageInspection->update($this->buildPayload($request->validated()));

        return redirect()
            ->route('tractor-usage-inspections.show', $tractorUsageInspection)
            ->with('success', 'อัปเดตการตรวจเช็กการใช้งานรถไถเรียบร้อยแล้ว');
    }

    public function destroy(TractorUsageInspection $tractorUsageInspection): RedirectResponse
    {
        $tractorUsageInspection->delete();

        return redirect()
            ->route('tractor-usage-inspections.index')
            ->with('success', 'ลบรายการตรวจเช็กการใช้งานรถไถเรียบร้อยแล้ว');
    }

    private function buildPayload(array $validated): array
    {
        $results = [];

        foreach (TractorUsageInspection::checklistItems() as $item) {
            $input = $validated['inspection_items'][$item['key']] ?? [];

            $results[$item['key']] = [
                'label' => $item['label'],
                'status' => $input['status'] ?? null,
                'note' => $input['note'] ?? null,
            ];
        }

        $validated['user_id'] = auth()->id();
        $validated['checklist_results'] = $results;
        $validated['is_ready_for_use'] = collect($results)
            ->every(fn (array $result) => ($result['status'] ?? null) === TractorUsageInspection::STATUS_PASS);

        unset($validated['inspection_items']);

        return $validated;
    }

    private function formData(): array
    {
        return [
            'vehicles' => Vehicle::query()
                ->with('primaryDriver')
                ->where('status', 'active')
                ->whereIn('vehicle_type', $this->tractorVehicleTypes())
                ->orderBy('registration_number')
                ->get(),
            'farms' => Farm::query()->orderBy('farm_name')->get(),
        ];
    }

    private function tractorVehicleTypes(): array
    {
        return ['รถไถ', 'รถไถ คูโบต้า'];
    }

    private function tractorVehicles(): Collection
    {
        return Vehicle::query()
            ->where('status', 'active')
            ->whereIn('vehicle_type', $this->tractorVehicleTypes())
            ->orderBy('registration_number')
            ->get();
    }
}
