@extends('layouts.app')

@section('content')
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">ค้นหา</label>
            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="ชื่อคู่สัญญา">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">ค้นหา</button>
            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">ล้าง</a>
        </div>
        <div class="col text-end"><a href="{{ route('vendors.create') }}" class="btn btn-success">เพิ่มคู่สัญญา</a></div>
    </form>
</div></div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-hover align-middle">
        <thead><tr><th>ชื่อคู่สัญญา</th><th>รายละเอียด</th><th>สถานะ</th><th class="text-end">จัดการ</th></tr></thead>
        <tbody>
        @forelse($vendors as $vendor)
            <tr>
                <td>{{ $vendor->vendor_name }}</td>
                <td>{{ \Illuminate\Support\Str::limit($vendor->details, 70) }}</td>
                <td>{{ $vendor->status }}</td>
                <td class="text-end">
                    <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                    <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบข้อมูลคู่สัญญา?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">ลบ</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted">ยังไม่มีข้อมูลคู่สัญญา</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $vendors->links() }}
</div></div>
@endsection
