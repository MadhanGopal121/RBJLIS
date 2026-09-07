<?php

namespace App\Http\Controllers;

use App\Models\Investigation;
use App\Models\Lab;
use App\Models\User;
use App\Services\QrcodeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    public function __construct()
    {
        $role = Auth::user()->role_id ?? 3;
        $sidebar = ($role == 2) ? 'labadmin' : (($role == 8) ? 'll' : 'frontoffice');
        view()->share('sidebar', $sidebar);
    }

    public function index(Request $request)
    {
        $labId = Auth::user()->lab_id ?? 1;

        $query = Investigation::with([
            'patient', 'doctor', 'labtolab',
            'investigationTests.diagnosticstest',
            'investigationTests.approver'
        ])
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
        if ($request->filled('investigation_id')) {
            $query->where('id', $request->investigation_id);
        }

        $reports = $query->latest('id')->paginate(30)->withQueryString();

        return view('reports.index', compact('reports'));
    }
}
