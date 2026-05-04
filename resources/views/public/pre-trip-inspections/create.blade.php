@extends('layouts.guest')

@php
    $title = 'ตรวจเช็กรถก่อนวิ่ง';
@endphp

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-12 col-xl-10">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card login-card">
            <div class="card-body p-4">
                <div class="mb-4">
                    <div class="text-muted small">Public QR Form</div>
                    <h3 class="mb-1">ตรวจเช็กรถก่อนวิ่ง</h3>
                    <div class="text-muted">ทะเบียนรถ {{ $lockedVehicle->registration_number }}</div>
                </div>

                <form method="POST" action="{{ route('public.vehicle-qr.inspection.store', $qrToken->token) }}" class="d-grid gap-4">
                    @csrf
                    @include('pre-trip-inspections._form')
                    <div>
                        <button class="btn btn-primary">บันทึกผลตรวจเช็ก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
