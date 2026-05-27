<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreTripInspectionRequest;
use App\Http\Requests\TractorUsageInspectionRequest;
use App\Http\Requests\VehicleUsageLogRequest;
use App\Models\Driver;
use App\Models\Farm;
use App\Models\PreTripChecklistItem;
use App\Models\PreTripInspection;
use App\Models\TractorUsageInspection;
use App\Models\Vehicle;
use App\Models\VehicleQrAccessLog;
use App\Models\VehicleQrToken;
use App\Models\VehicleUsageLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicVehicleQrController extends Controller
{
    public function showChallenge(string $token): View|RedirectResponse
    {
        $qrToken = $this->findAvailableToken($token);
        $this->logEvent($qrToken, VehicleQrAccessLog::EVENT_CHALLENGE_VIEWED);

        if ($this->isVerified($qrToken)) {
            return redirect()->to($qrToken->targetFormUrl());
        }

        return view('public.vehicle-qr.challenge', [
            'qrToken' => $qrToken,
            'vehicle' => $qrToken->vehicle,
        ]);
    }

    public function verifyPin(Request $request, string $token): RedirectResponse
    {
        $validated = $request->validate([
            'pin' => ['required', 'digits:6'],
        ]);

        $qrToken = $this->findAvailableToken($token);

        if (! $qrToken->verifyPin($validated['pin'])) {
            $this->logEvent($qrToken, VehicleQrAccessLog::EVENT_PIN_FAILED);

            return back()
                ->withInput()
                ->withErrors(['pin' => 'รหัส PIN ไม่ถูกต้อง']);
        }

        $request->session()->put($this->sessionKey($qrToken), now()->addHours(8)->timestamp);
        $qrToken->markVerified();
        $this->logEvent($qrToken, VehicleQrAccessLog::EVENT_PIN_VERIFIED);

        return redirect()->to($qrToken->targetFormUrl());
    }

    public function showInspectionForm(Request $request, string $token): View|RedirectResponse
    {
        $qrToken = $this->findAvailableToken($token);
        abort_unless($qrToken->access_type === VehicleQrToken::TYPE_INSPECTION, 404);

        if (! $this->isVerified($qrToken, $request)) {
            return redirect()->route('public.vehicle-qr.access', $qrToken->token);
        }

        $vehicle = $qrToken->vehicle()->with('primaryDriver')->firstOrFail();

        $this->logEvent($qrToken, VehicleQrAccessLog::EVENT_FORM_VIEWED);

        return view('public.pre-trip-inspections.create', [
            'inspection' => new PreTripInspection([
                'inspection_date' => now()->toDateString(),
                'inspection_time' => now()->format('H:i'),
                'vehicle_id' => $vehicle->id,
                'driver_id' => $vehicle->primary_driver_id,
            ]),
            'vehicles' => collect([$vehicle]),
            'drivers' => Driver::query()->where('status', 'active')->orderBy('full_name')->get(),
            'statusOptions' => PreTripInspection::statusOptions(),
            'checklistItems' => PreTripChecklistItem::query()->active()->ordered()->get(),
            'lockedVehicle' => $vehicle,
            'qrToken' => $qrToken,
        ]);
    }

    public function storeInspection(PreTripInspectionRequest $request, string $token): RedirectResponse
    {
        $qrToken = $this->findAvailableToken($token);
        abort_unless($qrToken->access_type === VehicleQrToken::TYPE_INSPECTION, 404);

        if (! $this->isVerified($qrToken, $request)) {
            return redirect()->route('public.vehicle-qr.access', $qrToken->token);
        }

        PreTripInspection::create($this->inspectionPayload($request->validated()));

        $qrToken->markUsed();
        $this->logEvent($qrToken, VehicleQrAccessLog::EVENT_FORM_SUBMITTED, [
            'inspection_date' => $request->input('inspection_date'),
        ]);

        return redirect()
            ->route('public.vehicle-qr.inspection.form', $qrToken->token)
            ->with('success', 'บันทึกผลตรวจเช็กรถก่อนวิ่งเรียบร้อยแล้ว');
    }

    public function showUsageForm(Request $request, string $token): View|RedirectResponse
    {
        $qrToken = $this->findAvailableToken($token);
        abort_unless($qrToken->access_type === VehicleQrToken::TYPE_USAGE, 404);

        if (! $this->isVerified($qrToken, $request)) {
            return redirect()->route('public.vehicle-qr.access', $qrToken->token);
        }

        $vehicle = $qrToken->vehicle()->with('primaryDriver')->firstOrFail();

        $latestLog = VehicleUsageLog::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereNotNull('odometer_end')
            ->latest('usage_date')
            ->latest('id')
            ->first();

        $this->logEvent($qrToken, VehicleQrAccessLog::EVENT_FORM_VIEWED);

        return view('public.vehicle-usage-logs.create', [
            'log' => new VehicleUsageLog([
                'usage_date' => now()->toDateString(),
                'vehicle_id' => $vehicle->id,
                'driver_name' => $vehicle->primaryDriver?->full_name,
                'odometer_start' => $latestLog?->odometer_end,
            ]),
            'vehicles' => collect([$vehicle]),
            'drivers' => Driver::query()->where('status', 'active')->orderBy('full_name')->get(),
            'lockedVehicle' => $vehicle,
            'latestLog' => $latestLog,
            'qrToken' => $qrToken,
        ]);
    }

    public function storeUsageLog(VehicleUsageLogRequest $request, string $token): RedirectResponse
    {
        $qrToken = $this->findAvailableToken($token);
        abort_unless($qrToken->access_type === VehicleQrToken::TYPE_USAGE, 404);

        if (! $this->isVerified($qrToken, $request)) {
            return redirect()->route('public.vehicle-qr.access', $qrToken->token);
        }

        VehicleUsageLog::create($this->usagePayload($request->validated()));

        $qrToken->markUsed();
        $this->logEvent($qrToken, VehicleQrAccessLog::EVENT_FORM_SUBMITTED, [
            'usage_date' => $request->input('usage_date'),
        ]);

        return redirect()
            ->route('public.vehicle-qr.usage.form', $qrToken->token)
            ->with('success', 'บันทึกการใช้รถเรียบร้อยแล้ว');
    }

    public function showTractorUsageInspectionForm(Request $request, string $token): View|RedirectResponse
    {
        $qrToken = $this->findAvailableToken($token);
        abort_unless($qrToken->access_type === VehicleQrToken::TYPE_TRACTOR_USAGE_INSPECTION, 404);

        if (! $this->isVerified($qrToken, $request)) {
            return redirect()->route('public.vehicle-qr.access', $qrToken->token);
        }

        $vehicle = $qrToken->vehicle()->with('primaryDriver')->firstOrFail();
        abort_unless($vehicle->supportsTractorUsageInspectionQr(), 404);

        $this->logEvent($qrToken, VehicleQrAccessLog::EVENT_FORM_VIEWED);

        return view('public.tractor-usage-inspections.create', [
            'inspection' => new TractorUsageInspection([
                'inspection_date' => now()->toDateString(),
                'inspection_time' => now()->format('H:i'),
                'vehicle_id' => $vehicle->id,
                'driver_id' => $vehicle->primary_driver_id,
            ]),
            'vehicles' => collect([$vehicle]),
            'farms' => Farm::query()->orderBy('farm_name')->get(),
            'statusOptions' => TractorUsageInspection::statusOptions(),
            'checklistGroups' => TractorUsageInspection::checklistGroups(),
            'lockedVehicle' => $vehicle,
            'qrToken' => $qrToken,
        ]);
    }

    public function storeTractorUsageInspection(TractorUsageInspectionRequest $request, string $token): RedirectResponse
    {
        $qrToken = $this->findAvailableToken($token);
        abort_unless($qrToken->access_type === VehicleQrToken::TYPE_TRACTOR_USAGE_INSPECTION, 404);

        if (! $this->isVerified($qrToken, $request)) {
            return redirect()->route('public.vehicle-qr.access', $qrToken->token);
        }

        $vehicle = $qrToken->vehicle()->firstOrFail();
        abort_unless($vehicle->supportsTractorUsageInspectionQr(), 404);

        TractorUsageInspection::create($this->tractorUsageInspectionPayload(
            array_merge($request->validated(), ['vehicle_id' => $vehicle->id])
        ));

        $qrToken->markUsed();
        $this->logEvent($qrToken, VehicleQrAccessLog::EVENT_FORM_SUBMITTED, [
            'inspection_date' => $request->input('inspection_date'),
        ]);

        return redirect()
            ->route('public.vehicle-qr.tractor-usage-inspection.form', $qrToken->token)
            ->with('success', 'บันทึกการตรวจเช็กการใช้งานรถไถเรียบร้อยแล้ว');
    }

    private function inspectionPayload(array $validated): array
    {
        $items = PreTripChecklistItem::query()->active()->ordered()->get();
        $inspectionItems = collect($validated['inspection_items'] ?? []);
        $results = [];

        foreach ($items as $item) {
            $input = $inspectionItems->get($item->key, []);
            $results[$item->key] = [
                'label' => $item->label,
                'status' => $input['status'] ?? null,
                'note' => $input['note'] ?? null,
            ];
        }

        $validated['user_id'] = auth()->id();
        $validated['checklist_results'] = $results;
        $validated['is_ready_to_drive'] = collect($results)
            ->every(fn (array $result) => ($result['status'] ?? null) === PreTripInspection::STATUS_PASS);

        foreach (PreTripInspection::LEGACY_CHECK_KEYS as $key) {
            $validated[$key . '_status'] = $results[$key]['status'] ?? null;
            $validated[$key . '_note'] = $results[$key]['note'] ?? null;
        }

        unset($validated['inspection_items']);

        return $validated;
    }

    private function usagePayload(array $validated): array
    {
        $distance = 0;

        if (filled($validated['odometer_start'] ?? null) && filled($validated['odometer_end'] ?? null)) {
            $distance = max(0, (float) $validated['odometer_end'] - (float) $validated['odometer_start']);
        }

        $fuelTotal = 0;

        if (filled($validated['fuel_liters'] ?? null) && filled($validated['fuel_price_per_liter'] ?? null)) {
            $fuelTotal = (float) $validated['fuel_liters'] * (float) $validated['fuel_price_per_liter'];
        }

        return array_merge($validated, [
            'usage_month' => substr((string) $validated['usage_date'], 0, 7),
            'distance_km' => $distance,
            'fuel_total_amount' => $fuelTotal,
            'user_id' => auth()->id(),
        ]);
    }

    private function tractorUsageInspectionPayload(array $validated): array
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

    private function findAvailableToken(string $token): VehicleQrToken
    {
        $qrToken = VehicleQrToken::query()
            ->with('vehicle')
            ->where('token', $token)
            ->firstOrFail();

        abort_unless($qrToken->isAvailable(), 404);

        return $qrToken;
    }

    private function isVerified(VehicleQrToken $qrToken, ?Request $request = null): bool
    {
        $request ??= request();
        $expiresAt = (int) $request->session()->get($this->sessionKey($qrToken), 0);

        return $expiresAt > now()->timestamp;
    }

    private function sessionKey(VehicleQrToken $qrToken): string
    {
        return 'vehicle_qr_verified.' . $qrToken->id;
    }

    private function logEvent(VehicleQrToken $qrToken, string $eventType, array $payload = []): void
    {
        VehicleQrAccessLog::create([
            'vehicle_qr_token_id' => $qrToken->id,
            'vehicle_id' => $qrToken->vehicle_id,
            'access_type' => $qrToken->access_type,
            'event_type' => $eventType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'payload' => $payload === [] ? null : $payload,
            'happened_at' => now(),
        ]);
    }
}
