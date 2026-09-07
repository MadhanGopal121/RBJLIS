<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Diagnosticstest;
use App\Models\Lab;
use App\Models\LabDepartment;
use App\Models\Mastertest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperadminController extends Controller
{
    public function __construct()
    {
        // Set sidebar for views
        view()->share('sidebar', 'superadmin');
    }

    public function index()
    {
        $labCount = Lab::count();
        $userCount = User::count();
        $testCount = Mastertest::count();
        $departmentCount = Department::count();
        $recentLabs = Lab::latest()->take(5)->get();

        return view('superadmin.index', compact('labCount', 'userCount', 'testCount', 'departmentCount', 'recentLabs'));
    }

    public function lablist()
    {
        $labs = Lab::with('users')->latest()->get();
        return view('superadmin.lablist', compact('labs'));
    }

    public function showAddLab(Request $request)
    {
        $labdata = null;
        if ($request->has('id') && $request->id) {
            $labdata = Lab::with('labDepartments')->find($request->id);
        }

        $departments = Department::where('status', 1)->get();
        return view('superadmin.addlab', compact('labdata', 'departments'));
    }

    public function saveLab(Request $request)
    {
        $data = $request->all();
        $userId = Auth::id() ?? 1;
        $data['created_by'] = $userId;
        $data['sub_from'] = now()->toDateString();

        if (isset($data['membership'])) {
            if ($data['membership'] == 3) {
                $data['sub_to'] = now()->addMonths(3)->toDateString();
            } elseif ($data['membership'] == 6) {
                $data['sub_to'] = now()->addMonths(6)->toDateString();
            } elseif ($data['membership'] == 12) {
                $data['sub_to'] = now()->addMonths(12)->toDateString();
            }
        }

        if (!empty($data['id'])) {
            $this->updateLab($data);
            return redirect()->route('superadmin.lablist')->with('success', 'Lab details updated successfully.');
        } else {
            $result = $this->createLab($data);
            if ($result === false) {
                return back()->with('error', 'A user with this email already exists.')->withInput();
            }
            return redirect()->route('superadmin.lablist')->with('success', 'Lab and administrator account created successfully.');
        }
    }

    private function updateLab(array $data)
    {
        $lab = Lab::findOrFail($data['id']);
        $lab->name = $data['name'];
        $lab->email = $data['email'] ?? $lab->email;
        $lab->phone = $data['phone'] ?? $lab->phone;
        $lab->user_count = $data['user_count'] ?? $lab->user_count;
        $lab->address = $data['address'] ?? $lab->address;
        $lab->save();

        // Update admin user
        $user = User::where('lab_id', $lab->id)->where('role_id', 2)->first();
        if ($user) {
            $user->name = $data['contactname'] ?? $user->name;
            $user->email = $data['email'] ?? $user->email;
            $user->phone = $data['phone'] ?? $user->phone;
            if (!empty($data['passworda'])) {
                $user->password = Hash::make($data['passworda']);
            }
            $user->save();
        }

        if (isset($data['department']) && is_array($data['department'])) {
            Diagnosticstest::where('lab_id', $lab->id)->update(['status' => 0]);
            LabDepartment::where('lab_id', $lab->id)->update(['status' => 0]);

            foreach ($data['department'] as $deptId) {
                LabDepartment::updateOrCreate(
                    ['lab_id' => $lab->id, 'department_id' => $deptId],
                    ['status' => 1]
                );

                $dtCheck = Diagnosticstest::where('lab_id', $lab->id)->where('department_id', $deptId)->count();
                if ($dtCheck > 0) {
                    Diagnosticstest::where('lab_id', $lab->id)->where('department_id', $deptId)->update(['status' => 1]);
                } else {
                    $mastertests = Mastertest::where('department_id', $deptId)->get();
                    foreach ($mastertests as $mt) {
                        Diagnosticstest::create([
                            'lab_id' => $lab->id,
                            'department_id' => $mt->department_id,
                            'name' => $mt->name,
                            'reference_val' => $mt->reference_val,
                            'm_testid' => $mt->id,
                            'units' => $mt->units,
                            'price' => $mt->price,
                            'method' => $mt->method,
                            'notes' => $mt->notes,
                            'specimen_time' => $mt->specimen_time,
                            'created_by' => 1,
                            'status' => 1,
                        ]);
                    }
                }
            }
        }
    }

    private function createLab(array $data)
    {
        if (User::where('email', $data['email'])->exists()) {
            return false;
        }

        DB::beginTransaction();
        try {
            $lab = Lab::create([
                'name' => $data['name'],
                'contactname' => $data['contactname'] ?? '',
                'shortname' => $data['shortname'] ?? strtoupper(substr($data['name'], 0, 3)),
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'user_count' => $data['user_count'] ?? 5,
                'sub_from' => $data['sub_from'] ?? now()->toDateString(),
                'sub_to' => $data['sub_to'] ?? now()->addYear()->toDateString(),
                'created_by' => $data['created_by'] ?? 1,
                'status' => 1,
            ]);

            User::create([
                'name' => $data['contactname'] ?? $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
                'password' => Hash::make($data['passworda'] ?? 'password123'),
                'designation' => 'Labadmin',
                'lab_id' => $lab->id,
                'role_id' => 2,
                'created_by' => $data['created_by'] ?? 1,
                'status' => 1,
            ]);

            if (isset($data['department']) && is_array($data['department'])) {
                foreach ($data['department'] as $deptId) {
                    LabDepartment::create([
                        'lab_id' => $lab->id,
                        'department_id' => $deptId,
                        'status' => 1,
                    ]);

                    $mastertests = Mastertest::where('department_id', $deptId)->get();
                    foreach ($mastertests as $mt) {
                        Diagnosticstest::create([
                            'lab_id' => $lab->id,
                            'department_id' => $deptId,
                            'name' => $mt->name,
                            'reference_val' => $mt->reference_val,
                            'm_testid' => $mt->id,
                            'units' => $mt->units,
                            'price' => $mt->price,
                            'method' => $mt->method,
                            'notes' => $mt->notes,
                            'specimen_time' => $mt->specimen_time,
                            'created_by' => $data['created_by'] ?? 1,
                            'status' => 1,
                        ]);
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function subscriptions()
    {
        $labs = Lab::latest()->get();
        return view('superadmin.subscriptions', compact('labs'));
    }

    public function departments()
    {
        $departments = Department::withCount('mastertests')->get();
        return view('superadmin.departments', compact('departments'));
    }

    public function showAddDepartment()
    {
        return view('superadmin.adddepartment');
    }

    public function saveDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        Department::create([
            'name' => $request->name,
            'is_default' => $request->has('is_default') ? 1 : 0,
            'price' => $request->price ?? 0,
            'status' => 1,
            'created_on' => now(),
        ]);

        return redirect()->route('superadmin.departments')->with('success', 'Department created successfully.');
    }

    public function tests()
    {
        $mtdata = Mastertest::with('department')->latest()->get();
        return view('superadmin.tests', compact('mtdata'));
    }

    public function showAddTest(Request $request)
    {
        $test = null;
        if ($request->has('id') && $request->id) {
            $test = Mastertest::find($request->id);
        }

        $departments = Department::where('status', 1)->get();
        return view('superadmin.addtest', compact('test', 'departments'));
    }

    public function saveTest(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'department_id' => 'required|integer',
        ]);

        if ($request->has('id') && $request->id) {
            $mastertest = Mastertest::findOrFail($request->id);
            $mastertest->update([
                'name' => $request->name,
                'department_id' => $request->department_id,
                'reference_val' => $request->reference_val,
                'units' => $request->units,
                'price' => $request->price ?? 0,
                'method' => $request->method,
                'notes' => $request->notes,
                'specimen_time' => $request->specimen_time,
            ]);

            return redirect()->route('superadmin.tests')->with('success', 'Master test updated successfully.');
        } else {
            $minsert = Mastertest::create([
                'name' => $request->name,
                'department_id' => $request->department_id,
                'reference_val' => $request->reference_val,
                'units' => $request->units,
                'price' => $request->price ?? 0,
                'method' => $request->method,
                'notes' => $request->notes,
                'specimen_time' => $request->specimen_time,
                'created_by' => Auth::id() ?? 1,
                'status' => 1,
                'created_on' => now(),
            ]);

            // Sync with existing labs having this department
            $labs = LabDepartment::where('department_id', $request->department_id)->get();
            foreach ($labs as $l) {
                Diagnosticstest::create([
                    'm_testid' => $minsert->id,
                    'lab_id' => $l->lab_id,
                    'name' => $request->name,
                    'department_id' => $request->department_id,
                    'reference_val' => $request->reference_val,
                    'units' => $request->units,
                    'price' => $request->price ?? 0,
                    'method' => $request->method,
                    'status' => $l->status,
                    'created_by' => Auth::id() ?? 1,
                ]);
            }

            return redirect()->route('superadmin.tests')->with('success', 'Master test created and propagated to subscribed labs.');
        }
    }

    public function roles()
    {
        $roles = Role::withCount('users')->get();
        return view('superadmin.roles', compact('roles'));
    }
}
