<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">ชื่อผู้ใช้</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">อีเมล</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">สิทธิ์ใช้งาน</label>
        <select name="role" class="form-select" required>
            @foreach(['admin' => 'ผู้ดูแลระบบ', 'operator' => 'ผู้ใช้งานทั่วไป'] as $value => $label)
                <option value="{{ $value }}" @selected(old('role', $user->role ?: 'operator') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">รหัสผ่าน</label>
        <input type="password" name="password" class="form-control" @required(!$user->exists) autocomplete="new-password">
        @if($user->exists)
            <div class="form-text">เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน</div>
        @endif
    </div>
    <div class="col-md-4">
        <label class="form-label">ยืนยันรหัสผ่าน</label>
        <input type="password" name="password_confirmation" class="form-control" @required(!$user->exists) autocomplete="new-password">
    </div>
</div>
