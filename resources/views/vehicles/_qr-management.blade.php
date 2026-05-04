<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start">
            <div>
                <div class="text-muted small mb-2">สถานะการเข้าถึงแบบ Public</div>
                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                    <span class="badge {{ $qrToken->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                        {{ $qrToken->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                    </span>
                    <span class="badge text-bg-dark text-uppercase">{{ $qrToken->access_type }}</span>
                </div>
                <div class="small text-muted">PIN ปัจจุบัน: <strong>{{ $qrToken->decryptedPin() }}</strong></div>
                <div class="small text-muted">ลิงก์ public: {{ $qrToken->publicUrl() }}</div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <form method="POST" action="{{ route('vehicles.qr-token.toggle', [$vehicle, $qrToken->access_type]) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $qrToken->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                        {{ $qrToken->is_active ? 'ปิดใช้งาน' : 'เปิดใช้งาน' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('vehicles.qr-token.regenerate', [$vehicle, $qrToken->access_type]) }}" onsubmit="return confirm('ยืนยันการเปลี่ยน QR และ PIN ใหม่?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning">เปลี่ยน QR ใหม่</button>
                </form>
            </div>
        </div>

        @if($recentQrLogs->isNotEmpty())
            <div class="table-responsive mt-4">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>เวลา</th>
                            <th>เหตุการณ์</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($recentQrLogs as $log)
                        <tr>
                            <td>{{ optional($log->happened_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->event_type }}</td>
                            <td>{{ $log->ip_address ?: '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
