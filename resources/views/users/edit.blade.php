@php($title = 'แก้ไขผู้ใช้')
@php($subtitle = 'ปรับข้อมูลบัญชี สิทธิ์ใช้งาน หรือเปลี่ยนรหัสผ่าน')

@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('users.update', $user) }}" class="d-grid gap-4">
            @csrf
            @method('PUT')
            @include('users._form')
            <div>
                <button class="btn btn-primary">บันทึกการแก้ไข</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">กลับ</a>
            </div>
        </form>
    </div>
</div>
@endsection
