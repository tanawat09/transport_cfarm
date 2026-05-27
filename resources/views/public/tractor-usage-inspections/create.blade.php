@extends('layouts.guest')

@php
    $title = 'ตรวจเช็กการใช้งานรถไถ';
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
                    <h3 class="mb-1">ตรวจเช็กการใช้งานรถไถ</h3>
                    <div class="text-muted">รถไถคูโบต้า {{ $lockedVehicle->registration_number }}</div>
                </div>

                <form method="POST" action="{{ route('public.vehicle-qr.tractor-usage-inspection.store', $qrToken->token) }}" class="d-grid gap-4">
                    @csrf
                    @include('tractor-usage-inspections._form')
                    <div>
                        <button class="btn btn-primary">บันทึกการตรวจเช็ก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
