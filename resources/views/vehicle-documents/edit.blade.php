@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('vehicle-documents.update', $document) }}" class="card">
    @csrf
    @method('PUT')
    <div class="card-body">
        @include('vehicle-documents._form')
        <div class="mt-4">
            <button class="btn btn-primary">อัปเดตเอกสารรถ</button>
            <a href="{{ route('vehicle-documents.index') }}" class="btn btn-outline-secondary">ย้อนกลับ</a>
        </div>
    </div>
</form>
@endsection
