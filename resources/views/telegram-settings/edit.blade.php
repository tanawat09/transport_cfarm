@extends('layouts.app')

@php
    $title = 'จัดการ Telegram';
    $subtitle = 'ตั้งค่าการแจ้งเตือนเอกสารทะเบียน พ.ร.บ. และประกันหมดอายุผ่าน Telegram';
@endphp

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('telegram-settings.update') }}" class="row g-3">
                    @csrf

                    <div class="col-12">
                        <label for="telegram_bot_token" class="form-label">Bot Token</label>
                        <input type="password" name="telegram_bot_token" id="telegram_bot_token" value="{{ old('telegram_bot_token', $botToken) }}" class="form-control" autocomplete="off" placeholder="เช่น 123456789:AA...">
                        <div class="form-text">สร้างจาก BotFather แล้วนำ token มากรอกที่นี่</div>
                    </div>

                    <div class="col-12">
                        <label for="telegram_chat_id" class="form-label">Chat ID</label>
                        <input type="text" name="telegram_chat_id" id="telegram_chat_id" value="{{ old('telegram_chat_id', $chatId) }}" class="form-control" placeholder="เช่น -1001234567890 หรือ 123456789">
                    </div>

                    <div class="col-md-6">
                        <label for="vehicle_document_alert_days" class="form-label">แจ้งเตือนก่อนหมดอายุ (วัน)</label>
                        <input type="number" name="vehicle_document_alert_days" id="vehicle_document_alert_days" value="{{ old('vehicle_document_alert_days', $alertDays) }}" class="form-control" min="1" max="365" required>
                    </div>

                    <div class="col-12 d-flex flex-wrap gap-2">
                        <button class="btn btn-primary">บันทึกการตั้งค่า</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h2 class="h5 mb-3">ส่งข้อความทดสอบ</h2>
                <p class="text-muted">ใช้สำหรับตรวจว่า Bot Token และ Chat ID ถูกต้องก่อนเปิดใช้งานแจ้งเตือนจริง</p>

                <form method="POST" action="{{ route('telegram-settings.test') }}" class="d-grid gap-3">
                    @csrf
                    <input type="hidden" name="telegram_bot_token" value="{{ old('telegram_bot_token', $botToken) }}">
                    <input type="hidden" name="telegram_chat_id" value="{{ old('telegram_chat_id', $chatId) }}">
                    <button class="btn btn-outline-success">ส่งข้อความทดสอบ</button>
                </form>

                <hr>

                <div class="small text-muted">
                    ระบบจะรันแจ้งเตือนอัตโนมัติผ่านคำสั่ง
                    <code>vehicle-documents:notify-expiring</code>
                    ตาม schedule เวลา 08:00 น.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
