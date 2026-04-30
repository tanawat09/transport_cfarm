@extends('layouts.app')

@section('content')
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">ฟาร์ม</label>
            <select name="farm_id" class="form-select">
                <option value="">ทั้งหมด</option>
                @foreach($farms as $farm)
                    <option value="{{ $farm->id }}" @selected(request('farm_id') == $farm->id)>{{ $farm->farm_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">คู่สัญญา</label>
            <select name="vendor_id" class="form-select">
                <option value="">ทั้งหมด</option>
                @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" @selected(request('vendor_id') == $vendor->id)>{{ $vendor->vendor_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">ค้นหา</button>
            <a href="{{ route('route-standards.index') }}" class="btn btn-outline-secondary">ล้าง</a>
        </div>
        <div class="col text-end"><a href="{{ route('route-standards.create') }}" class="btn btn-success">เพิ่มมาตรฐานเส้นทาง</a></div>
    </form>
</div></div>
<div class="card"><div class="card-body table-responsive">
    <table class="table table-hover align-middle">
        <thead><tr><th>ฟาร์ม</th><th>คู่สัญญา</th><th class="text-end">น้ำมันกำหนด</th><th class="text-end">ระยะทางมาตรฐาน</th><th>สถานะ</th><th class="text-end">จัดการ</th></tr></thead>
        <tbody>
        @forelse($routeStandards as $routeStandard)
            <tr>
                <td>{{ $routeStandard->farm?->farm_name }}</td>
                <td>{{ $routeStandard->vendor?->vendor_name }}</td>
                <td class="text-end">{{ number_format($routeStandard->company_oil_liters, 2) }}</td>
                <td class="text-end">{{ number_format($routeStandard->standard_distance_km, 2) }}</td>
                <td>{{ $routeStandard->status }}</td>
                <td class="text-end">
                    <a href="{{ route('route-standards.edit', $routeStandard) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                    <form method="POST" action="{{ route('route-standards.destroy', $routeStandard) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบมาตรฐานเส้นทาง?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">ลบ</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted">ยังไม่มีข้อมูลมาตรฐานเส้นทาง</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $routeStandards->links() }}
</div></div>
@endsection
