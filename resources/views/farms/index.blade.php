@extends('layouts.app')

@section('content')
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">ค้นหา</label>
            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="ชื่อฟาร์ม, เจ้าของ, เบอร์โทร">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">ค้นหา</button>
            <a href="{{ route('farms.index') }}" class="btn btn-outline-secondary">ล้าง</a>
        </div>
        <div class="col text-end"><a href="{{ route('farms.create') }}" class="btn btn-success">เพิ่มฟาร์ม</a></div>
    </form>
</div></div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-hover align-middle">
        <thead><tr><th>ชื่อฟาร์ม</th><th>เจ้าของ</th><th>เบอร์โทร</th><th>ที่อยู่</th><th class="text-end">จัดการ</th></tr></thead>
        <tbody>
        @forelse($farms as $farm)
            <tr>
                <td>{{ $farm->farm_name }}</td>
                <td>{{ $farm->owner_name }}</td>
                <td>{{ $farm->phone }}</td>
                <td>{{ \Illuminate\Support\Str::limit($farm->address, 60) }}</td>
                <td class="text-end">
                    <a href="{{ route('farms.edit', $farm) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                    <form method="POST" action="{{ route('farms.destroy', $farm) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบข้อมูลฟาร์ม?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">ลบ</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted">ยังไม่มีข้อมูลฟาร์ม</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $farms->links() }}
</div></div>
@endsection
