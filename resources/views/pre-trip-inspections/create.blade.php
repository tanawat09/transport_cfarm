@extends('layouts.app')

@section('content')
@if(!empty($lockedVehicle))
    <div class="alert alert-info">
        เปิดฟอร์มจาก QR Code ของรถ <strong>{{ $lockedVehicle->registration_number }}</strong>
    </div>
@endif
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('pre-trip-inspections.store') }}" class="d-grid gap-4">
            @csrf
            @include('pre-trip-inspections._form')
            <div>
                <button class="btn btn-primary">บันทึกผลตรวจเช็ก</button>
                <a href="{{ route('pre-trip-inspections.index') }}" class="btn btn-outline-secondary">กลับ</a>
            </div>
        </form>
    </div>
</div>
@endsection
