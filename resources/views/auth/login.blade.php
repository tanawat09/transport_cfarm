@extends('layouts.guest')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card login-card">
            <div class="card-body p-4 p-lg-5">
                <h1 class="h3 fw-semibold mb-2">เข้าสู่ระบบ</h1>
                <p class="text-muted mb-4">ระบบบริหารรถขนส่งอาหารไก่</p>
                @include('partials.flash')
                <form method="POST" action="{{ route('login.store') }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label class="form-label">อีเมล</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                    </div>
                    <div>
                        <label class="form-label">รหัสผ่าน</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                        <label class="form-check-label" for="remember">จดจำการเข้าสู่ระบบ</label>
                    </div>
                    <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
