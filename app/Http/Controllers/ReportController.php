<?php

namespace App\Http\Controllers;

use App\Services\ReportDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private ReportDataService $reports
    ) {}

    public function index(
        Request $request
    ): View {
        return view(
            'reports.index',
            $this->reports->build(
                $request
            )
        );
    }
}
