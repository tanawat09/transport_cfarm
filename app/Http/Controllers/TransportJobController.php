<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\OilCompensationReason;
use App\Models\RouteStandard;
use App\Models\TransportJob;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Vendor;
use App\Http\Requests\TransportJobRequest;
use App\Services\RunningNumberService;
use App\Services\TransportJobCalculationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportJobController extends Controller
{
    public function __construct(
        protected TransportJobCalculationService $calculationService,
        protected RunningNumberService $runningNumberService,
    ) {
    }

    public function index(Request $request): View
    {
        $jobs = TransportJob::query()
            ->with(['vehicle', 'driver', 'farm', 'vendor'])
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->string('keyword');
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('document_no', 'like', "%{$keyword}%")
                        ->orWhereHas('vehicle', function ($vehicleQuery) use ($keyword) {
                            $vehicleQuery->where('registration_number', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($request->filled('start_date'), fn ($query) => $query->whereDate('transport_date', '>=', $request->date('start_date')->toDateString()))
            ->when($request->filled('end_date'), fn ($query) => $query->whereDate('transport_date', '<=', $request->date('end_date')->toDateString()))
            ->latest('transport_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('transport-jobs.index', compact('jobs'));
    }

    public function create(): View
    {
        $transportDate = now()->toDateString();

        return view('transport-jobs.create', array_merge(
            $this->getFormData(),
            [
                'transportJob' => new TransportJob(['transport_date' => $transportDate]),
                'documentNo' => $this->runningNumberService->generateTransportDocumentNo($transportDate),
            ]
        ));
    }

    public function store(TransportJobRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $routeStandard = RouteStandard::query()->findOrFail($validated['route_standard_id']);
        $payload = $this->calculationService->buildRawPayload($validated, $routeStandard);
        $payload['document_no'] = $this->runningNumberService->generateTransportDocumentNo($validated['transport_date']);

        DB::transaction(fn () => TransportJob::create($payload));

        return redirect()->route('transport-jobs.index')->with('success', 'บันทึกเที่ยวขนส่งเรียบร้อยแล้ว');
    }

    public function show(TransportJob $transportJob): View
    {
        $transportJob->load(['vehicle', 'driver', 'farm', 'vendor', 'routeStandard', 'oilCompensationReason']);

        return view('transport-jobs.show', compact('transportJob'));
    }

    public function edit(TransportJob $transportJob): View
    {
        return view('transport-jobs.edit', array_merge(
            $this->getFormData(),
            [
                'transportJob' => $transportJob,
                'documentNo' => $transportJob->document_no,
            ]
        ));
    }

    public function update(TransportJobRequest $request, TransportJob $transportJob): RedirectResponse
    {
        $validated = $request->validated();
        $routeStandard = RouteStandard::query()->findOrFail($validated['route_standard_id']);
        $payload = $this->calculationService->buildRawPayload($validated, $routeStandard);
        $payload['document_no'] = $transportJob->document_no;

        DB::transaction(fn () => $transportJob->update($payload));

        return redirect()->route('transport-jobs.index')->with('success', 'อัปเดตเที่ยวขนส่งเรียบร้อยแล้ว');
    }

    public function destroy(TransportJob $transportJob): RedirectResponse
    {
        DB::transaction(fn () => $transportJob->delete());

        return redirect()->route('transport-jobs.index')->with('success', 'ลบเที่ยวขนส่งเรียบร้อยแล้ว');
    }

    public function recalculateAll(): RedirectResponse
    {
        $vehicleIds = TransportJob::query()
            ->whereNull('deleted_at')
            ->distinct()
            ->pluck('vehicle_id');

        foreach ($vehicleIds as $vehicleId) {
            $this->calculationService->recalculateVehicleJobs((int) $vehicleId);
        }

        return redirect()
            ->route('transport-jobs.index')
            ->with('success', 'คำนวณเที่ยวขนส่งใหม่ตามลำดับวันที่ของรถทุกคันเรียบร้อยแล้ว');
    }

    private function getFormData(): array
    {
        return [
            'vehicles' => Vehicle::query()
                ->with('primaryDriver')
                ->where('status', 'active')
                ->orderBy('registration_number')
                ->get(),
            'drivers' => Driver::query()->where('status', 'active')->orderBy('full_name')->get(),
            'farms' => Farm::query()->orderBy('farm_name')->get(),
            'vendors' => Vendor::query()->where('status', 'active')->orderBy('vendor_name')->get(),
            'oilCompensationReasons' => OilCompensationReason::query()->where('status', 'active')->orderBy('reason_name')->get(),
        ];
    }
}
