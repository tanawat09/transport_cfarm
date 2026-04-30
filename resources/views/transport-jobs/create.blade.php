@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('transport-jobs.store') }}" class="d-grid gap-4">
            @csrf
            @include('transport-jobs._form')
            <div>
                <button class="btn btn-primary">บันทึกเที่ยวขนส่ง</button>
                <a href="{{ route('transport-jobs.index') }}" class="btn btn-outline-secondary">กลับ</a>
            </div>
        </form>
    </div>
</div>
@endsection
