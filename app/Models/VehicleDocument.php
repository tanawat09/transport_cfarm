<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'document_type',
        'document_no',
        'provider_name',
        'insurance_capital',
        'issued_at',
        'expires_at',
        'tax_expires_at',
        'compulsory_expires_at',
        'insurance_expires_at',
        'alert_before_days',
        'is_alert_enabled',
        'notes',
        'last_telegram_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
            'tax_expires_at' => 'date',
            'compulsory_expires_at' => 'date',
            'insurance_expires_at' => 'date',
            'insurance_capital' => 'decimal:2',
            'alert_before_days' => 'integer',
            'is_alert_enabled' => 'boolean',
            'last_telegram_notified_at' => 'datetime',
        ];
    }

    public static function typeOptions(): array
    {
        return [
            'registration_tax' => 'ทะเบียน/ภาษี',
            'compulsory_insurance' => 'พ.ร.บ.',
            'insurance' => 'ประกันภัย',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function typeLabel(): string
    {
        return self::typeOptions()[$this->document_type] ?? $this->document_type;
    }

    public function daysUntilExpiry(): int
    {
        return now()->startOfDay()->diffInDays($this->expires_at->startOfDay(), false);
    }

    public function statusLabel(): string
    {
        $days = $this->daysUntilExpiry();

        if ($days < 0) {
            return 'หมดอายุแล้ว';
        }

        if ($days <= $this->alert_before_days) {
            return 'ใกล้หมดอายุ';
        }

        return 'ปกติ';
    }

    public function statusBadgeClass(): string
    {
        return match ($this->statusLabel()) {
            'หมดอายุแล้ว' => 'text-bg-danger',
            'ใกล้หมดอายุ' => 'text-bg-warning',
            default => 'text-bg-success',
        };
    }

    public function shouldNotify(?CarbonInterface $today = null): bool
    {
        $today ??= now()->startOfDay();
        $days = $today->diffInDays($this->expires_at->startOfDay(), false);

        return $this->is_alert_enabled && $days <= $this->alert_before_days;
    }
}
