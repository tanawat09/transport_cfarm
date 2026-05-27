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
        :root {
            --app-bg: #f3f7fb;
            --panel-bg: rgba(255, 255, 255, 0.94);
            --panel-border: rgba(148, 163, 184, 0.16);
            --panel-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            --sidebar-bg: linear-gradient(180deg, #0f2235 0%, #17324d 52%, #1b4a5a 100%);
            --topbar-bg: rgba(15, 34, 53, 0.92);
            --text-main: #17212b;
            --text-soft: #6b7b8c;
            --accent: #1f6f78;
            --accent-dark: #184d57;
            --accent-soft: rgba(31, 111, 120, 0.12);
            --radius-lg: 22px;
            --radius-md: 16px;
            --radius-sm: 12px;
        }

        html, body {
            min-height: 100%;
        }

        body {
            margin: 0;
            color: var(--text-main);
            background:
                radial-gradient(circle at top right, rgba(31, 111, 120, 0.08), transparent 26%),
                radial-gradient(circle at left top, rgba(23, 50, 77, 0.06), transparent 22%),
                var(--app-bg);
            font-family: "Segoe UI", Tahoma, sans-serif;
            overflow-x: hidden;
        }

        .app-topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            background: #16293d;
            border-bottom: 1px solid rgba(15, 23, 42, 0.18);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
        }

        .app-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .app-topbar-start,
        .app-topbar-end {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .app-menu-toggle {
            display: none;
            width: 42px;
            height: 42px;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            line-height: 1;
        }

        .app-menu-toggle span,
        .app-menu-toggle::before,
        .app-menu-toggle::after {
            content: '';
            display: block;
            width: 18px;
            height: 2px;
            border-radius: 999px;
            background: currentColor;
        }

        .app-menu-toggle {
            flex-direction: column;
            gap: 4px;
        }

        .app-brand {
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .app-brand-title {
            font-size: 1.05rem;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .app-brand-subtitle {
            font-size: .76rem;
            color: rgba(255, 255, 255, 0.68);
        }

        .app-user-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            font-size: .92rem;
        }

        .app-shell {
            padding: 22px;
        }

        .app-grid {
            display: grid;
            grid-template-columns: 290px minmax(0, 1fr);
            gap: 24px;
            align-items: start;
        }

        .app-content {
            min-width: 0;
        }

        .app-sidebar-overlay {
            display: none;
        }

        .app-sidebar {
            position: static;
            min-height: calc(100vh - 118px);
            padding: 22px;
            border-radius: 28px;
            color: #fff;
            background: var(--sidebar-bg);
            box-shadow: 0 22px 40px rgba(15, 23, 42, 0.18);
        }

        .app-sidebar-close {
            display: none;
        }

        .sidebar-heading {
            margin-bottom: 18px;
            padding-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .sidebar-heading strong {
            display: block;
            font-size: 1rem;
            font-weight: 800;
        }

        .sidebar-heading span {
            color: rgba(255, 255, 255, 0.68);
            font-size: .82rem;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            min-height: 46px;
            padding: 10px 14px;
            border-radius: 14px;
            color: rgba(255, 255, 255, 0.84);
            text-decoration: none;
            font-weight: 600;
            transition: .18s ease;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(2px);
        }

        .sidebar-subnav {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin: -2px 0 4px;
            padding-left: 14px;
        }

        .sidebar-subnav .nav-link {
            min-height: 40px;
            padding: 8px 12px;
            font-size: .9rem;
            color: rgba(255, 255, 255, 0.72);
        }

        .sidebar-subnav .nav-link::before {
            content: '';
            width: 8px;
            height: 8px;
            margin-right: 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.32);
            flex: 0 0 auto;
        }

        .page-header-card {
            margin-bottom: 22px;
            padding: 24px 26px;
            border: 1px solid var(--panel-border);
            border-radius: var(--radius-lg);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.96) 0%, rgba(244, 249, 252, 0.92) 100%);
            box-shadow: var(--panel-shadow);
        }

        .page-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            padding: 6px 12px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent-dark);
            font-size: .8rem;
            font-weight: 700;
        }

        .page-title {
            margin: 0;
            font-size: 1.9rem;
            font-weight: 800;
        }

        .page-subtitle {
            margin: 8px 0 0;
            max-width: 780px;
            color: var(--text-soft);
            font-size: .98rem;
        }

        .card,
        .content-card {
            border: 1px solid var(--panel-border);
            border-radius: var(--radius-lg);
            background: var(--panel-bg);
            box-shadow: var(--panel-shadow);
        }

        .card-header {
            border-bottom: 1px solid rgba(148, 163, 184, 0.16);
            background: transparent;
            padding: 1rem 1.25rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .stat-card,
        .metric-card {
            border: 1px solid var(--panel-border);
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.06);
        }

        .btn {
            border-radius: 12px;
            font-weight: 700;
            padding: .62rem 1rem;
        }

        .btn-sm {
            border-radius: 10px;
            padding: .46rem .85rem;
        }

        .btn-primary {
            border-color: var(--accent);
            background: var(--accent);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            border-color: var(--accent-dark);
            background: var(--accent-dark);
        }

        .form-label {
            margin-bottom: .45rem;
            font-weight: 700;
            color: #344256;
        }

        .form-control,
        .form-select,
        textarea.form-control {
            min-height: 46px;
            border: 1px solid rgba(148, 163, 184, 0.32);
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0.96);
            color: var(--text-main);
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03);
        }

        textarea.form-control {
            min-height: auto;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(31, 111, 120, 0.5);
            box-shadow: 0 0 0 .25rem rgba(31, 111, 120, 0.12);
        }

        .form-text {
            margin-top: .45rem;
            color: #7b8794;
        }

        .table {
            --bs-table-bg: transparent;
            margin-bottom: 0;
        }

        .table thead th {
            border-bottom-width: 1px;
            border-color: rgba(148, 163, 184, 0.18);
            color: #516274;
            font-size: .84rem;
            font-weight: 800;
            background: rgba(246, 249, 252, 0.9);
            white-space: nowrap;
        }

        .table > :not(caption) > * > * {
            padding: .9rem .95rem;
            border-color: rgba(148, 163, 184, 0.15);
        }

        .table-responsive {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-hover tbody tr:hover {
            background: rgba(31, 111, 120, 0.04);
        }

        .alert {
            border: 1px solid transparent;
            border-radius: 16px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
        }

        .pagination {
            gap: 6px;
        }

        .page-link {
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: 10px;
            color: #334155;
        }

        .page-item.active .page-link {
            border-color: var(--accent);
            background: var(--accent);
        }

        @media (max-width: 1199.98px) {
            body.is-sidebar-open {
                overflow: hidden;
            }

            .app-menu-toggle {
                display: inline-flex;
                flex: 0 0 auto;
            }

            .app-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .app-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1060;
                width: min(86vw, 330px);
                height: 100vh;
                min-height: 0;
                overflow-y: auto;
                border-radius: 0 26px 26px 0;
                transform: translateX(-105%);
                transition: transform .22s ease;
            }

            body.is-sidebar-open .app-sidebar {
                transform: translateX(0);
            }

            .app-sidebar-overlay {
                display: block;
                position: fixed;
                inset: 0;
                z-index: 1050;
                background: rgba(15, 23, 42, 0.46);
                opacity: 0;
                pointer-events: none;
                transition: opacity .18s ease;
            }

            body.is-sidebar-open .app-sidebar-overlay {
                opacity: 1;
                pointer-events: auto;
            }

            .app-sidebar-close {
                display: inline-flex;
                position: absolute;
                top: 16px;
                right: 16px;
                width: 38px;
                height: 38px;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(255, 255, 255, 0.18);
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
                font-size: 1.25rem;
                line-height: 1;
            }

            .sidebar-heading {
                padding-right: 42px;
            }
        }

        @media (max-width: 767.98px) {
            .app-topbar {
                position: static;
            }

            .app-topbar-inner {
                padding-left: 14px !important;
                padding-right: 14px !important;
                flex-wrap: wrap;
            }

            .app-topbar-start {
                flex: 1 1 100%;
            }

            .app-topbar-end {
                width: 100%;
                justify-content: space-between;
            }

            .app-user-chip {
                min-width: 0;
                padding: 7px 10px;
                font-size: .82rem;
            }

            .app-user-chip span {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .app-shell {
                padding: 14px;
            }

            .app-grid {
                gap: 14px;
            }

            .page-header-card {
                margin-bottom: 16px;
                padding: 16px;
                border-radius: 18px;
            }

            .page-kicker {
                margin-bottom: 8px;
                padding: 5px 10px;
                font-size: .74rem;
            }

            .page-title {
                font-size: 1.45rem;
            }

            .page-subtitle {
                font-size: .88rem;
            }

            .card,
            .content-card,
            .stat-card,
            .metric-card {
                border-radius: 16px;
            }

            .card-header,
            .card-body {
                padding: 1rem;
            }

            .btn {
                min-height: 42px;
            }

            .table > :not(caption) > * > * {
                padding: .75rem .8rem;
            }
        }

        @media (max-width: 575.98px) {
            .app-brand-title {
                font-size: .95rem;
            }

            .app-brand-subtitle {
                font-size: .68rem;
            }

            .app-shell {
                padding: 10px;
            }

            .page-header-card {
                padding: 14px;
            }

            .page-title {
                font-size: 1.28rem;
            }

            .form-control,
            .form-select,
            textarea.form-control {
                min-height: 44px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg app-topbar">
    <div class="container-fluid px-4 py-2 app-topbar-inner">
        <div class="app-topbar-start">
            <button type="button" class="app-menu-toggle" data-sidebar-toggle aria-label="เปิดเมนูการทำงาน" aria-controls="app-sidebar" aria-expanded="false">
                <span></span>
            </button>
            <a class="app-brand" href="{{ route('dashboard') }}">
                <span class="app-brand-title">ระบบบริหารรถขนส่งอาหารไก่</span>
                <span class="app-brand-subtitle">Transport Management Center</span>
            </a>
        </div>
        <div class="app-topbar-end">
            <div class="app-user-chip">
                <span>{{ auth()->user()->name ?? '' }}</span>
                <span>{{ auth()->user()->role ?? '' }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">ออกจากระบบ</button>
            </form>
        </div>
    </div>
</nav>
<div class="app-sidebar-overlay" data-sidebar-close></div>
<div class="app-shell">
    <div class="app-grid">
        <aside class="app-sidebar" id="app-sidebar">
            <button type="button" class="app-sidebar-close" data-sidebar-close aria-label="ปิดเมนูการทำงาน">&times;</button>
            <div class="sidebar-heading">
                <strong>เมนูการทำงาน</strong>
                <span>เข้าถึงงานหลักของระบบได้จากจุดเดียว</span>
            </div>
            <nav class="sidebar-nav">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">ภาพรวมระบบ</a>
                <a class="nav-link {{ request()->routeIs('transport-jobs.*') || request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('transport-jobs.create') }}">บันทึกเที่ยวขนส่ง</a>
                <div class="sidebar-subnav">
                    <a class="nav-link {{ request()->routeIs('transport-jobs.index') || request()->routeIs('transport-jobs.show') || request()->routeIs('transport-jobs.edit') ? 'active' : '' }}" href="{{ route('transport-jobs.index') }}">รายการเที่ยวขนส่ง</a>
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">รายงาน</a>
                </div>
                <a class="nav-link {{ request()->routeIs('vehicle-usage-logs.*') ? 'active' : '' }}" href="{{ route('vehicle-usage-logs.index') }}">บันทึกการใช้รถ</a>
                  <a class="nav-link {{ request()->routeIs('tractor-usage-inspections.*') || request()->routeIs('tractor-usage-checklist-items.*') ? 'active' : '' }}" href="{{ route('tractor-usage-inspections.index') }}">ตรวจเช็กรถไถ</a>
                  @if(auth()->user()?->isAdmin())
                      <div class="sidebar-subnav">
                          <a class="nav-link {{ request()->routeIs('tractor-usage-checklist-items.*') ? 'active' : '' }}" href="{{ route('tractor-usage-checklist-items.index') }}">ตั้งค่ารายการตรวจรถไถ</a>
                      </div>
                  @endif
                <a class="nav-link {{ request()->routeIs('pre-trip-inspections.*') ? 'active' : '' }}" href="{{ route('pre-trip-inspections.index') }}">ตรวจเช็กรถก่อนวิ่ง</a>
                @if(auth()->user()?->isAdmin())
                    <div class="sidebar-subnav">
                        <a class="nav-link {{ request()->routeIs('pre-trip-checklist-items.*') ? 'active' : '' }}" href="{{ route('pre-trip-checklist-items.index') }}">ตั้งค่ารายการตรวจเช็ก</a>
                    </div>
                @endif
                <a class="nav-link {{ request()->routeIs('tire-registrations.index') || request()->routeIs('tire-registrations.report') ? 'active' : '' }}" href="{{ route('tire-registrations.index') }}">การจัดการยาง</a>
                <div class="sidebar-subnav">
                    <a class="nav-link {{ request()->routeIs('tire-registrations.report') ? 'active' : '' }}" href="{{ route('tire-registrations.report') }}">รายงานยางใกล้เปลี่ยน</a>
                </div>
                @if(auth()->user()?->isAdmin())
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">จัดการผู้ใช้</a>
                    <a class="nav-link {{ request()->routeIs('vehicle-documents.*') ? 'active' : '' }}" href="{{ route('vehicle-documents.index') }}">ทะเบียน พ.ร.บ. ประกัน</a>
                    <a class="nav-link {{ request()->routeIs('route-standards.*') ? 'active' : '' }}" href="{{ route('route-standards.index') }}">มาตรฐานเส้นทาง</a>
                    <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">จัดการรถ</a>
                    <a class="nav-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}" href="{{ route('drivers.index') }}">จัดการพนักงานขับ</a>
                    <a class="nav-link {{ request()->routeIs('farms.*') ? 'active' : '' }}" href="{{ route('farms.index') }}">จัดการฟาร์ม</a>
                    <a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}">จัดการคู่สัญญา</a>
                    <a class="nav-link {{ request()->routeIs('telegram-settings.*') ? 'active' : '' }}" href="{{ route('telegram-settings.edit') }}">จัดการ Telegram</a>
                @endif
            </nav>
        </aside>
        <main class="app-content">
            <div class="page-header-card">
                <div class="page-kicker">CFARM Transport</div>
                <h1 class="page-title">{{ $title ?? 'ระบบบริหารรถขนส่งอาหารไก่' }}</h1>
                @isset($subtitle)
                    <p class="page-subtitle">{{ $subtitle }}</p>
                @endisset
            </div>
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleButtons = document.querySelectorAll('[data-sidebar-toggle]');
    const closeButtons = document.querySelectorAll('[data-sidebar-close]');

    const setSidebarOpen = (isOpen) => {
        document.body.classList.toggle('is-sidebar-open', isOpen);
        toggleButtons.forEach((button) => {
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    };

    toggleButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setSidebarOpen(!document.body.classList.contains('is-sidebar-open'));
        });
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => setSidebarOpen(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setSidebarOpen(false);
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1200) {
            setSidebarOpen(false);
        }
    });
});
</script>
@stack('scripts')
</body>
</html>
