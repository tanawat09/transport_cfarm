<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ระบบบริหารรถขนส่งอาหารไก่' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --app-bg: #f5f7fa;
            --panel-bg: #ffffff;
            --panel-border: #dde5ec;
            --panel-shadow: 0 10px 28px rgba(18, 38, 58, 0.06);
            --sidebar-bg: linear-gradient(180deg, #1f9d55 0%, #157347 100%);
            --sidebar-muted: rgba(244, 255, 248, 0.78);
            --topbar-bg: #ffffff;
            --text-main: #182635;
            --text-soft: #647386;
            --accent: #1f9d55;
            --accent-dark: #157347;
            --accent-soft: #e8f7ef;
            --warning-soft: #fff7e6;
            --radius-lg: 10px;
            --radius-md: 8px;
            --radius-sm: 6px;
        }

        html, body {
            min-height: 100%;
        }

        body {
            margin: 0;
            color: var(--text-main);
            background: var(--app-bg);
            font-family: "Segoe UI", Tahoma, sans-serif;
            overflow-x: hidden;
        }

        .app-topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--panel-border);
            box-shadow: 0 6px 18px rgba(18, 38, 58, 0.04);
        }

        .app-topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .app-menu-toggle {
            display: none;
            width: 42px;
            height: 42px;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--panel-border);
            border-radius: var(--radius-md);
            background: #fff;
            color: var(--text-main);
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

        .app-topbar-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .app-shell {
            padding: 20px;
        }

        .app-grid {
            display: grid;
            grid-template-columns: 270px minmax(0, 1fr);
            gap: 20px;
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
            padding: 16px;
            border-radius: var(--radius-lg);
            color: #fff;
            background: var(--sidebar-bg);
            box-shadow: 0 12px 30px rgba(18, 38, 58, 0.14);
            display: flex;
            flex-direction: column;
        }

        .app-sidebar-close {
            display: none;
        }

        .sidebar-heading {
            margin-bottom: 12px;
            padding: 4px 4px 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            text-decoration: none;
        }

        .sidebar-brand img {
            width: 58px;
            height: auto;
            flex: 0 0 auto;
        }

        .sidebar-brand-text {
            min-width: 0;
        }

        .sidebar-heading strong {
            display: block;
            font-size: 1rem;
            font-weight: 800;
        }

        .sidebar-heading span {
            color: var(--sidebar-muted);
            font-size: .82rem;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1 1 auto;
        }

        .sidebar-footer {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            padding: 10px 12px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar-user-icon {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            font-size: 1rem;
            flex: 0 0 auto;
        }

        .sidebar-user-text {
            min-width: 0;
        }

        .sidebar-user-name,
        .sidebar-user-role {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-name {
            color: #fff;
            font-size: .92rem;
            font-weight: 700;
        }

        .sidebar-user-role {
            color: var(--sidebar-muted);
            font-size: .78rem;
        }

        .sidebar-logout {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 42px;
            padding: 9px 10px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.9);
            font-weight: 700;
            text-align: left;
        }

        .sidebar-logout:hover,
        .sidebar-logout:focus {
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
        }

        .sidebar-section-label {
            margin: 14px 8px 6px;
            color: var(--sidebar-muted);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 42px;
            padding: 9px 10px;
            border-radius: var(--radius-md);
            color: rgba(255, 255, 255, 0.92);
            text-decoration: none;
            font-weight: 600;
            transition: .18s ease;
        }

        .sidebar-nav .nav-icon {
            width: 22px;
            display: inline-flex;
            justify-content: center;
            color: rgba(244, 255, 248, 0.82);
            font-size: 1rem;
            flex: 0 0 auto;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.16);
            transform: translateX(1px);
        }

        .sidebar-nav .nav-link.active .nav-icon {
            color: #fff;
        }

        .sidebar-subnav {
            display: flex;
            flex-direction: column;
            gap: 3px;
            margin: 0 0 4px 32px;
            padding-left: 10px;
            border-left: 1px solid rgba(255, 255, 255, 0.12);
        }

        .sidebar-subnav .nav-link {
            min-height: 34px;
            padding: 6px 9px;
            font-size: .86rem;
            color: rgba(244, 255, 248, 0.82);
        }

        .page-header-card {
            margin-bottom: 18px;
            padding: 18px 20px;
            border: 1px solid var(--panel-border);
            border-radius: var(--radius-lg);
            background: var(--panel-bg);
            box-shadow: var(--panel-shadow);
        }

        .page-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            padding: 5px 9px;
            border-radius: var(--radius-sm);
            background: var(--accent-soft);
            color: var(--accent-dark);
            font-size: .76rem;
            font-weight: 700;
        }

        .page-title {
            margin: 0;
            font-size: 1.55rem;
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
            background: #fbfcfd;
            padding: .9rem 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .stat-card,
        .metric-card {
            border: 1px solid var(--panel-border);
            border-radius: var(--radius-md);
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(18, 38, 58, 0.04);
        }

        .btn {
            border-radius: var(--radius-md);
            font-weight: 700;
            padding: .55rem .9rem;
        }

        .btn-sm {
            border-radius: var(--radius-sm);
            padding: .4rem .7rem;
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
            border-radius: var(--radius-md);
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
            background: #f7fafc;
            white-space: nowrap;
        }

        .table > :not(caption) > * > * {
            padding: .78rem .85rem;
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
            border-radius: var(--radius-lg);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
        }

        .pagination {
            gap: 6px;
        }

        .page-link {
            border: 1px solid rgba(148, 163, 184, 0.25);
            border-radius: var(--radius-sm);
            color: #334155;
        }

        .badge {
            border-radius: var(--radius-sm);
            font-weight: 700;
        }

        .btn-outline-light {
            border-color: #cfd8e1;
            color: var(--text-main);
        }

        .btn-outline-light:hover,
        .btn-outline-light:focus {
            border-color: var(--accent);
            background: var(--accent);
            color: #fff;
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
                border-radius: 0 var(--radius-lg) var(--radius-lg) 0;
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
                border-radius: var(--radius-md);
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

            .app-shell {
                padding: 14px;
            }

            .app-grid {
                gap: 14px;
            }

            .page-header-card {
                margin-bottom: 16px;
                padding: 16px;
                border-radius: var(--radius-lg);
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
                border-radius: var(--radius-lg);
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
            .app-topbar-title {
                font-size: .92rem;
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
        <button type="button" class="app-menu-toggle" data-sidebar-toggle aria-label="เปิดเมนูการทำงาน" aria-controls="app-sidebar" aria-expanded="false">
            <span></span>
        </button>
        @unless(request()->routeIs('dashboard'))
            <div class="app-topbar-title">{{ $title ?? 'ระบบบริหารรถขนส่งอาหารไก่' }}</div>
        @endunless
    </div>
</nav>
<div class="app-sidebar-overlay" data-sidebar-close></div>
<div class="app-shell">
    <div class="app-grid">
        <aside class="app-sidebar" id="app-sidebar">
            <button type="button" class="app-sidebar-close" data-sidebar-close aria-label="ปิดเมนูการทำงาน">&times;</button>
            <div class="sidebar-heading">
                <a class="sidebar-brand" href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/cfarm-logo.png') }}" alt="CFARM">
                    <div class="sidebar-brand-text">
                        <strong>ระบบบริหารรถขนส่งอาหารไก่</strong>
                        <span>เมนูการทำงาน</span>
                    </div>
                </a>
            </div>
            <nav class="sidebar-nav">
                <div class="sidebar-section-label">Overview</div>
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 nav-icon"></i><span>ภาพรวมระบบ</span></a>

                <div class="sidebar-section-label">Operations</div>
                <a class="nav-link {{ request()->routeIs('transport-jobs.*') || request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('transport-jobs.create') }}"><i class="bi bi-truck nav-icon"></i><span>บันทึกเที่ยวขนส่ง</span></a>
                <div class="sidebar-subnav">
                    <a class="nav-link {{ request()->routeIs('transport-jobs.index') || request()->routeIs('transport-jobs.show') || request()->routeIs('transport-jobs.edit') ? 'active' : '' }}" href="{{ route('transport-jobs.index') }}">รายการเที่ยวขนส่ง</a>
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">รายงาน</a>
                </div>
                <a class="nav-link {{ request()->routeIs('vehicle-usage-logs.*') ? 'active' : '' }}" href="{{ route('vehicle-usage-logs.index') }}"><i class="bi bi-journal-check nav-icon"></i><span>บันทึกการใช้รถ</span></a>
                  <a class="nav-link {{ request()->routeIs('tractor-usage-inspections.*') || request()->routeIs('tractor-usage-checklist-items.*') ? 'active' : '' }}" href="{{ route('tractor-usage-inspections.index') }}"><i class="bi bi-clipboard2-check nav-icon"></i><span>ตรวจเช็กรถไถ</span></a>
                  @if(auth()->user()?->isAdmin())
                      <div class="sidebar-subnav">
                          <a class="nav-link {{ request()->routeIs('tractor-usage-checklist-items.*') ? 'active' : '' }}" href="{{ route('tractor-usage-checklist-items.index') }}">ตั้งค่ารายการตรวจรถไถ</a>
                      </div>
                  @endif
                <a class="nav-link {{ request()->routeIs('pre-trip-inspections.*') ? 'active' : '' }}" href="{{ route('pre-trip-inspections.index') }}"><i class="bi bi-ui-checks-grid nav-icon"></i><span>ตรวจเช็กรถก่อนวิ่ง</span></a>
                @if(auth()->user()?->isAdmin())
                    <div class="sidebar-subnav">
                        <a class="nav-link {{ request()->routeIs('pre-trip-checklist-items.*') ? 'active' : '' }}" href="{{ route('pre-trip-checklist-items.index') }}">ตั้งค่ารายการตรวจเช็ก</a>
                    </div>
                @endif
                <a class="nav-link {{ request()->routeIs('tire-registrations.index') || request()->routeIs('tire-registrations.report') ? 'active' : '' }}" href="{{ route('tire-registrations.index') }}"><i class="bi bi-record-circle nav-icon"></i><span>การจัดการยาง</span></a>
                <div class="sidebar-subnav">
                    <a class="nav-link {{ request()->routeIs('tire-registrations.report') ? 'active' : '' }}" href="{{ route('tire-registrations.report') }}">รายงานยางใกล้เปลี่ยน</a>
                </div>
                @if(auth()->user()?->isAdmin())
                    <div class="sidebar-section-label">Admin</div>
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-people nav-icon"></i><span>จัดการผู้ใช้</span></a>
                    <a class="nav-link {{ request()->routeIs('vehicle-documents.*') ? 'active' : '' }}" href="{{ route('vehicle-documents.index') }}"><i class="bi bi-file-earmark-text nav-icon"></i><span>ทะเบียน พ.ร.บ. ประกัน</span></a>
                    <a class="nav-link {{ request()->routeIs('route-standards.*') ? 'active' : '' }}" href="{{ route('route-standards.index') }}"><i class="bi bi-signpost-2 nav-icon"></i><span>มาตรฐานเส้นทาง</span></a>
                    <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}"><i class="bi bi-truck-front nav-icon"></i><span>จัดการรถ</span></a>
                    <a class="nav-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}" href="{{ route('drivers.index') }}"><i class="bi bi-person-vcard nav-icon"></i><span>จัดการพนักงานขับ</span></a>
                    <a class="nav-link {{ request()->routeIs('farms.*') ? 'active' : '' }}" href="{{ route('farms.index') }}"><i class="bi bi-buildings nav-icon"></i><span>จัดการฟาร์ม</span></a>
                    <a class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}" href="{{ route('vendors.index') }}"><i class="bi bi-briefcase nav-icon"></i><span>จัดการคู่สัญญา</span></a>
                    <a class="nav-link {{ request()->routeIs('telegram-settings.*') ? 'active' : '' }}" href="{{ route('telegram-settings.edit') }}"><i class="bi bi-send nav-icon"></i><span>จัดการ Telegram</span></a>
                @endif
            </nav>
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="sidebar-user-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="sidebar-user-text">
                        <span class="sidebar-user-name">{{ auth()->user()->name ?? '' }}</span>
                        <span class="sidebar-user-role">{{ auth()->user()->role ?? '' }}</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-logout">
                        <i class="bi bi-box-arrow-right nav-icon"></i>
                        <span>ออกจากระบบ</span>
                    </button>
                </form>
            </div>
        </aside>
        <main class="app-content">
            @unless(request()->routeIs('dashboard') || request()->routeIs('reports.*') || request()->routeIs('tractor-usage-inspections.index'))
                <div class="page-header-card">
                    <div class="page-kicker">CFARM Transport</div>
                    <h1 class="page-title">{{ $title ?? 'ระบบบริหารรถขนส่งอาหารไก่' }}</h1>
                    @isset($subtitle)
                        <p class="page-subtitle">{{ $subtitle }}</p>
                    @endisset
                </div>
            @endunless
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
