@extends('layouts.app')

@section('content')
<div class="card"><div class="card-body">
    <form method="POST" action="{{ route('route-standards.update', $routeStandard) }}" class="d-grid gap-4">
        @csrf @method('PUT')
        @include('route-standards._form')
        <div><button class="btn btn-primary">อัปเดต</button> <a href="{{ route('route-standards.index') }}" class="btn btn-outline-secondary">กลับ</a></div>
    </form>
</div></div>
@endsection
