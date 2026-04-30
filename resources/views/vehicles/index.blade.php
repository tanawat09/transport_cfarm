@extends('layouts.app')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">ค้นหา</label>
                <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="ทะเบียนรถ, ยี่ห้อ, รุ่น, รหัสคนขับ, ชื่อคนขับ">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">ค้นหา</button>
                <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">ล้าง</a>
            </div>
            <div class="col text-end">
                <a href="{{ route('vehicles.create') }}" class="btn btn-success">เพิ่มข้อมูลรถ</a>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ทะเบียนรถ</th>
                    <th>ยี่ห้อ</th>
                    <th>รุ่น</th>
                    <th>พนักงานขับประจำรถ</th>
                    <th class="text-end">ความจุ</th>
                    <th>สถานะ</th>
                    <th class="text-end">จัดการ</th>
                </tr>
            </thead>
            <tbody>
            @forelse($vehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle->registration_number }}</td>
                    <td>{{ $vehicle->brand }}</td>
                    <td>{{ $vehicle->model ?: '-' }}</td>
                    <td>
                        @if($vehicle->primaryDriver)
                            <div>{{ $vehicle->primaryDriver->full_name }}</div>
                            <small class="text-muted">{{ $vehicle->primaryDriver->employee_code }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($vehicle->capacity_kg ?? 0, 2) }}</td>
                    <td>{{ $vehicle->status }}</td>
                    <td class="text-end">
                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-warning">แก้ไข</a>
                        <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบข้อมูลรถ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">ลบ</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">ยังไม่มีข้อมูลรถ</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $vehicles->links() }}
    </div>
</div>
@endsection
