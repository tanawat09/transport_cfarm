<?php

namespace App\Http\Controllers;

use App\Exports\TransportJobsReportExport;
use App\Http\Requests\ReportFilterRequest;
use App\Models\Driver;
use App\Models\Farm;
use App\Models\Vehicle;
use App\Models\Vendor;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService)
    {
    }

    public function index(ReportFilterRequest $request): View
    {
        $filters = $request->validated() + [
            'start_date' => $request->input('start_date', now()->startOfMonth()->toDateString()),
            'end_date' => $request->input('end_date', now()->toDateString()),
        ];

        $query = $this->reportService->query($filters);

        return view('reports.index', [
            'filters' => $filters,
            'jobs' => $query->paginate(30)->withQueryString(),
            'summary' => $this->reportService->summary($query),
            'vehicles' => Vehicle::query()->orderBy('registration_number')->get(),
            'drivers' => Driver::query()->orderBy('full_name')->get(),
            'farms' => Farm::query()->orderBy('farm_name')->get(),
            'vendors' => Vendor::query()->orderBy('vendor_name')->get(),
        ]);
    }

    public function exportExcel(ReportFilterRequest $request)
    {
        $filters = $request->validated();
        $fileName = 'transport-report-'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new TransportJobsReportExport($filters, $this->reportService), $fileName);
    }

    public function exportPdf(ReportFilterRequest $request): Response
    {
        $filters = $request->validated();
        $query = $this->reportService->query($filters);

        $pdf = Pdf::loadView('reports.pdf', [
            'filters' => $filters,
            'jobs' => $query->get(),
            'summary' => $this->reportService->summary($query),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('transport-report-'.now()->format('Ymd_His').'.pdf');
    }
}
