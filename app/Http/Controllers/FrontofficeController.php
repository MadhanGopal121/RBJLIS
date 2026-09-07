<?php

namespace App\Http\Controllers;

use App\Models\Diagnosticstest;
use App\Models\Doctor;
use App\Models\Investigation;
use App\Models\InvestigationDecline;
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

class FrontofficeController extends Controller
{
    public function __construct()
    {
        view()->share('sidebar', 'frontoffice');
    }

    private function getLabId(): int
    {
        return Auth::user()->lab_id ?? 1;
    }

    private function getLabShortName(): string
    {
        return Auth::user()->lab->shortname ?? 'RBJ';
    }

    private function generatePatientUid(): string
    {
        $labId = $this->getLabId();
        $shortname = $this->getLabShortName();

        $lastPatient = Patient::where('lab_id', $labId)->latest('id')->first();
        $ptid = 1;

        if ($lastPatient && $lastPatient->unique_id) {
            $num = str_replace(strtoupper($shortname), '', $lastPatient->unique_id);
            $num = ltrim($num, '0');
            if (is_numeric($num)) {
                $ptid = ((int)$num) + 1;
            }
        }

        return strtoupper($shortname) . str_pad((string)$ptid, 5, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $labId = $this->getLabId();
        $tests = Diagnosticstest::where('lab_id', $labId)->where('status', 1)->orderBy('name')->get();
        $associatedlabs = Labtolab::where('lab_id', $labId)->where('status', 1)->get();
        $doctors = Doctor::where('lab_id', $labId)->orWhere('lab_id', 0)->where('status', 1)->get();
        $nextPatientUid = $this->generatePatientUid();

        return view('frontoffice.index', compact('tests', 'associatedlabs', 'doctors', 'nextPatientUid'));
    }

    public function createinvestigation(Request $request, BarcodeService $barcodeService, SmsService $smsService, ImageService $imageService)
    {
        $request->validate([
            'testid' => 'required|array|min:1',
            'name' => 'required_without:patient_id|string|max:200',
        ]);

        $labId = $this->getLabId();
        $userId = Auth::id() ?? 1;
        $labName = Auth::user()->lab->name ?? 'RBJ Lab';
        $shortname = $this->getLabShortName();

        DB::beginTransaction();
        try {
            // 1. Patient Handling
            if (empty($request->patient_id)) {
                $patient = Patient::create([
                    'lab_id' => $labId,
                    'unique_id' => $this->generatePatientUid(),
                    'title' => $request->title ?? 'Mr',
                    'name' => $request->name,
                    'age' => $request->age,
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'aadhar' => $request->aadhar,
                    'address' => $request->address,
                    'created_by' => $userId,
                    'created_on' => now(),
                ]);
            } else {
                $patient = Patient::where('lab_id', $labId)->findOrFail($request->patient_id);
                if ($request->filled('aadhar')) {
                    $patient->aadhar = $request->aadhar;
                    $patient->save();
                }
            }

            // 2. Pricing & Discounts
            $totalAmount = 0;
            if (is_array($request->testprice)) {
                foreach ($request->testprice as $price) {
                    $totalAmount += (float)$price;
                }
            }

            $discountInput = (float)($request->discount ?? 0);
            $discountAmount = 0;
            if ($discountInput > 0) {
                if ($request->discountype === 'percentage' && $discountInput < 100) {
                    $discountAmount = ($totalAmount * $discountInput) / 100;
                } else {
                    $discountAmount = $discountInput;
                }
            }

            $paidAmount = (float)($request->paid_amount ?? 0);
            $balanceAmount = $totalAmount - ($discountAmount + $paidAmount);

            // Upload Prescription if any
            $prescriptionFile = '';
            if ($request->hasFile('filedata')) {
                $prescriptionFile = $imageService->upload($request->file('filedata'), 'uploads/prescription');
            }

            // 3. Create Investigation
            $investigation = Investigation::create([
                'lab_id' => $labId,
                'patient_id' => $patient->id,
                'doctor_id' => $request->doctor_id ?: null,
                'ltol_id' => $request->ltol_id ?: null,
                'refered_by' => $request->refered_by,
                'clinicname' => $request->clinicname,
                'total_amount' => $totalAmount,
                'balance_amount' => $balanceAmount,
                'discount' => $discountAmount,
                'notes' => $request->notes,
                'prescription' => $prescriptionFile,
                'status' => 1,
                'test_status' => 1,
                'created_by' => $userId,
                'created_on' => now(),
            ]);

            // 4. Record Initial Payment
            if ($paidAmount > 0) {
                LabPayment::create([
                    'lab_id' => $labId,
                    'investigation_id' => $investigation->id,
                    'labtolab_id' => $request->ltol_id ?: null,
                    'paymenttype' => $request->pay_method ?? 'Cash',
                    'payment_amount' => $paidAmount,
                    'trans_number' => $request->transid,
                    'received_date' => now(),
                    'received_by' => $userId,
                ]);
            }

            // 5. Create Individual Investigation Tests
            $emergencyTests = $request->requiredemergency ?? [];

            foreach ($request->testid as $key => $testId) {
                $testType = $request->testtype[$key] ?? 'test';

                if ($testType === 'profile') {
                    $profileTests = ProfileTest::where('profile_id', $testId)->get();
                    foreach ($profileTests as $pt) {
                        $this->createSingleInvestigationTest($investigation->id, $pt->diagnosticstest_id, $testId, in_array($testId, $emergencyTests), $patient->name, $shortname, $barcodeService, $userId);
                    }
                } else {
                    $this->createSingleInvestigationTest($investigation->id, $testId, null, in_array($testId, $emergencyTests), $patient->name, $shortname, $barcodeService, $userId);
                }
            }

            DB::commit();

            // 6. Optional SMS
            if ($patient->phone) {
                $smsService->investigationCreate(
                    $patient->name,
                    $totalAmount - $discountAmount,
                    $labName,
                    $paidAmount,
                    $balanceAmount,
                    $patient->phone
                );
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'err' => 0,
                    'message' => 'Patient and investigations booked successfully.',
                    'lid' => $investigation->id,
                ]);
            }

            return redirect()->route('frontoffice.bills')->with('success', 'Investigation booked successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['err' => 1, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Error booking investigation: ' . $e->getMessage())->withInput();
        }
    }

    private function createSingleInvestigationTest(int $investigationId, int $testId, ?int $packageId, bool $isEmergency, string $patientName, string $shortname, BarcodeService $barcodeService, int $userId): void
    {
        $cleanName = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $patientName), 0, 5));
        $barcodeString = $cleanName . '-' . abs(crc32(uniqid()));

        InvestigationTest::create([
            'investigation_id' => $investigationId,
            'test_id' => $testId,
            'package_id' => $packageId,
            'is_emergency' => $isEmergency,
            'status' => 1,
            'created_by' => $userId,
            'created_on' => now(),
        ]);

        $barcodeService->create($barcodeString);
    }

    // --- Receipts / Invoices list ---
    public function bills(Request $request)
    {
        $labId = $this->getLabId();
        $query = Investigation::with(['patient', 'doctor', 'labtolab', 'investigationTests.diagnosticstest', 'labPayments'])
            ->where('lab_id', $labId)
            ->where('status', 1);

        if ($request->filled('fromdate')) {
            $query->whereDate('created_on', '>=', $request->fromdate);
        }
        if ($request->filled('todate')) {
            $query->whereDate('created_on', '<=', $request->todate);
        }
        if ($request->filled('patientname')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->patientname . '%');
            });
        }
        if ($request->filled('patientphone')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->patientphone . '%');
            });
        }

        $bills = $query->latest('id')->paginate(30)->withQueryString();
        return view('frontoffice.bills', compact('bills'));
    }

    // --- Collect Balance Payment Modal / Action ---
    public function collectpayment(Request $request)
    {
        $request->validate([
            'investigation_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'paymenttype' => 'required|string',
        ]);

        $labId = $this->getLabId();
        $inv = Investigation::where('lab_id', $labId)->findOrFail($request->investigation_id);

        LabPayment::create([
            'lab_id' => $labId,
            'investigation_id' => $inv->id,
            'paymenttype' => $request->paymenttype,
            'payment_amount' => $request->amount,
            'trans_number' => $request->trans_number,
            'received_date' => now(),
            'received_by' => Auth::id() ?? 1,
        ]);

        $paidSoFar = $inv->labPayments->sum('payment_amount') + $request->amount;
        $inv->balance_amount = max(0, $inv->total_amount - ($paidSoFar + $inv->discount));
        $inv->save();

        return back()->with('success', 'Payment received successfully.');
    }

    // --- Lab B2B Payments ---
    public function labpayment(Request $request)
    {
        $labId = $this->getLabId();
        if ($request->isMethod('post')) {
            $request->validate([
                'labtolab_id' => 'required|integer',
                'amount' => 'required|numeric|min:1',
                'paymenttype' => 'required|string',
            ]);

            LabPayment::create([
                'lab_id' => $labId,
                'investigation_id' => 0,
                'labtolab_id' => $request->labtolab_id,
                'paymenttype' => $request->paymenttype,
                'payment_amount' => $request->amount,
                'trans_number' => $request->trans_number,
                'received_date' => now(),
                'received_by' => Auth::id() ?? 1,
            ]);

            return redirect()->route('frontoffice.labpayment')->with('success', 'B2B payment recorded successfully.');
        }

        $associatedlabs = Labtolab::where('lab_id', $labId)->where('status', 1)->get();
        $payments = LabPayment::with('labtolab')
            ->where('lab_id', $labId)
            ->whereNotNull('labtolab_id')
            ->latest('id')
            ->paginate(25);

        return view('frontoffice.labpayment', compact('associatedlabs', 'payments'));
    }

    // --- AJAX APIs for autocomplete & test lookup ---
    public function gettest(Request $request)
    {
        $labId = $this->getLabId();
        $term = $request->term ?? '';
        $clab = (int)($request->clabid ?? 0);

        $tests = Diagnosticstest::where('lab_id', $labId)
            ->where('status', 1)
            ->where('name', 'like', '%' . $term . '%')
            ->get();

        $results = [];
        foreach ($tests as $t) {
            $price = $t->price;
            if ($clab > 0) {
                $sp = LabSpecialprice::where('lab_id', $labId)
                    ->where('child_lab_id', $clab)
                    ->where('diagnosticestest_id', $t->id)
                    ->first();
                if ($sp) {
                    $price = $sp->sp_price;
                }
            }

            $results[] = [
                'id' => $t->id,
                'label' => $t->name . ' (Rs. ' . $price . ')',
                'value' => $t->name,
                'name' => $t->name,
                'price' => $price,
                'type' => 'test',
            ];
        }

        if (!$request->has('packages') || $request->packages != 0) {
            $packages = Profile::where('lab_id', $labId)
                ->where('status', 1)
                ->where('name', 'like', '%' . $term . '%')
                ->get();

            foreach ($packages as $pkg) {
                $results[] = [
                    'id' => $pkg->id,
                    'label' => '[Package] ' . $pkg->name . ' (Rs. ' . $pkg->price . ')',
                    'value' => $pkg->name,
                    'name' => $pkg->name,
                    'price' => $pkg->price,
                    'type' => 'profile',
                ];
            }
        }

        return response()->json($results);
    }

    public function getpatient(Request $request)
    {
        $labId = $this->getLabId();
        $term = $request->term ?? '';

        $patients = Patient::where('lab_id', $labId)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', '%' . $term . '%')
                  ->orWhere('phone', 'like', '%' . $term . '%')
                  ->orWhere('unique_id', 'like', '%' . $term . '%');
            })
            ->take(20)
            ->get();

        $results = [];
        foreach ($patients as $p) {
            $results[] = [
                'id' => $p->id,
                'label' => $p->name . ' (' . $p->unique_id . ') - ' . $p->phone,
                'value' => $p->name,
                'patient' => $p,
            ];
        }

        return response()->json($results);
    }

    public function declinetest(Request $request, ImageService $imageService)
    {
        $request->validate([
            'investigationtest_id' => 'required|integer',
            'reason' => 'required|string',
        ]);

        $labId = $this->getLabId();
        $it = InvestigationTest::whereHas('investigation', function ($q) use ($labId) {
            $q->where('lab_id', $labId);
        })->findOrFail($request->investigationtest_id);

        $filePath = '';
        if ($request->hasFile('filedata')) {
            $filePath = $imageService->upload($request->file('filedata'), 'uploads/declines');
        }

        $it->is_declined = true;
        $it->declined_media = $filePath;
        $it->declined_reason = $request->reason;
        $it->declined_by = Auth::id() ?? 1;
        $it->save();

        InvestigationDecline::create([
            'investigation_id' => $it->investigation_id,
            'investigation_test_id' => $it->id,
            'lab_id' => $labId,
            'reason' => $request->reason,
            'filepath' => $filePath,
            'declined_role' => Auth::user()->role_id ?? 3,
            'created_by' => Auth::id() ?? 1,
            'created_on' => now(),
        ]);

        return back()->with('success', 'Sample marked as declined.');
    }
}
