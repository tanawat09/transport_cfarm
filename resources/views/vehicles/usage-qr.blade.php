@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5 col-md-8">
        <div class="card">
            <div class="card-body text-center">
                <div class="text-muted small mb-2">QR Code บันทึกการใช้รถ</div>
                <h4 class="mb-1">{{ $vehicle->registration_number }}</h4>
                <div class="text-muted mb-4">{{ $vehicle->vehicle_type ?: '-' }} / {{ $vehicle->brand }}{{ $vehicle->model ? ' / ' . $vehicle->model : '' }}</div>

                <div class="border rounded-3 bg-white p-3 d-inline-flex mb-4">
                    <img
                        src="{{ route('vehicles.usage-qr-code', $vehicle) }}"
                        alt="QR Code บันทึกการใช้รถ {{ $vehicle->registration_number }}"
                        style="width: 280px; height: 280px;"
                    >
                </div>

                <div class="mb-3">
                    <input type="text" class="form-control text-center" value="{{ $vehicle->usageLogQrUrl() }}" readonly>
                </div>

                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="{{ $vehicle->usageLogQrUrl() }}" class="btn btn-primary" target="_blank">เปิดฟอร์มใช้รถ</a>
                    <a href="{{ route('vehicles.usage-qr-print', $vehicle) }}" class="btn btn-outline-secondary" target="_blank">พิมพ์ QR</a>
                    <a href="{{ route('vehicles.usage-qr-code', $vehicle) }}" class="btn btn-outline-secondary" target="_blank">เปิด SVG</a>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-dark">กลับ</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
