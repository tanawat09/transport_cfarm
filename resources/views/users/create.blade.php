@php($title = 'เพิ่มผู้ใช้')
@php($subtitle = 'สร้างบัญชีใหม่และกำหนดสิทธิ์การใช้งานระบบ')

@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}" class="d-grid gap-4">
            @csrf
            @include('users._form')
            <div>
                <button class="btn btn-primary">บันทึกผู้ใช้</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">กลับ</a>
            </div>
        </form>
    </div>
</div>
@endsection
