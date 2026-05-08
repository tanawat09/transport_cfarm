@extends('layouts.app')

@php
    $title = 'ตรวจเช็กรถก่อนวิ่ง';
    $subtitle = 'บันทึกผลตรวจเช็กก่อนออกรถให้ครบในหน้าเดียว พร้อมสรุปความพร้อมของรถแบบทันทีเพื่อใช้งานหน้างานได้ง่ายขึ้น';
@endphp

@push('styles')
<style>
    .inspection-locked-banner {
        margin-bottom: 1rem;
        padding: 1rem 1.1rem;
        border: 1px solid rgba(59, 130, 246, 0.16);
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.09) 0%, rgba(14, 165, 233, 0.06) 100%);
        color: #17324d;
    }

    .inspection-locked-banner strong {
        color: #0f3c68;
    }

    .inspection-form-card .card-body {
        padding: 1.4rem;
    }

    .inspection-form-card {
        overflow: visible;
    }

    .inspection-form-card > .card-body {
        overflow: visible;
    }

    .inspection-form-card form {
        min-width: 0;
    }

    .inspection-action-card {
        margin-top: 1.15rem;
    }

    .inspection-action-card .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.2rem;
    }

    .inspection-action-text strong {
        display: block;
        font-size: 1rem;
        font-weight: 800;
        color: #17324d;
    }

    .inspection-action-text span {
        color: #708092;
        font-size: .9rem;
    }

    .inspection-action-buttons {
        display: inline-flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .inspection-action-buttons .btn-primary {
        min-width: 170px;
    }

    @media (max-width: 991.98px) {
        .inspection-form-card .card-body {
            padding: 1rem;
        }

        .inspection-action-card .card-body {
            flex-direction: column;
            align-items: stretch;
        }

        .inspection-action-buttons {
            justify-content: stretch;
        }

        .inspection-action-buttons .btn {
            width: 100%;
        }
    }

    @media (max-width: 767.98px) {
        .inspection-locked-banner {
            padding: .9rem 1rem;
            border-radius: 16px;
            font-size: .92rem;
        }

        .inspection-form-card {
            border-radius: 18px;
        }

        .inspection-form-card .card-body {
            padding: .85rem;
        }

        .inspection-action-card {
            position: sticky;
            bottom: calc(.75rem + env(safe-area-inset-bottom));
            z-index: 20;
            margin-left: -.15rem;
            margin-right: -.15rem;
        }

        .inspection-action-card .card-body {
            padding: .85rem;
            border-radius: 18px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.18);
        }

        .inspection-action-text strong {
            font-size: .95rem;
        }

        .inspection-action-text span {
            font-size: .84rem;
        }

        .inspection-action-buttons {
            gap: .55rem;
        }

        .inspection-action-buttons .btn {
            min-height: 50px;
            font-size: .96rem;
        }

        .inspection-action-buttons .btn-primary {
            min-width: 0;
        }

        .inspection-action-buttons .btn-outline-secondary {
            order: 2;
        }
    }

    @media (max-width: 575.98px) {
        .inspection-locked-banner {
            margin-bottom: .8rem;
            padding: .8rem;
            border-radius: 14px;
        }

        .inspection-form-card {
            margin-left: -4px;
            margin-right: -4px;
            border-radius: 16px;
        }

        .inspection-form-card .card-body {
            padding: .65rem;
        }

        .inspection-action-card {
            bottom: calc(.5rem + env(safe-area-inset-bottom));
        }

        .inspection-action-card .card-body {
            padding: .75rem;
        }
    }
</style>
@endpush

@section('content')
@if(!empty($lockedVehicle))
    <div class="inspection-locked-banner">
        เปิดฟอร์มจาก QR Code ของรถ <strong>{{ $lockedVehicle->registration_number }}</strong> ระบบล็อกทะเบียนคันนี้ไว้ให้แล้วเพื่อความถูกต้องในการบันทึก
    </div>
@endif

<div class="card inspection-form-card">
    <div class="card-body">
        <form method="POST" action="{{ route('pre-trip-inspections.store') }}" class="d-grid gap-3">
            @csrf
            @include('pre-trip-inspections._form')

            <div class="card inspection-action-card">
                <div class="card-body">
                    <div class="inspection-action-text">
                        <strong>ตรวจทานอีกครั้งก่อนบันทึก</strong>
                        <span>เลือกผลตรวจให้ครบทุกข้อและเติมหมายเหตุในหัวข้อที่ไม่ผ่าน เพื่อให้ข้อมูลพร้อมใช้งานต่อทันที</span>
                    </div>
                    <div class="inspection-action-buttons">
                        <a href="{{ route('pre-trip-inspections.index') }}" class="btn btn-outline-secondary">กลับไปหน้ารายการ</a>
                        <button class="btn btn-primary" type="submit">บันทึกผลตรวจเช็ก</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
