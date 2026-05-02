@php($title = 'ตั้งค่ารายการตรวจเช็กก่อนวิ่ง')
@php($subtitle = 'เพิ่ม ลบ เปิด/ปิด และจัดลำดับหัวข้อที่จะแสดงในแบบฟอร์มตรวจเช็กรถก่อนวิ่ง')

@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <div class="fw-bold">เพิ่มรายการตรวจเช็ก</div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('pre-trip-checklist-items.store') }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label class="form-label">หัวข้อตรวจเช็ก</label>
                        <textarea name="label" rows="4" class="form-control" required>{{ old('label') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">ลำดับแสดงผล</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}" class="form-control" min="0" required>
                    </div>
                    <label class="form-check">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" checked>
                        <span class="form-check-label">เปิดใช้งานในฟอร์มตรวจ</span>
                    </label>
                    <button class="btn btn-primary">เพิ่มรายการ</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold">รายการตรวจเช็กทั้งหมด</div>
                    <div class="text-muted small">ปิดใช้งานเพื่อไม่ให้แสดงในฟอร์ม โดยข้อมูลเก่าจะยังดูย้อนหลังได้</div>
                </div>
                <span class="badge text-bg-secondary">{{ number_format($items->count()) }} รายการ</span>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 110px;">ลำดับ</th>
                            <th>หัวข้อตรวจเช็ก</th>
                            <th style="width: 140px;">สถานะ</th>
                            <th class="text-end" style="width: 190px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $checkItem)
                        <tr>
                            <td>
                                <form id="update-item-{{ $checkItem->id }}" method="POST" action="{{ route('pre-trip-checklist-items.update', $checkItem) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="sort_order" value="{{ old('sort_order', $checkItem->sort_order) }}" class="form-control form-control-sm" min="0" required>
                            </td>
                            <td>
                                    <textarea name="label" rows="2" class="form-control" required>{{ old('label', $checkItem->label) }}</textarea>
                            </td>
                            <td>
                                    <label class="form-check">
                                        <input type="checkbox" name="is_active" value="1" class="form-check-input" @checked($checkItem->is_active)>
                                        <span class="form-check-label">{{ $checkItem->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}</span>
                                    </label>
                                </form>
                            </td>
                            <td class="text-end">
                                <button form="update-item-{{ $checkItem->id }}" class="btn btn-sm btn-warning">บันทึก</button>
                                <form method="POST" action="{{ route('pre-trip-checklist-items.destroy', $checkItem) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบรายการตรวจเช็กนี้?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">ลบ</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">ยังไม่มีรายการตรวจเช็ก</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
