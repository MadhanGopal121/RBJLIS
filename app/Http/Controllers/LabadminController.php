<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Diagnosticstest;
use App\Models\Doctor;
use App\Models\Investigation;
use App\Models\Lab;
use App\Models\LabDepartment;
use App\Models\LabPayment;
use App\Models\LabSpecialprice;
use App\Models\Labtolab;
use App\Models\Parameter;
use App\Models\Patient;
use App\Models\Profile;
use App\Models\ProfileTest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDepartment;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LabadminController extends Controller
{
    public function __construct()
    {
        view()->share('sidebar', 'labadmin');
    }

    private function getLabId(): int
    {
        $labId = Auth::user()->lab_id ?? 1;
        return ($labId > 0) ? (int)$labId : 1;
    }

    public function index()
    {
        $labId = $this->getLabId();
        $patientCount = Patient::where('lab_id', $labId)->count();
        $testCount = Diagnosticstest::where('lab_id', $labId)->where('status', 1)->count();
        $packageCount = Profile::where('lab_id', $labId)->where('status', 1)->count();
        $doctorCount = Doctor::where('lab_id', $labId)->count();
        $investigationCount = Investigation::where('lab_id', $labId)->where('status', 1)->count();
        $todayInvestigations = Investigation::where('lab_id', $labId)
            ->whereDate('created_on', today())
            ->where('status', 1)
            ->with(['patient', 'investigationTests.diagnosticstest'])
            ->latest()
            ->take(10)
            ->get();

        return view('labadmin.index', compact(
            'patientCount', 'testCount', 'packageCount', 'doctorCount',
            'investigationCount', 'todayInvestigations'
        ));
    }

    // --- Packages / Profiles ---
    public function packages()
    {
        $labId = $this->getLabId();
        $packages = Profile::with(['profileTests.diagnosticstest'])
            ->where('lab_id', $labId)
            ->where('status', 1)
            ->latest()
            ->get();

        return view('labadmin.packages', compact('packages'));
    }

    public function showAddPackage(Request $request)
    {
        $labId = $this->getLabId();
        $profiles = null;
        if ($request->has('id') && $request->id) {
            $profiles = Profile::with('profileTests.diagnosticstest')->where('lab_id', $labId)->find($request->id);
        }

        $tests = Diagnosticstest::where('lab_id', $labId)->where('status', 1)->orderBy('name')->get();
        return view('labadmin.addpackage', compact('profiles', 'tests'));
    }

    public function savePackage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'price' => 'required|numeric',
        ]);

        $labId = $this->getLabId();
        $userId = Auth::id() ?? 1;

        if ($request->has('id') && $request->id) {
            $profile = Profile::where('lab_id', $labId)->findOrFail($request->id);
            $profile->update([
                'name' => $request->name,
                'price' => $request->price,
                'lab_price' => $request->lab_price ?? 0,
            ]);

            if ($request->has('packagetests') && is_array($request->packagetests)) {
                ProfileTest::where('profile_id', $profile->id)->delete();
                foreach ($request->packagetests as $testId) {
                    ProfileTest::create([
                        'profile_id' => $profile->id,
                        'diagnosticstest_id' => $testId,
                        'status' => 1,
                    ]);
                }
            }

            return redirect()->route('labadmin.packages')->with('success', 'Package updated successfully.');
        } else {
            $profile = Profile::create([
                'lab_id' => $labId,
                'name' => $request->name,
                'price' => $request->price,
                'lab_price' => $request->lab_price ?? 0,
                'created_by' => $userId,
                'created_on' => now(),
                'status' => 1,
            ]);

            if ($request->has('packagetests') && is_array($request->packagetests)) {
                foreach ($request->packagetests as $testId) {
                    ProfileTest::create([
                        'profile_id' => $profile->id,
                        'diagnosticstest_id' => $testId,
                        'status' => 1,
                    ]);
                }
            }

            return redirect()->route('labadmin.packages')->with('success', 'Package created successfully.');
        }
    }

    // --- Diagnostic Tests ---
    public function labtests(Request $request)
    {
        $labId = $this->getLabId();
        $query = Diagnosticstest::with('department')
            ->where('lab_id', $labId)
            ->where('status', 1);

        if ($request->has('q') && $request->q) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $labtests = $query->orderBy('name')->paginate(25)->withQueryString();
        return view('labadmin.labtests', compact('labtests'));
    }

    public function updatetest(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $labId = $this->getLabId();
        $test = Diagnosticstest::where('lab_id', $labId)->findOrFail($request->id);
        $test->update($request->only([
            'name', 'price', 'lab_price', 'reference_val', 'units',
            'method', 'notes', 'note', 'default_notes', 'default_note',
            'interpretation', 'sample_type', 'specimen_time'
        ]));

        return response()->json(['status' => 'success', 'message' => 'Test details updated successfully.']);
    }

    // --- Parameters Management ---
    public function listparameters(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $params = Parameter::where('diagnosticstests_id', $request->id)
            ->where('status', 1)
            ->orderBy('sort', 'ASC')
            ->get();

        return view('labadmin.listparameters', compact('params'));
    }

    public function addparameters(Request $request)
    {
        $request->validate([
            'testid' => 'required|integer',
            'parametername' => 'required|string|max:100',
        ]);

        $type = $request->addtype ?? 'parameter';
        $param = Parameter::create([
            'diagnosticstests_id' => $request->testid,
            'name' => $request->parametername,
            'default_value' => ($type === 'parameter') ? ($request->parameterdefault ?? '') : '',
            'units' => ($type === 'parameter') ? ($request->parameterunits ?? '') : '',
            'method' => ($type === 'parameter') ? ($request->method ?? '') : '',
            'type' => $type,
            'created_by' => Auth::id() ?? 1,
            'created_on' => now(),
            'status' => 1,
        ]);

        return response()->json(['status' => 'success', 'param' => $param]);
    }

    public function updatetestparams(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $param = Parameter::findOrFail($request->id);
        $param->update([
            'name' => $request->parname,
            'default_value' => $request->pardefaults,
            'units' => $request->parunits,
            'method' => $request->parmethod,
        ]);

        return response('Updated successfully');
    }

    public function deleteparameter(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $param = Parameter::findOrFail($request->id);
        $param->update(['status' => 0]);
        return response('Deleted successfully');
    }

    // --- Lab Staff Users ---
    public function labusers()
    {
        $labId = $this->getLabId();
        $users = User::with(['role', 'userDepartments.department'])
            ->where('lab_id', $labId)
            ->get();

        return view('labadmin.labusers', compact('users'));
    }

    public function showCreateLabUser(Request $request)
    {
        $labId = $this->getLabId();
        $userinfo = null;
        if ($request->has('id') && $request->id) {
            $userinfo = User::with('userDepartments')->where('lab_id', $labId)->find($request->id);
            if ($userinfo) {
                $userinfo->dep = $userinfo->userDepartments->pluck('department_id')->toArray();
            }
        }

        $departments = LabDepartment::with('department')
            ->where('lab_id', $labId)
            ->where('status', 1)
            ->get()
            ->pluck('department');

        $roles = Role::whereIn('id', [2, 3, 4, 5, 6, 7])->get();

        return view('labadmin.createlabuser', compact('userinfo', 'departments', 'roles'));
    }

    public function saveLabUser(Request $request)
    {
        $labId = $this->getLabId();
        $lab = Lab::findOrFail($labId);

        if ($request->has('id') && $request->id) {
            $user = User::where('lab_id', $labId)->findOrFail($request->id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->designation = $request->designation;
            $user->role_id = $request->role_id;
            if (!empty($request->passworda)) {
                $user->password = Hash::make($request->passworda);
            }
            $user->save();

            if ($request->has('department') && is_array($request->department)) {
                UserDepartment::where('user_id', $user->id)->delete();
                foreach ($request->department as $dId) {
                    UserDepartment::create(['user_id' => $user->id, 'department_id' => $dId]);
                }
            }

            return redirect()->route('labadmin.labusers')->with('success', 'User updated successfully.');
        } else {
            $currentUsersCount = User::where('lab_id', $labId)->count();
            if ($currentUsersCount >= $lab->user_count) {
                return back()->with('error', 'User limit reached for your laboratory subscription.')->withInput();
            }

            if (User::where('email', $request->email)->exists()) {
                return back()->with('error', 'A user with this email already exists.')->withInput();
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'designation' => $request->designation,
                'password' => Hash::make($request->passworda ?? 'password123'),
                'lab_id' => $labId,
                'role_id' => $request->role_id,
                'created_by' => Auth::id() ?? 1,
                'status' => 1,
            ]);

            if ($request->has('department') && is_array($request->department)) {
                foreach ($request->department as $dId) {
                    UserDepartment::create(['user_id' => $user->id, 'department_id' => $dId]);
                }
            }

            return redirect()->route('labadmin.labusers')->with('success', 'Staff user created successfully.');
        }
    }

    // --- Doctors Directory ---
    public function doctors()
    {
        $labId = $this->getLabId();
        $doctors = Doctor::where('lab_id', $labId)->orWhere('lab_id', 0)->latest()->get();
        return view('labadmin.doctors', compact('doctors'));
    }

    public function showAddDoctor(Request $request)
    {
        $doctor = null;
        if ($request->has('id') && $request->id) {
            $doctor = Doctor::find($request->id);
        }
        return view('labadmin.adddoctor', compact('doctor'));
    }

    public function saveDoctor(Request $request)
    {
        $request->validate([
            'doctor_name' => 'required|string|max:200',
        ]);

        $labId = $this->getLabId();

        if ($request->has('id') && $request->id) {
            $doctor = Doctor::findOrFail($request->id);
            $doctor->update($request->only(['doctor_name', 'clinic_name', 'phone', 'email', 'discount']));
            return redirect()->route('labadmin.doctors')->with('success', 'Doctor updated successfully.');
        } else {
            Doctor::create([
                'lab_id' => $labId,
                'doctor_name' => $request->doctor_name,
                'clinic_name' => $request->clinic_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'discount' => $request->discount ?? 0,
                'created_by' => Auth::id() ?? 1,
                'status' => 1,
            ]);
            return redirect()->route('labadmin.doctors')->with('success', 'Doctor registered successfully.');
        }
    }

    // --- Patients Management ---
    public function patients()
    {
        $labId = $this->getLabId();
        $patients = Patient::where('lab_id', $labId)->latest()->paginate(25);
        return view('labadmin.patients', compact('patients'));
    }

    public function showEditPatient(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $labId = $this->getLabId();
        $patient = Patient::where('lab_id', $labId)->findOrFail($request->id);
        return view('labadmin.editpatient', compact('patient'));
    }

    public function updatePatient(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string|max:200',
        ]);

        $labId = $this->getLabId();
        $patient = Patient::where('lab_id', $labId)->findOrFail($request->id);
        $patient->update($request->only(['title', 'name', 'age', 'gender', 'phone', 'email', 'aadhar', 'address']));

        return redirect()->route('labadmin.patients')->with('success', 'Patient updated successfully.');
    }

    // --- Lab-to-Lab (B2B) Centers & Special Rates ---
    public function labs()
    {
        $labId = $this->getLabId();
        $labs = Labtolab::where('lab_id', $labId)->latest()->get();
        return view('labadmin.labs', compact('labs'));
    }

    public function showCreateLab(Request $request)
    {
        $labinfo = null;
        if ($request->has('id') && $request->id) {
            $labinfo = Labtolab::find($request->id);
        }
        return view('labadmin.createlab', compact('labinfo'));
    }

    public function saveLab(Request $request)
    {
        $request->validate([
            'lab_name' => 'required|string|max:200',
            'contact_email' => 'required|email',
        ]);

        $labId = $this->getLabId();

        if (User::where('email', $request->contact_email)->exists()) {
            return back()->with('error', 'A user with this contact email already exists.')->withInput();
        }

        $ltol = Labtolab::create([
            'lab_id' => $labId,
            'lab_name' => $request->lab_name,
            'contact_name' => $request->contact_name,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address' => $request->address,
            'created_by' => Auth::id() ?? 1,
            'status' => 1,
        ]);

        User::create([
            'name' => $request->contact_name ?? $request->lab_name,
            'email' => $request->contact_email,
            'phone' => $request->contact_phone,
            'password' => Hash::make('Test@1234'),
            'designation' => 'Lab Client',
            'lab_id' => $labId,
            'role_id' => 8,
            'created_by' => Auth::id() ?? 1,
            'status' => 1,
        ]);

        return redirect()->route('labadmin.labs')->with('success', 'Partner lab center created successfully.');
    }

    public function speciallabrates(Request $request)
    {
        $request->validate(['llid' => 'required|integer']);
        $labId = $this->getLabId();
        $labdet = Labtolab::findOrFail($request->llid);
        $tests = Diagnosticstest::where('lab_id', $labId)->where('status', 1)->orderBy('name')->get();
        $sps = LabSpecialprice::with(['mastertest', 'diagnosticstest'])
            ->where('child_lab_id', $request->llid)
            ->where('lab_id', $labId)
            ->get();

        $ltolid = $request->llid;
        return view('labadmin.speciallabrates', compact('labdet', 'tests', 'sps', 'ltolid'));
    }

    public function addspecialrates(Request $request)
    {
        $request->validate([
            'testid' => 'required|integer',
            'ltolid' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $labId = $this->getLabId();
        $test = Diagnosticstest::findOrFail($request->testid);

        $sp = LabSpecialprice::updateOrCreate(
            ['lab_id' => $labId, 'child_lab_id' => $request->ltolid, 'diagnosticestest_id' => $test->id],
            [
                'mastertest_id' => $test->m_testid,
                'sp_price' => $request->price,
                'created_by' => Auth::id() ?? 1,
                'status' => 1,
            ]
        );

        return response()->json(['status' => 'success', 'data' => $sp]);
    }

    // --- Receipts / Invoices / Billing ---
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

        if ($request->boolean('exportexcel')) {
            $bills = $query->latest('id')->get();
            return $this->exportBillsToCsv($bills);
        }

        $bills = $query->latest('id')->paginate(30)->withQueryString();
        $pdata = $request->all();

        return view('labadmin.bills', compact('bills', 'pdata'));
    }

    private function exportBillsToCsv($bills): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="receipts_' . date('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($bills) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Bill No.', 'Patient Name', 'Age/Gender', 'Phone', 'Referred By / Lab', 'No. of Tests', 'Total', 'Paid', 'Balance', 'Discount', 'Created Date']);

            foreach ($bills as $b) {
                $paid = $b->labPayments->sum('payment_amount');
                $balance = $b->total_amount - ($paid + $b->discount);
                $refered = $b->labtolab ? $b->labtolab->lab_name : ($b->doctor ? $b->doctor->doctor_name : ($b->refered_by ?? 'Self'));

                fputcsv($handle, [
                    $b->patient->unique_id ?? ('INV-' . $b->id),
                    $b->patient->name ?? 'N/A',
                    ($b->patient->age ?? '') . '/' . ($b->patient->gender ?? ''),
                    $b->patient->phone ?? '',
                    $refered,
                    $b->investigationTests->count(),
                    $b->total_amount,
                    $paid,
                    $balance,
                    $b->discount,
                    $b->created_on,
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function deletedbills()
    {
        $labId = $this->getLabId();
        $bills = Investigation::with(['patient', 'investigationTests.diagnosticstest'])
            ->where('lab_id', $labId)
            ->where('status', 0)
            ->latest('id')
            ->paginate(30);

        return view('labadmin.deletedbills', compact('bills'));
    }

    public function deletebill(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $labId = $this->getLabId();
        $inv = Investigation::where('lab_id', $labId)->findOrFail($request->id);
        $inv->update(['status' => 0]);

        return response()->json(['status' => 'success', 'message' => 'Investigation deleted.']);
    }

    // --- Lab Branding & Settings ---
    public function settings()
    {
        $labId = $this->getLabId();
        $labinfo = Lab::findOrFail($labId);
        return view('labadmin.settings', compact('labinfo'));
    }

    public function saveSettings(Request $request, ImageService $imageService)
    {
        $labId = $this->getLabId();
        $lab = Lab::findOrFail($labId);

        $lab->address = $request->address;
        $lab->defult_notes = $request->defult_notes;

        // UPI Settings
        $lab->upi_id = $request->upi_id;
        $lab->upi_name = $request->upi_name;
        $lab->enable_upi = $request->has('enable_upi');

        // Stripe Settings
        $lab->stripe_key = $request->stripe_key;
        $lab->stripe_secret = $request->stripe_secret;
        $lab->stripe_webhook_secret = $request->stripe_webhook_secret;
        $lab->enable_stripe = $request->has('enable_stripe');

        // SMS Settings
        $lab->sms_provider = $request->sms_provider ?? 'bulksmsgateway';
        $lab->sms_api_key = $request->sms_api_key;
        $lab->sms_sender_id = $request->sms_sender_id;
        $lab->enable_sms = $request->has('enable_sms');

        // Email & SMTP Settings
        $lab->enable_email = $request->has('enable_email');
        $lab->smtp_host = $request->smtp_host;
        $lab->smtp_port = $request->smtp_port;
        $lab->smtp_user = $request->smtp_user;
        $lab->smtp_pass = $request->smtp_pass;
        $lab->smtp_encryption = $request->smtp_encryption;

        if ($request->hasFile('logoimg')) {
            $logoPath = $imageService->upload($request->file('logoimg'), 'uploads');
            if ($logoPath) {
                $lab->logo = $logoPath;
            }
        }

        if ($request->hasFile('letterhead')) {
            $lhPath = $imageService->upload($request->file('letterhead'), 'uploads/letterheads');
            if ($lhPath) {
                $lab->letter_head = $lhPath;
            }
        }

        if ($request->hasFile('sealimg')) {
            $sealPath = $imageService->upload($request->file('sealimg'), 'uploads/seals');
            if ($sealPath) {
                $lab->lab_seal = $sealPath;
            }
        }

        $lab->save();

        return redirect()->route('labadmin.settings')->with('success', 'Laboratory settings, payment gateways, and notification integrations updated successfully.');
    }

    // --- Test SMTP Connection & Email ---
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $labId = $this->getLabId();
        $lab = Lab::find($labId);

        $smtpHost = $request->smtp_host ?: ($lab->smtp_host ?: config('mail.mailers.smtp.host'));
        $smtpPort = $request->smtp_port ?: ($lab->smtp_port ?: config('mail.mailers.smtp.port', 587));
        $smtpUser = $request->smtp_user ?: ($lab->smtp_user ?: config('mail.mailers.smtp.username'));
        $smtpPass = $request->smtp_pass ?: ($lab->smtp_pass ?: config('mail.mailers.smtp.password'));
        $smtpEnc = $request->smtp_encryption ?: ($lab->smtp_encryption ?: 'tls');

        if ($smtpHost && $smtpUser && $smtpPass) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $smtpHost,
                'mail.mailers.smtp.port' => (int)$smtpPort,
                'mail.mailers.smtp.encryption' => ($smtpEnc === 'none') ? null : $smtpEnc,
                'mail.mailers.smtp.username' => $smtpUser,
                'mail.mailers.smtp.password' => $smtpPass,
                'mail.from.address' => $smtpUser,
                'mail.from.name' => $lab->name ?? 'RBJLIS Diagnostics',
            ]);
        }

        try {
            Mail::raw("Hello!\n\nThis is a test email sent from your RBJLIS Laboratory Information System to verify your SMTP email integration.\n\nLaboratory: " . ($lab->name ?? 'RBJ Diagnostics') . "\nStatus: SMTP Connection & Delivery Successful!\nTimestamp: " . now()->toDateTimeString(), function ($msg) use ($request, $lab) {
                $msg->to($request->test_email)
                    ->subject("SMTP Test Email Verification - " . ($lab->name ?? 'RBJLIS'));
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Test email sent successfully to ' . $request->test_email . '! Please check your inbox or spam folder.'
            ]);
        } catch (\Throwable $e) {
            Log::error('SMTP Test Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    // --- Reports Register ---
    public function lastdayreport(Request $request)
    {
        $labId = $this->getLabId();
        $date = $request->reportdate ?? today()->toDateString();

        $investigations = Investigation::with(['patient', 'investigationTests.diagnosticstest', 'labPayments'])
            ->where('lab_id', $labId)
            ->whereDate('created_on', $date)
            ->where('status', 1)
            ->get();

        $totalCollection = $investigations->sum('total_amount');
        $totalReceived = $investigations->flatMap->labPayments->sum('payment_amount');
        $totalDiscount = $investigations->sum('discount');

        return view('labadmin.lastdayreport', compact('investigations', 'date', 'totalCollection', 'totalReceived', 'totalDiscount'));
    }
}
