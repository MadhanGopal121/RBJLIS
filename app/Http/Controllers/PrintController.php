<?php

namespace App\Http\Controllers;

use App\Models\Investigation;
use App\Models\InvestigationTest;
use App\Models\Lab;
use App\Models\User;
use App\Services\BarcodeService;
use App\Services\QrcodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintController extends Controller
{
    // Printable Thermal / A4 Receipt
    public function printbill(Request $request, QrcodeService $qrcodeService)
    {
        $request->validate(['id' => 'required|integer']);

        $bill = Investigation::with([
            'lab',
            'patient',
            'doctor',
            'labtolab',
            'investigationTests.diagnosticstest',
            'labPayments',
        ])->findOrFail($request->id);

        $lab = $bill->lab ?: Lab::find(1);

        $amountToPay = $bill->balance_amount > 0 ? (float)$bill->balance_amount : (float)$bill->total_amount;
        $vpa = $lab->upi_id ?: 'rbjlab@upi';
        $payeeName = $lab->upi_name ?: $lab->name;
        $transRef = 'INV' . $bill->id;
        $upiQrPath = $qrcodeService->generateUpiQr($vpa, $payeeName, $amountToPay, $transRef, 'INV-' . $bill->id);

        return view('print.printbill', compact('bill', 'lab', 'upiQrPath', 'vpa', 'amountToPay'));
    }

    // PDF / Printable Diagnostic Test Report
    public function printreport(Request $request, QrcodeService $qrcodeService)
    {
        $request->validate(['id' => 'required|integer']);

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
        ])->findOrFail($request->id);

        $lab = $investigation->lab ?: Lab::find(1);

        // Pathologist / In-charge staff
        $labAdmin = User::where('lab_id', $lab->id)->where('role_id', 2)->first();
        $pathologist = User::where('lab_id', $lab->id)->whereIn('role_id', [6, 7])->first() ?: $labAdmin;

        // Generate QR code for report authenticity
        $qrData = url('/report/' . ($investigation->unique_id ?: $investigation->id));
        $qrcodePath = $qrcodeService->create($investigation->unique_id ?: (string)$investigation->id);

        if ($request->has('pdf') && $request->pdf == '1') {
            $pdf = Pdf::loadView('print.pdfreport', compact('investigation', 'lab', 'labAdmin', 'pathologist', 'qrcodePath'));
            return $pdf->stream('Report_' . ($investigation->patient->unique_id ?? $investigation->id) . '.pdf');
        }

        return view('print.printreport', compact('investigation', 'lab', 'labAdmin', 'pathologist', 'qrcodePath'));
    }
}
