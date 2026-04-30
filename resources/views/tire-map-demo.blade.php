@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="#" class="d-grid gap-4" onsubmit="event.preventDefault(); alert('ตำแหน่งยางที่เลือก: ' + (document.getElementById('tire_position').value || '-'));">
            @csrf
            <input type="hidden" name="tire_position" id="tire_position">

            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                <div>
                    <h5 class="mb-1">การจัดการยาง</h5>
                    <div class="text-muted">แผนผังตำแหน่งยางหัวลาก + หางพ่วง 6 เพลา 22 ล้อ</div>
                </div>
                <div id="selected_tire_text" class="alert alert-info mb-0 py-2 px-3">
                    ยังไม่ได้เลือกตำแหน่งยาง
                </div>
            </div>

            <x-truck-tire-map :tire-statuses="$tireStatuses" />

            <div>
                <button type="submit" class="btn btn-primary">บันทึกตำแหน่งยาง</button>
            </div>
        </form>
    </div>
</div>
@endsection
