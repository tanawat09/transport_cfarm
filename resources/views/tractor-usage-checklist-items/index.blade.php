@php($title = 'ตั้งค่ารายการตรวจเช็กรถไถ')
@php($subtitle = 'เพิ่ม ลบ เปิด/ปิด และจัดลำดับหัวข้อที่จะแสดงในแบบฟอร์มตรวจเช็กการใช้งานรถไถ')

@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <div class="fw-bold">เพิ่มรายการตรวจเช็ก</div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tractor-usage-checklist-items.store') }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label class="form-label">หมวดตรวจเช็ก</label>
                        <input
                            type="text"
                            name="group_label"
                            value="{{ old('group_label', $item->group_label) }}"
                            list="tractor-checklist-group-labels"
                            class="form-control"
                            required
                        >
                        <datalist id="tractor-checklist-group-labels">
                            @foreach($groups as $group)
                                <option value="{{ $group->group_label }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="form-label">รหัสหมวด</label>
                        <input
                            type="text"
                            name="group_key"
                            value="{{ old('group_key', $item->group_key) }}"
                            class="form-control"
                            required
                        >
                        <div class="form-text">ใช้ตัวอักษรอังกฤษ เช่น engine_system หรือ hydraulic_system</div>
                    </div>
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
                    <div class="fw-bold">รายการตรวจเช็กรถไถทั้งหมด</div>
                    <div class="text-muted small">ปิดใช้งานเพื่อไม่ให้แสดงในฟอร์ม โดยประวัติเก่ายังดูย้อนหลังได้</div>
                </div>
                <span class="badge text-bg-secondary">{{ number_format($items->count()) }} รายการ</span>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 100px;">ลำดับ</th>
                            <th style="width: 220px;">หมวด</th>
                            <th>หัวข้อตรวจเช็ก</th>
                            <th style="width: 130px;">สถานะ</th>
                            <th class="text-end" style="width: 190px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $checkItem)
                        <tr>
                            <td>
                                <form id="update-tractor-item-{{ $checkItem->id }}" method="POST" action="{{ route('tractor-usage-checklist-items.update', $checkItem) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="sort_order" value="{{ old('sort_order', $checkItem->sort_order) }}" class="form-control form-control-sm" min="0" required>
                            </td>
                            <td>
                                    <input type="text" name="group_label" value="{{ old('group_label', $checkItem->group_label) }}" class="form-control form-control-sm mb-2" required>
                                    <input type="text" name="group_key" value="{{ old('group_key', $checkItem->group_key) }}" class="form-control form-control-sm" required>
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
                                <button form="update-tractor-item-{{ $checkItem->id }}" class="btn btn-sm btn-warning">บันทึก</button>
                                <form method="POST" action="{{ route('tractor-usage-checklist-items.destroy', $checkItem) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบรายการตรวจเช็กรถไถนี้?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">ลบ</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">ยังไม่มีรายการตรวจเช็กรถไถ</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
