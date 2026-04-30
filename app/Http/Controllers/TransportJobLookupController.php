<?php

namespace App\Http\Controllers;

use App\Models\RouteStandard;
use App\Services\RunningNumberService;
use App\Services\TransportJobCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransportJobLookupController extends Controller
{
    public function __construct(
        protected TransportJobCalculationService $calculationService,
        protected RunningNumberService $runningNumberService,
    ) {
    }

    public function routeStandard(Request $request): JsonResponse
    {
        $request->validate([
            'farm_id' => ['required', 'integer'],
            'vendor_id' => ['required', 'integer'],
        ]);

        $routeStandard = $this->calculationService->findActiveRouteStandard(
            $request->integer('farm_id'),
            $request->integer('vendor_id'),
        );

        if (! $routeStandard) {
            return response()->json([
                'message' => 'ไม่พบมาตรฐานเส้นทางสำหรับฟาร์มและคู่สัญญานี้',
            ], 404);
        }

        return response()->json([
            'id' => $routeStandard->id,
            'company_oil_liters' => (float) $routeStandard->company_oil_liters,
            'standard_distance_km' => (float) $routeStandard->standard_distance_km,
        ]);
    }

    public function farmVendors(Request $request): JsonResponse
    {
        $request->validate([
            'farm_id' => ['required', 'integer'],
        ]);

        $vendors = RouteStandard::query()
            ->join('vendors', 'vendors.id', '=', 'route_standards.vendor_id')
            ->where('route_standards.farm_id', $request->integer('farm_id'))
            ->where('route_standards.status', 'active')
            ->where('vendors.status', 'active')
            ->whereNull('route_standards.deleted_at')
            ->whereNull('vendors.deleted_at')
            ->distinct()
            ->orderBy('vendors.vendor_name')
            ->get([
                'vendors.id',
                'vendors.vendor_name',
            ])
            ->map(fn ($vendor) => [
                'id' => (int) $vendor->id,
                'vendor_name' => $vendor->vendor_name,
            ])
            ->values();

        return response()->json([
            'vendors' => $vendors,
        ]);
    }

    public function documentNumber(Request $request): JsonResponse
    {
        $request->validate([
            'transport_date' => ['required', 'date'],
        ]);

        return response()->json([
            'document_no' => $this->runningNumberService->generateTransportDocumentNo($request->input('transport_date')),
        ]);
    }

    public function latestVehicleMileage(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => ['required', 'integer'],
            'transport_date' => ['nullable', 'date'],
        ]);

        $latestJob = $this->calculationService->findLatestVehicleJob(
            $request->integer('vehicle_id'),
            $request->input('transport_date'),
        );

        if (! $latestJob) {
            return response()->json([
                'message' => 'ยังไม่พบประวัติเที่ยวล่าสุดของรถคันนี้',
            ], 404);
        }

        return response()->json([
            'odometer_start' => (float) $latestJob->odometer_end,
            'latest_transport_date' => $latestJob->transport_date?->format('Y-m-d'),
            'latest_document_no' => $latestJob->document_no,
        ]);
    }
}
