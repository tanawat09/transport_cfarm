@extends('layouts.app')

@php
    $title = 'แก้ไขตรวจเช็กรถไถ';
    $subtitle = 'ปรับผลตรวจและหมายเหตุของรถไถรายการนี้ให้ตรงกับการใช้งานจริง';
@endphp

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('tractor-usage-inspections.update', $inspection) }}" class="d-grid gap-4">
            @csrf
            @method('PUT')
            @include('tractor-usage-inspections._form')
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <a href="{{ route('tractor-usage-inspections.show', $inspection) }}" class="btn btn-outline-secondary">กลับ</a>
                <button class="btn btn-primary" type="submit">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>
</div>
@endsection
