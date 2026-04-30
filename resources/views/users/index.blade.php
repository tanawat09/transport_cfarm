@php($title = 'จัดการผู้ใช้')
@php($subtitle = 'เพิ่ม แก้ไข ลบ และกำหนดสิทธิ์บัญชีผู้ใช้งานระบบ')

@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">ค้นหา</label>
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="ชื่อหรืออีเมล">
            </div>
            <div class="col-md-3">
                <label class="form-label">สิทธิ์ใช้งาน</label>
                <select name="role" class="form-select">
                    <option value="">ทุกสิทธิ์</option>
                    <option value="admin" @selected(request('role') === 'admin')>ผู้ดูแลระบบ</option>
                    <option value="operator" @selected(request('role') === 'operator')>ผู้ใช้งานทั่วไป</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">ค้นหา</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">ล้าง</a>
            </div>
            <div class="col text-end">
                <a href="{{ route('users.create') }}" class="btn btn-success">เพิ่มผู้ใช้</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ชื่อผู้ใช้</th>
                    <th>อีเมล</th>
                    <th>สิทธิ์ใช้งาน</th>
                    <th>วันที่สร้าง</th>
                    <th class="text-end">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            @if(auth()->id() === $user->id)
                                <span class="badge text-bg-info">กำลังใช้งาน</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge text-bg-primary">ผู้ดูแลระบบ</span>
                            @else
                                <span class="badge text-bg-secondary">ผู้ใช้งานทั่วไป</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบผู้ใช้นี้?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" @disabled(auth()->id() === $user->id)>ลบ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">ยังไม่มีข้อมูลผู้ใช้</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
