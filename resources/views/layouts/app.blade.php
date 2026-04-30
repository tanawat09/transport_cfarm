<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ระบบบริหารรถขนส่งอาหารไก่' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f6f8fb; }
        .sidebar { min-height: calc(100vh - 56px); background: #17324d; }
        .sidebar .nav-link { color: rgba(255,255,255,.85); border-radius: .5rem; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover { background: rgba(255,255,255,.12); color: #fff; }
        .stat-card { border: 0; border-radius: 1rem; box-shadow: 0 10px 30px rgba(23,50,77,.08); }
        .card { border: 0; border-radius: 1rem; box-shadow: 0 10px 30px rgba(23,50,77,.06); }
        .table thead th { white-space: nowrap; }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#10263a;">
    <div class="container-fluid">
        <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">ระบบบริหารรถขนส่งอาหารไก่</a>
        <div class="d-flex align-items-center gap-3 text-white">
            <span>{{ auth()->user()->name ?? '' }} ({{ auth()->user()->role ?? '' }})</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">ออกจากระบบ</button>
            </form>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-2 px-3 py-4 sidebar">
            <div class="nav flex-column gap-2">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                @if(auth()->user()?->isAdmin())
                    <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">จัดการรถ</a>
                    <a class="nav-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}" href="{{ route('drivers.index') }}">จัดการพนักงานขับ</a>
                    <a class="nav-link {{ request()->routeIs('farms.*') ? 'active' : '' }}" href="{{ route('farms.index') }}">จัดการฟาร์ม</a>
                    <a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">จัดการคู่สัญญา</a>
                    <a class="nav-link {{ request()->routeIs('route-standards.*') ? 'active' : '' }}" href="{{ route('route-standards.index') }}">จัดการมาตรฐานเส้นทาง</a>
                @endif
                <a class="nav-link {{ request()->routeIs('transport-jobs.create') ? 'active' : '' }}" href="{{ route('transport-jobs.create') }}">บันทึกเที่ยวขนส่ง</a>
                <a class="nav-link {{ request()->routeIs('transport-jobs.index') || request()->routeIs('transport-jobs.show') || request()->routeIs('transport-jobs.edit') ? 'active' : '' }}" href="{{ route('transport-jobs.index') }}">รายการเที่ยวขนส่ง</a>
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">รายงาน</a>
                <a class="nav-link {{ request()->routeIs('reports.date-range') ? 'active' : '' }}" href="{{ route('reports.date-range') }}">ค้นหาตามช่วงวันที่</a>
            </div>
        </aside>
        <main class="col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ $title ?? 'ระบบบริหารรถขนส่งอาหารไก่' }}</h1>
                    @isset($subtitle)
                        <p class="text-muted mb-0">{{ $subtitle }}</p>
                    @endisset
                </div>
            </div>
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
