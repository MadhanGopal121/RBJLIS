<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FrontofficeController;
use App\Http\Controllers\LabadminController;
use App\Http\Controllers\LabinvestigationController;
use App\Http\Controllers\LlController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SuperadminController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Online QR Report Verification / Download
Route::get('/report/{path}', [ReportController::class, 'index'])->name('report.view');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // User Profile & Password
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/index', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/signature', [ProfileController::class, 'updateSignature'])->name('profile.signature');
    Route::get('/password/changepassword', [PasswordController::class, 'changepassword'])->name('password.change');
    Route::get('/password/change', [PasswordController::class, 'changepassword'])->name('password.changepassword');
    Route::post('/password/changepassword', [PasswordController::class, 'updatePassword'])->name('password.change.post');

    // Superadmin Module
    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/', [SuperadminController::class, 'index'])->name('index');
        Route::get('/lablist', [SuperadminController::class, 'lablist'])->name('lablist');
        Route::get('/addlab', [SuperadminController::class, 'showAddLab'])->name('addlab');
        Route::post('/addlab', [SuperadminController::class, 'saveLab'])->name('addlab.post');
        Route::get('/subscriptions', [SuperadminController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/departments', [SuperadminController::class, 'departments'])->name('departments');
        Route::get('/adddepartment', [SuperadminController::class, 'showAddDepartment'])->name('adddepartment');
        Route::post('/adddepartment', [SuperadminController::class, 'saveDepartment'])->name('adddepartment.post');
        Route::get('/tests', [SuperadminController::class, 'tests'])->name('tests');
        Route::get('/addtest', [SuperadminController::class, 'showAddTest'])->name('addtest');
        Route::post('/addtest', [SuperadminController::class, 'saveTest'])->name('addtest.post');
        Route::get('/roles', [SuperadminController::class, 'roles'])->name('roles');
    });

    // Lab Admin Module
    Route::prefix('labadmin')->name('labadmin.')->group(function () {
        Route::get('/', [LabadminController::class, 'index'])->name('index');
        Route::get('/packages', [LabadminController::class, 'packages'])->name('packages');
        Route::get('/addpackage', [LabadminController::class, 'showAddPackage'])->name('addpackage');
        Route::post('/addpackage', [LabadminController::class, 'savePackage'])->name('addpackage.post');
        Route::get('/labtests', [LabadminController::class, 'labtests'])->name('labtests');
        Route::post('/updatetest', [LabadminController::class, 'updatetest'])->name('updatetest');
        Route::post('/updatetestnotes', [LabadminController::class, 'updatetest'])->name('updatetestnotes');
        Route::get('/listparameters', [LabadminController::class, 'listparameters'])->name('listparameters');
        Route::post('/addparameters', [LabadminController::class, 'addparameters'])->name('addparameters');
        Route::post('/updatetestparams', [LabadminController::class, 'updatetestparams'])->name('updatetestparams');
        Route::post('/deleteparameter', [LabadminController::class, 'deleteparameter'])->name('deleteparameter');
        Route::get('/labusers', [LabadminController::class, 'labusers'])->name('labusers');
        Route::get('/createlabuser', [LabadminController::class, 'showCreateLabUser'])->name('addlabuser');
        Route::post('/createlabuser', [LabadminController::class, 'saveLabUser'])->name('addlabuser.post');
        Route::get('/doctors', [LabadminController::class, 'doctors'])->name('doctors');
        Route::get('/adddoctor', [LabadminController::class, 'showAddDoctor'])->name('adddoctor');
        Route::post('/adddoctor', [LabadminController::class, 'saveDoctor'])->name('adddoctor.post');
        Route::get('/patients', [LabadminController::class, 'patients'])->name('patients');
        Route::get('/editpatient', [LabadminController::class, 'showEditPatient'])->name('editpatient');
        Route::post('/editpatient', [LabadminController::class, 'updatePatient'])->name('editpatient.post');
        Route::get('/labs', [LabadminController::class, 'labs'])->name('labs');
        Route::get('/createlab', [LabadminController::class, 'showCreateLab'])->name('addlabtolab');
        Route::post('/createlab', [LabadminController::class, 'saveLab'])->name('addlabtolab.post');
        Route::get('/speciallabrates', [LabadminController::class, 'speciallabrates'])->name('speciallabrates');
        Route::post('/addspecialrates', [LabadminController::class, 'addspecialrates'])->name('addspecialrates');
        Route::get('/bills', [LabadminController::class, 'bills'])->name('bills');
        Route::get('/deletedbills', [LabadminController::class, 'deletedbills'])->name('deletedbills');
        Route::post('/deletebill', [LabadminController::class, 'deletebill'])->name('deletebill');
        Route::get('/settings', [LabadminController::class, 'settings'])->name('settings');
        Route::post('/settings', [LabadminController::class, 'saveSettings'])->name('settings.post');
        Route::get('/lastdayreport', [LabadminController::class, 'lastdayreport'])->name('lastdayreport');
    });

    // Front Office Module
    Route::prefix('frontoffice')->name('frontoffice.')->group(function () {
        Route::get('/', [FrontofficeController::class, 'index'])->name('index');
        Route::post('/createinvestigation', [FrontofficeController::class, 'createinvestigation'])->name('createinvestigation');
        Route::get('/bills', [FrontofficeController::class, 'bills'])->name('bills');
        Route::post('/collectpayment', [FrontofficeController::class, 'collectpayment'])->name('collectpayment');
        Route::get('/labpayment', [FrontofficeController::class, 'labpayment'])->name('labpayment');
        Route::post('/labpayment', [FrontofficeController::class, 'labpayment'])->name('labpayment.post');
        Route::get('/gettest', [FrontofficeController::class, 'gettest'])->name('gettest');
        Route::get('/getpatient', [FrontofficeController::class, 'getpatient'])->name('getpatient');
        Route::post('/declinetest', [FrontofficeController::class, 'declinetest'])->name('declinetest');
    });

    // Lab Investigation & Phlebotomy Module
    Route::prefix('labinvestigation')->name('labinvestigation.')->group(function () {
        Route::get('/', [LabinvestigationController::class, 'index'])->name('index');
        Route::get('/pendingtest', [LabinvestigationController::class, 'pendingtest'])->name('pendingtest');
        Route::get('/processedtests', [LabinvestigationController::class, 'processedtests'])->name('processedtests');
        Route::get('/verifiedtest', [LabinvestigationController::class, 'verifiedtest'])->name('verifiedtest');
        Route::get('/approvedtests', [LabinvestigationController::class, 'approvedtests'])->name('approvedtests');
        Route::get('/collectsample', [LabinvestigationController::class, 'collectsample'])->name('collectsample');
        Route::post('/collectsample', [LabinvestigationController::class, 'collectsample'])->name('collectsample.post');
        Route::get('/getparameters', [LabinvestigationController::class, 'getparameters'])->name('getparameters');
        Route::post('/updateresult', [LabinvestigationController::class, 'updateresult'])->name('updateresult');
        Route::post('/declinetest', [FrontofficeController::class, 'declinetest'])->name('declinetest');
        Route::post('/multireportappove', [LabinvestigationController::class, 'multireportappove'])->name('multireportappove');
    });

    // Lab-to-Lab (B2B) Portal
    Route::prefix('ll')->name('ll.')->group(function () {
        Route::get('/', [LlController::class, 'index'])->name('index');
        Route::get('/createbill', [LlController::class, 'createbill'])->name('createbill');
        Route::post('/createbill', [LlController::class, 'createinvestigation'])->name('createbill.post');
        Route::get('/reports', [LlController::class, 'reports'])->name('reports');
        Route::get('/payments', [LlController::class, 'payments'])->name('payments');
        Route::get('/ratecard', [LlController::class, 'ratecard'])->name('ratecard');
    });

    // Search Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');

    // Printing Receipts & Reports
    Route::get('/print/bill', [PrintController::class, 'printbill'])->name('print.bill');
    Route::get('/print/report', [PrintController::class, 'printreport'])->name('print.report');
});