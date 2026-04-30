@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('vehicle-documents.store') }}" class="card">
    @csrf
    <div class="card-body">
        @include('vehicle-documents._form')
        <div class="mt-4">
            <button class="btn btn-success">บันทึกเอกสารรถ</button>
            <a href="{{ route('vehicle-documents.index') }}" class="btn btn-outline-secondary">ย้อนกลับ</a>
        </div>
    </div>
</form>
@endsection
