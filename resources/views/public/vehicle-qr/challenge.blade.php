@extends('layouts.guest')

@php
    $title = 'ยืนยัน PIN เพื่อเข้าใช้งาน';
@endphp

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-lg-5 col-md-7">
        <div class="card login-card">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <div class="text-muted small mb-2">Vehicle QR Access</div>
                    <h3 class="mb-1">{{ $vehicle->registration_number }}</h3>
                    <div class="text-muted">{{ $qrToken->access_type === \App\Models\VehicleQrToken::TYPE_INSPECTION ? 'แบบฟอร์มตรวจรถก่อนวิ่ง' : 'แบบฟอร์มบันทึกการใช้รถ' }}</div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('public.vehicle-qr.verify-pin', $qrToken->token) }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label for="pin" class="form-label">รหัส PIN</label>
                        <input type="password" inputmode="numeric" maxlength="6" name="pin" id="pin" class="form-control form-control-lg text-center" placeholder="กรอกรหัส 6 หลัก" required autofocus>
                        @error('pin')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">ยืนยันเพื่อเข้าใช้งาน</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
