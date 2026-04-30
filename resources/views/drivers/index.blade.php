@extends('layouts.app')

@section('content')
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">ค้นหา</label>
            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="รหัส, ชื่อ, เลขใบขับขี่">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">ค้นหา</button>
            <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary">ล้าง</a>
        </div>
        <div class="col text-end">
            <a href="{{ route('drivers.create') }}" class="btn btn-success">เพิ่มพนักงานขับ</a>
        </div>
    </form>
</div></div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-hover align-middle">
        <thead><tr><th>รหัส</th><th>ชื่อ-นามสกุล</th><th>เบอร์โทร</th><th>ใบขับขี่</th><th>หมดอายุ</th><th>สถานะ</th><th class="text-end">จัดการ</th></tr></thead>
        <tbody>
        @forelse($drivers as $driver)
            <tr>
                <td>{{ $driver->employee_code }}</td>
                <td>{{ $driver->full_name }}</td>
                <td>{{ $driver->phone }}</td>
                <td>{{ $driver->driving_license_number }}</td>
                <td>{{ $driver->driving_license_expiry_date?->format('d/m/Y') }}</td>
                <td>{{ $driver->status }}</td>
                <td class="text-end">
                    <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                    <form method="POST" action="{{ route('drivers.destroy', $driver) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบข้อมูลพนักงานขับ?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">ลบ</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted">ยังไม่มีข้อมูลพนักงานขับ</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $drivers->links() }}
</div></div>
@endsection
