<?php

namespace App\Http\Controllers;

use App\Models\Investigation;
use App\Models\Lab;
use App\Models\User;
use App\Services\QrcodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(string $path, QrcodeService $qrcodeService)
    {
        $investigation = Investigation::with([
            'lab',
            'patient',
            'doctor',
            'labtolab',
            'investigationTests.diagnosticstest.parameters',
            'investigationTests.investigationTestResults.parameter',
            'investigationTests.technician',
            'investigationTests.authenticator',
            'investigationTests.approver',
            'labPayments'
        ])
        ->where('unique_id', $path)
        ->orWhere('id', is_numeric($path) ? (int)$path : 0)
        ->first();

        if (!$investigation) {
            abort(404, 'Diagnostic report not found or invalid QR verification code.');
        }

        $paid = $investigation->labPayments->sum('payment_amount');
        $balance = $investigation->total_amount - ($paid + $investigation->discount);

        if ($balance > 0) {
            return response('<div style="font-family:sans-serif; text-align:center; padding:50px;"><h2>Payment Pending</h2><p>This pathology report will be available online once full payment has been settled.</p></div>', 402);
        }

        $lab = $investigation->lab ?: Lab::find(1);
        $labAdmin = User::where('lab_id', $lab->id)->where('role_id', 2)->first();
        $pathologist = User::where('lab_id', $lab->id)->whereIn('role_id', [6, 7])->first() ?: $labAdmin;
        $qrcodePath = $qrcodeService->create($investigation->unique_id ?: (string)$investigation->id);

        $pdf = Pdf::loadView('print.pdfreport', compact('investigation', 'lab', 'labAdmin', 'pathologist', 'qrcodePath'));
        return $pdf->stream('Diagnostic_Report_' . ($investigation->patient->unique_id ?? $investigation->id) . '.pdf');
    }
}
