@extends('layouts.app')

@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('farms.update', $farm) }}" class="d-grid gap-4">
        @csrf @method('PUT')
        @include('farms._form')
        <div><button class="btn btn-primary">อัปเดต</button> <a href="{{ route('farms.index') }}" class="btn btn-outline-secondary">กลับ</a></div>
    </form>
</div></div>
@endsection
