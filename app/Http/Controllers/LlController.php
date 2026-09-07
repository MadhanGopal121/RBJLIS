<?php

namespace App\Http\Controllers;

use App\Models\Diagnosticstest;
use App\Models\Investigation;
use App\Models\InvestigationTest;
use App\Models\LabPayment;
use App\Models\LabSpecialprice;
use App\Models\Labtolab;
use App\Models\Patient;
use App\Models\Profile;
use App\Models\ProfileTest;
use App\Services\BarcodeService;
use App\Services\ImageService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LlController extends Controller
{
    public function __construct()
    {
        view()->share('sidebar', 'll');
    }

    private function getLabId(): int
    {
        return Auth::user()->lab_id ?? 1;
    }

    private function getLtolId(): int
    {
        return Auth::user()->ltol ?? 1;
    }

    public function index()
    {
        $labId = $this->getLabId();
        $ltolId = $this->getLtolId();

        $totalBilled = Investigation::where('lab_id', $labId)->where('ltol_id', $ltolId)->where('status', 1)->sum('total_amount');
        $totalPaid = LabPayment::where('lab_id', $labId)->where('labtolab_id', $ltolId)->sum('payment_amount');
        $dueBalance = $totalBilled - $totalPaid;

        $recentOrders = Investigation::with(['patient', 'investigationTests.diagnosticstest'])
            ->where('lab_id', $labId)
            ->where('ltol_id', $ltolId)
            ->where('status', 1)
            ->latest('id')
            ->take(10)
            ->get();

        return view('ll.index', compact('totalBilled', 'totalPaid', 'dueBalance', 'recentOrders'));
    }

    public function createbill()
    {
        $labId = $this->getLabId();
        $ltolId = $this->getLtolId();

        $tests = Diagnosticstest::where('lab_id', $labId)->where('status', 1)->orderBy('name')->get();
        $specialPrices = LabSpecialprice::where('lab_id', $labId)->where('child_lab_id', $ltolId)->get()->keyBy('diagnosticestest_id');

        return view('ll.createbill', compact('tests', 'specialPrices', 'ltolId'));
    }

    public function createinvestigation(Request $request, BarcodeService $barcodeService, SmsService $smsService, ImageService $imageService)
    {
        $request->validate([
            'testid' => 'required|array|min:1',
            'name' => 'required|string|max:200',
        ]);

        $labId = $this->getLabId();
        $ltolId = $this->getLtolId();
        $userId = Auth::id() ?? 1;

        DB::beginTransaction();
        try {
            $patient = Patient::create([
                'lab_id' => $labId,
                'unique_id' => 'B2B-' . strtoupper(substr(uniqid(), -6)),
                'title' => $request->title ?? 'Mr',
                'name' => $request->name,
                'age' => $request->age,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'created_by' => $userId,
                'created_on' => now(),
            ]);

            $totalAmount = 0;
            if (is_array($request->testprice)) {
                foreach ($request->testprice as $price) {
                    $totalAmount += (float)$price;
                }
            }

            $investigation = Investigation::create([
                'lab_id' => $labId,
                'patient_id' => $patient->id,
                'ltol_id' => $ltolId,
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount,
                'discount' => 0,
                'notes' => $request->notes,
                'status' => 1,
                'test_status' => 1,
                'created_by' => $userId,
                'created_on' => now(),
            ]);

            foreach ($request->testid as $testId) {
                InvestigationTest::create([
                    'investigation_id' => $investigation->id,
                    'test_id' => $testId,
                    'status' => 1,
                    'created_by' => $userId,
                    'created_on' => now(),
                ]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['err' => 0, 'message' => 'Investigation order placed successfully.', 'lid' => $investigation->id]);
            }

            return redirect()->route('ll.reports')->with('success', 'Order placed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['err' => 1, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    public function reports(Request $request)
    {
        $labId = $this->getLabId();
        $ltolId = $this->getLtolId();

        $query = Investigation::with(['patient', 'investigationTests.diagnosticstest', 'investigationTests.approver'])
            ->where('lab_id', $labId)
            ->where('ltol_id', $ltolId)
            ->where('status', 1);

        if ($request->filled('patientname')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->patientname . '%');
            });
        }

        $orders = $query->latest('id')->paginate(25)->withQueryString();
        return view('ll.reports', compact('orders'));
    }

    public function payments()
    {
        $labId = $this->getLabId();
        $ltolId = $this->getLtolId();

        $payments = LabPayment::where('lab_id', $labId)
            ->where('labtolab_id', $ltolId)
            ->latest('id')
            ->paginate(25);

        $totalBilled = Investigation::where('lab_id', $labId)->where('ltol_id', $ltolId)->where('status', 1)->sum('total_amount');
        $totalPaid = LabPayment::where('lab_id', $labId)->where('labtolab_id', $ltolId)->sum('payment_amount');
        $dueBalance = $totalBilled - $totalPaid;

        return view('ll.payments', compact('payments', 'totalBilled', 'totalPaid', 'dueBalance'));
    }

    public function ratecard()
    {
        $labId = $this->getLabId();
        $ltolId = $this->getLtolId();

        $tests = Diagnosticstest::with('department')
            ->where('lab_id', $labId)
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $specialPrices = LabSpecialprice::where('lab_id', $labId)
            ->where('child_lab_id', $ltolId)
            ->get()
            ->keyBy('diagnosticestest_id');

        return view('ll.ratecard', compact('tests', 'specialPrices'));
    }
}
