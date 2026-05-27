@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-9">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header border-0 py-4 px-4 px-lg-5 text-white" style="background: linear-gradient(135deg, #17324d 0%, #1f6f78 100%);">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('images/cfarm-logo.png') }}" alt="CFARM" style="width: 120px; max-width: 100%; height: auto;">
                        <div>
                            <div class="fw-bold fs-4">QR ตรวจเช็กการใช้งานรถไถ</div>
                            <div class="text-white-50">สำหรับประเภทรถ รถไถ คูโบต้า</div>
                        </div>
                    </div>
                    <span class="badge rounded-pill text-bg-light px-3 py-2 text-dark">สแกนเพื่อตรวจรถไถ</span>
                </div>
            </div>

            <div class="card-body p-4 p-lg-5">
                <div class="row g-4 align-items-stretch">
                    <div class="col-lg-5">
                        <div class="h-100 border rounded-4 bg-light-subtle p-4">
                            <div class="small text-primary fw-semibold mb-2">ข้อมูลรถไถ</div>
                            <h2 class="fw-bold mb-2">{{ $vehicle->registration_number }}</h2>
                            <div class="text-secondary mb-4">
                                {{ $vehicle->vehicle_type ?: '-' }}
                                @if($vehicle->brand)
                                    / {{ $vehicle->brand }}{{ $vehicle->model ? ' / ' . $vehicle->model : '' }}
                                @endif
                            </div>

                            <div class="rounded-4 p-4 text-center text-white" style="background: #17324d;">
                                <div class="small opacity-75">PIN สำหรับยืนยันตัวตน</div>
                                <div class="display-6 fw-bold mt-1 mb-0" style="letter-spacing: .08em;">{{ $qrToken->decryptedPin() }}</div>
                            </div>

                            <div class="mt-4">
                                <label class="form-label small text-muted mb-2">ลิงก์สำหรับใช้งาน</label>
                                <input type="text" class="form-control form-control-lg text-center" value="{{ $qrToken->publicUrl() }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="h-100 border rounded-4 p-4 text-center bg-white">
                            <div class="border rounded-4 bg-white p-3 p-lg-4 d-inline-flex mb-4 shadow-sm">
                                <img
                                    src="{{ route('vehicles.tractor-usage-inspection-qr-code', $vehicle) }}"
                                    alt="QR Code {{ $vehicle->registration_number }}"
                                    style="width: min(100%, 320px); height: auto; aspect-ratio: 1 / 1;"
                                >
                            </div>

                            <h3 class="fw-bold mb-2">สแกนเพื่อตรวจเช็กการใช้งานรถไถ</h3>
                            <p class="text-secondary mb-4">พนักงานสแกน QR แล้วกรอก PIN เพื่อเข้าสู่แบบฟอร์มตรวจเช็กรถไถคูโบต้าคันนี้โดยตรง</p>

                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <a href="{{ $qrToken->publicUrl() }}" class="btn btn-primary px-4" target="_blank">เปิดฟอร์มตรวจรถไถ</a>
                                <a href="{{ route('vehicles.tractor-usage-inspection-qr-print', $vehicle) }}" class="btn btn-outline-secondary px-4" target="_blank">พิมพ์ QR</a>
                                <a href="{{ route('vehicles.tractor-usage-inspection-qr-code', $vehicle) }}" class="btn btn-outline-secondary" target="_blank">เปิด SVG</a>
                                <a href="{{ route('vehicles.index') }}" class="btn btn-outline-dark">กลับ</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('vehicles._qr-management', ['vehicle' => $vehicle, 'qrToken' => $qrToken, 'recentQrLogs' => $recentQrLogs])
    </div>
</div>
@endsection
