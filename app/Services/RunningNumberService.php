<?php

namespace App\Services;

use App\Models\TransportJob;
use Carbon\Carbon;

class RunningNumberService
{
    public function generateTransportDocumentNo(string|Carbon $transportDate): string
    {
        $date = $transportDate instanceof Carbon ? $transportDate : Carbon::parse($transportDate);
        $prefix = 'TRN-'.$date->format('Ymd');

        $latest = TransportJob::query()
            ->where('document_no', 'like', $prefix.'-%')
            ->withTrashed()
            ->latest('id')
            ->value('document_no');

        $nextNumber = 1;

        if ($latest) {
            $lastSequence = (int) substr($latest, -4);
            $nextNumber = $lastSequence + 1;
        }

        return sprintf('%s-%04d', $prefix, $nextNumber);
    }
}
