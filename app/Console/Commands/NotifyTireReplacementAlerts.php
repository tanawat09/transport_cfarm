<?php

namespace App\Console\Commands;

use App\Services\TireAlertService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class NotifyTireReplacementAlerts extends Command
{
    protected $signature = 'tire-registrations:notify-replacement-alerts';

    protected $description = 'Send Telegram alerts for tires that are near replacement or due for replacement.';

    public function __construct(
        protected TireAlertService $tireAlertService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (! $token || ! $chatId) {
            $this->error('Missing TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID in .env');
            return self::FAILURE;
        }

        $rows = $this->tireAlertService->reportRows()
            ->whereIn('alert_code', ['warning', 'replace'])
            ->values();

        if ($rows->isEmpty()) {
            $this->info('No tire replacement alerts found.');
            return self::SUCCESS;
        }

        $lines = [
            'แจ้งเตือนยางใกล้เปลี่ยน / ถึงกำหนดเปลี่ยน',
            'วันที่ตรวจ: ' . now()->format('d/m/Y H:i'),
            '',
        ];

        foreach ($rows as $row) {
            $registration = $row['registration'];
            $vehicle = $row['vehicle'];
            $remainingDistance = $row['remaining_distance_km'];

            $distanceText = $remainingDistance === null
                ? 'ไม่สามารถคำนวณระยะคงเหลือ'
                : ($remainingDistance <= 0
                    ? 'เกินกำหนด ' . number_format(abs($remainingDistance), 2) . ' กม.'
                    : 'เหลือ ' . number_format($remainingDistance, 2) . ' กม.');

            $lines[] = sprintf(
                '- %s | %s | %s | %s | %s',
                $vehicle?->registration_number ?? '-',
                $registration->tire_position,
                $registration->tire_serial_number,
                $row['alert_label'],
                $distanceText
            );
        }

        $response = Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => implode("\n", $lines),
        ]);

        if (! $response->successful()) {
            $this->error('Telegram send failed: ' . $response->body());
            return self::FAILURE;
        }

        $this->info('Telegram notification sent for ' . $rows->count() . ' tire alert(s).');

        return self::SUCCESS;
    }
}
