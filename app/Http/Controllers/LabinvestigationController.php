<?php

namespace App\Http\Controllers;

use App\Models\Diagnosticstest;
use App\Models\Investigation;
use App\Models\InvestigationTest;
use App\Models\InvestigationTestResult;
use App\Models\ItParaValue;
use App\Models\Parameter;
use App\Models\User;
use App\Services\BarcodeService;
use App\Services\QrcodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LabinvestigationController extends Controller
{
    public function __construct()
    {
        view()->share('sidebar', 'labadmin'); // Fallback or adapts based on role
    }

    private function getLabId(): int
    {
        $labId = Auth::user()->lab_id ?? 1;
        return ($labId > 0) ? (int)$labId : 1;
    }

    // Worklist - All Recent Investigations
    public function index()
    {
        $labId = $this->getLabId();
        $samples = Investigation::with([
            'patient', 'doctor', 'labtolab',
            'investigationTests.diagnosticstest.parameters',
            'investigationTests.investigationTestResults',
            'investigationTests.technician',
            'investigationTests.authenticator',
            'investigationTests.approver',
            'labPayments'
        ])
        ->where('lab_id', $labId)
        ->where('status', 1)
        ->latest('id')
        ->paginate(25);

        $viewTitle = "Investigation Worklist";
        return view('labinvestigation.index', compact('samples', 'viewTitle'));
    }

    // Pending Tests Queue (Awaiting technician result entry)
    public function pendingtest()
    {
        $labId = $this->getLabId();
        $samples = Investigation::with([
            'patient', 'doctor', 'labtolab',
            'investigationTests' => function ($q) {
                $q->where('test_by', 0);
            },
            'investigationTests.diagnosticstest.parameters',
            'investigationTests.investigationTestResults',
            'investigationTests.technician',
            'investigationTests.authenticator',
            'investigationTests.approver',
            'labPayments'
        ])
        ->where('lab_id', $labId)
        ->where('status', 1)
        ->whereHas('investigationTests', function ($q) {
            $q->where('test_by', 0);
        })
        ->latest('id')
        ->paginate(25);

        $viewTitle = "Pending Tests Queue (Result Entry)";
        return view('labinvestigation.index', compact('samples', 'viewTitle'));
    }

    // Processed Tests (Results entered, awaiting verification)
    public function processedtests()
    {
        $labId = $this->getLabId();
        $samples = Investigation::with([
            'patient', 'doctor', 'labtolab',
            'investigationTests' => function ($q) {
                $q->where('test_by', '>', 0)->where('authenticated_by', 0)->where('approved_by', 0);
            },
            'investigationTests.diagnosticstest.parameters',
            'investigationTests.investigationTestResults',
            'investigationTests.technician',
            'investigationTests.authenticator',
            'investigationTests.approver',
            'labPayments'
        ])
        ->where('lab_id', $labId)
        ->where('status', 1)
        ->whereHas('investigationTests', function ($q) {
            $q->where('test_by', '>', 0)->where('authenticated_by', 0)->where('approved_by', 0);
        })
        ->latest('id')
        ->paginate(25);

        $viewTitle = "Processed Tests (Awaiting Pathologist Verification)";
        return view('labinvestigation.index', compact('samples', 'viewTitle'));
    }

    // Verified Tests (Pathologist verified, awaiting approval)
    public function verifiedtest()
    {
        $labId = $this->getLabId();
        $samples = Investigation::with([
            'patient', 'doctor', 'labtolab',
            'investigationTests' => function ($q) {
                $q->where('authenticated_by', '>', 0)->where('approved_by', 0);
            },
            'investigationTests.diagnosticstest.parameters',
            'investigationTests.investigationTestResults',
            'investigationTests.technician',
            'investigationTests.authenticator',
            'investigationTests.approver',
            'labPayments'
        ])
        ->where('lab_id', $labId)
        ->where('status', 1)
        ->whereHas('investigationTests', function ($q) {
            $q->where('authenticated_by', '>', 0)->where('approved_by', 0);
        })
        ->latest('id')
        ->paginate(25);

        $viewTitle = "Verified Tests (Awaiting Final Approval)";
        return view('labinvestigation.index', compact('samples', 'viewTitle'));
    }

    // Approved Tests (Finalized)
    public function approvedtests()
    {
        $labId = $this->getLabId();
        $samples = Investigation::with([
            'patient', 'doctor', 'labtolab',
            'investigationTests' => function ($q) {
                $q->where('approved_by', '>', 0);
            },
            'investigationTests.diagnosticstest.parameters',
            'investigationTests.investigationTestResults',
            'investigationTests.technician',
            'investigationTests.authenticator',
            'investigationTests.approver',
            'labPayments'
        ])
        ->where('lab_id', $labId)
        ->where('status', 1)
        ->whereHas('investigationTests', function ($q) {
            $q->where('approved_by', '>', 0);
        })
        ->latest('id')
        ->paginate(25);

        $viewTitle = "Approved & Signed Pathology Reports";
        return view('labinvestigation.index', compact('samples', 'viewTitle'));
    }

    // Collect Specimen / Sample Action
    public function collectsample(Request $request, BarcodeService $barcodeService)
    {
        $labId = $this->getLabId();
        $userId = Auth::id() ?? 1;

        if ($request->isMethod('post')) {
            $request->validate(['investigationtest_id' => 'required|integer']);

            $it = InvestigationTest::whereHas('investigation', function ($q) use ($labId) {
                $q->where('lab_id', $labId);
            })->findOrFail($request->investigationtest_id);

            $it->specimen_by = $userId;
            $it->specimen_time = now();
            $it->status = 2; // Sample collected
            $it->save();

            return response()->json(['status' => 'success', 'message' => 'Sample collected successfully.']);
        }

        // View sample collection barcode scanner page
        $samples = Investigation::with(['patient', 'investigationTests.diagnosticstest'])
            ->where('lab_id', $labId)
            ->where('status', 1)
            ->whereHas('investigationTests', function ($q) {
                $q->where('specimen_by', 0);
            })
            ->latest('id')
            ->paginate(25);

        return view('labinvestigation.collectsample', compact('samples'));
    }

    // Load Parameters for Result Entry Modal
    public function getparameters(Request $request)
    {
        $request->validate([
            'testid' => 'required|integer',
            'invtestid' => 'required|integer',
        ]);

        $labId = $this->getLabId();
        $test = Diagnosticstest::where('lab_id', $labId)->findOrFail($request->testid);
        $invtest = InvestigationTest::with('investigation.patient')->findOrFail($request->invtestid);
        $params = Parameter::where('diagnosticstests_id', $test->id)->where('status', 1)->orderBy('sort', 'ASC')->get();
        $existingResults = InvestigationTestResult::where('investigation_test_id', $invtest->id)->get()->keyBy('parameter_id');

        return view('labinvestigation.getparameters', compact('test', 'invtest', 'params', 'existingResults'));
    }

    // Save Parameter Results & Advance Workflow
    public function updateresult(Request $request, BarcodeService $barcodeService, QrcodeService $qrcodeService)
    {
        $request->validate([
            'invtestid' => 'required|integer',
            'resultid' => 'required|integer',
        ]);

        $labId = $this->getLabId();
        $userId = Auth::id() ?? 1;
        $userRole = Auth::user()->role_id ?? 2;

        $it = InvestigationTest::with(['investigation.patient', 'diagnosticstest'])->findOrFail($request->resultid);

        // Advance lifecycle status depending on role
        if ($userRole == 4) { // Technician
            $it->test_by = $userId;
            $it->test_time = now();
            $it->status = 3;
        } elseif ($userRole == 7) { // Pathologist Authenticator
            $it->authenticated_by = $userId;
            $it->authenticated_time = now();
            $it->status = 4;
        } elseif (in_array($userRole, [2, 6])) { // Approver / Lab Admin
            $it->test_by = $it->test_by ?: $userId;
            $it->test_time = $it->test_time ?: now();
            $it->authenticated_by = $it->authenticated_by ?: $userId;
            $it->authenticated_time = $it->authenticated_time ?: now();
            $it->approved_by = $userId;
            $it->approved_time = now();
            $it->status = 5;
        }

        $it->notes = $request->reportnotes ?? $it->notes;
        $it->highlight = $request->has('highlight') ? 1 : 0;
        $it->save();

        // Save Parameters
        $params = Parameter::where('diagnosticstests_id', $request->diagnostictestid)->where('status', 1)->get();
        if ($params->count() > 0) {
            foreach ($params as $p) {
                $valKey = 'parresultval_' . $p->id . '_' . $request->diagnostictestid;
                $boldKey = 'pardefaultval_' . $p->id . '_' . $request->diagnostictestid;

                $val = $request->input($valKey, '');
                $isBold = $request->has($boldKey) ? 1 : 0;

                InvestigationTestResult::updateOrCreate(
                    ['investigation_test_id' => $it->id, 'parameter_id' => $p->id],
                    [
                        'result' => $val,
                        'is_bold' => $isBold,
                        'sort' => $p->sort ?? 0,
                    ]
                );
            }
        } else {
            // Single test without parameter definitions
            $val = $request->input('parresultval_', '');
            $isBold = $request->has('pardefaultval_') ? 1 : 0;

            InvestigationTestResult::updateOrCreate(
                ['investigation_test_id' => $it->id, 'parameter_id' => 0],
                [
                    'result' => $val,
                    'is_bold' => $isBold,
                    'sort' => 0,
                ]
            );
        }

        return redirect()->back()->with('success', 'Test investigation results updated successfully.');
    }

    // Batch Approve Reports
    public function multireportappove(Request $request)
    {
        $request->validate(['id' => 'required|string']);
        $ids = explode(',', $request->id);
        $userId = Auth::id() ?? 1;

        InvestigationTest::whereIn('id', $ids)->update([
            'approved_by' => $userId,
            'approved_time' => now(),
            'status' => 5,
        ]);

        return response()->json(['error' => 0, 'file' => 'Selected reports approved successfully.']);
    }
}
