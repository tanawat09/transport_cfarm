<?php

namespace App\Console\Commands;

use App\Models\VehicleDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class NotifyExpiringVehicleDocuments extends Command
{
    protected $signature = 'vehicle-documents:notify-expiring {--days= : Override alert window in days}';

    protected $description = 'Send Telegram alerts for expiring vehicle documents.';

    public function handle(): int
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');
        $daysOverride = $this->option('days');

        if (! $token || ! $chatId) {
            $this->error('Missing TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID in .env');
            return self::FAILURE;
        }

        $documents = VehicleDocument::query()
            ->with('vehicle')
            ->where('is_alert_enabled', true)
            ->where(function ($query) use ($daysOverride) {
                if ($daysOverride) {
                    $query->whereDate('expires_at', '<=', now()->addDays((int) $daysOverride)->toDateString());
                    return;
                }

                $query->whereRaw('DATEDIFF(expires_at, CURDATE()) <= alert_before_days');
            })
            ->orderBy('expires_at')
            ->get();

        if ($documents->isEmpty()) {
            $this->info('No expiring vehicle documents found.');
            return self::SUCCESS;
        }

        $lines = [
            'แจ้งเตือนเอกสารรถใกล้หมดอายุ',
            'วันที่ตรวจ: ' . now()->format('d/m/Y H:i'),
            '',
        ];

        foreach ($documents as $document) {
            $days = $document->daysUntilExpiry();
            $dayLabel = $days < 0 ? 'หมดอายุแล้ว ' . abs($days) . ' วัน' : 'เหลือ ' . $days . ' วัน';
            $lines[] = sprintf(
                '- %s | %s | หมดอายุ %s | %s',
                $document->vehicle?->registration_number ?? '-',
                $document->typeLabel(),
                $document->expires_at?->format('d/m/Y'),
                $dayLabel
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

        VehicleDocument::query()
            ->whereIn('id', $documents->pluck('id'))
            ->update(['last_telegram_notified_at' => now()]);

        $this->info('Telegram notification sent for ' . $documents->count() . ' document(s).');

        return self::SUCCESS;
    }
}
