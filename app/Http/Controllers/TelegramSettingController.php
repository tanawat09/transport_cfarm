<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TelegramSettingController extends Controller
{
    public function edit(): View
    {
        return view('telegram-settings.edit', [
            'botToken' => env('TELEGRAM_BOT_TOKEN', ''),
            'chatId' => env('TELEGRAM_CHAT_ID', ''),
            'alertDays' => env('VEHICLE_DOCUMENT_ALERT_DAYS', 30),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'telegram_bot_token' => ['nullable', 'string', 'max:255'],
            'telegram_chat_id' => ['nullable', 'string', 'max:100'],
            'vehicle_document_alert_days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $this->writeEnvValues([
            'TELEGRAM_BOT_TOKEN' => $validated['telegram_bot_token'] ?? '',
            'TELEGRAM_CHAT_ID' => $validated['telegram_chat_id'] ?? '',
            'VEHICLE_DOCUMENT_ALERT_DAYS' => (string) $validated['vehicle_document_alert_days'],
        ]);

        return back()->with('success', 'บันทึกการตั้งค่า Telegram เรียบร้อยแล้ว');
    }

    public function test(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'telegram_bot_token' => ['nullable', 'string', 'max:255'],
            'telegram_chat_id' => ['nullable', 'string', 'max:100'],
        ]);

        $token = $validated['telegram_bot_token'] ?: env('TELEGRAM_BOT_TOKEN');
        $chatId = $validated['telegram_chat_id'] ?: env('TELEGRAM_CHAT_ID');

        if (! $token || ! $chatId) {
            return back()->with('error', 'กรุณากรอก Bot Token และ Chat ID ก่อนส่งข้อความทดสอบ');
        }

        $response = Http::asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => "ทดสอบแจ้งเตือนจากระบบบริหารรถขนส่งอาหารไก่\nเวลา: " . now()->format('d/m/Y H:i'),
        ]);

        if (! $response->successful()) {
            return back()->with('error', 'ส่งข้อความทดสอบไม่สำเร็จ: ' . Str::limit($response->body(), 160));
        }

        return back()->with('success', 'ส่งข้อความทดสอบ Telegram สำเร็จ');
    }

    private function writeEnvValues(array $values): void
    {
        $envPath = base_path('.env');
        $content = file_exists($envPath) ? file_get_contents($envPath) : '';

        foreach ($values as $key => $value) {
            $line = $key . '=' . $this->formatEnvValue($value);

            if (preg_match("/^{$key}=.*$/m", $content)) {
                $content = preg_replace("/^{$key}=.*$/m", $line, $content);
                continue;
            }

            $content = rtrim($content) . PHP_EOL . $line . PHP_EOL;
        }

        file_put_contents($envPath, $content);
    }

    private function formatEnvValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (preg_match('/\s|#|"|\'/', $value)) {
            return '"' . str_replace('"', '\"', $value) . '"';
        }

        return $value;
    }
}
