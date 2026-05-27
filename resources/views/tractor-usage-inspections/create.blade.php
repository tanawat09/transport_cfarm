@extends('layouts.app')

@php
    $title = 'บันทึกตรวจเช็กการใช้งานรถไถ';
    $subtitle = 'ตรวจสภาพรถไถก่อนใช้งานประจำวัน พร้อมบันทึกชั่วโมงการทำงานและจุดที่ต้องติดตามในหน้าเดียว';
@endphp

@section('content')
@if(!empty($lockedVehicle))
    <div class="alert alert-info mb-4">
        เปิดฟอร์มโดยล็อกที่รถไถ <strong>{{ $lockedVehicle->registration_number }}</strong> แล้ว ระบบเลือกคันนี้ไว้ให้เรียบร้อย
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('tractor-usage-inspections.store') }}" class="d-grid gap-4">
            @csrf
            @include('tractor-usage-inspections._form')
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <div class="text-muted small">ตรวจให้ครบทุกข้อก่อนบันทึก เพื่อให้ประวัติการใช้งานรถไถตรวจย้อนหลังได้ง่าย</div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('tractor-usage-inspections.index') }}" class="btn btn-outline-secondary">กลับไปหน้ารายการ</a>
                    <button class="btn btn-primary" type="submit">บันทึกการตรวจเช็ก</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
