@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('pre-trip-inspections.update', $inspection) }}" class="d-grid gap-4">
            @csrf
            @method('PUT')
            @include('pre-trip-inspections._form')
            <div>
                <button class="btn btn-primary">บันทึกการแก้ไข</button>
                <a href="{{ route('pre-trip-inspections.show', $inspection) }}" class="btn btn-outline-secondary">กลับ</a>
            </div>
        </form>
    </div>
</div>
@endsection
