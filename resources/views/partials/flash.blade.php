@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="fw-semibold mb-1">บันทึกข้อมูลสำเร็จ</div>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="fw-semibold mb-1">เกิดข้อผิดพลาด</div>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="fw-semibold mb-2">กรุณาตรวจสอบข้อมูลที่กรอก</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
