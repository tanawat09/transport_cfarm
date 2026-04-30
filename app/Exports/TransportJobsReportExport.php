<?php

namespace App\Exports;

use App\Services\ReportService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransportJobsReportExport implements FromView
{
    public function __construct(
        protected array $filters,
        protected ReportService $reportService,
    ) {
    }

    public function view(): View
    {
        $query = $this->reportService->query($this->filters);

        return view('reports.export', [
            'filters' => $this->filters,
            'jobs' => $query->get(),
            'summary' => $this->reportService->summary($query),
        ]);
    }
}
