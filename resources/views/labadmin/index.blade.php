@extends('layouts.app')

@section('title', 'Labadmin Dashboard - ' . (auth()->user()->lab->name ?? 'RBJLIS'))

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="font-weight-bold text-dark mb-1" style="letter-spacing: -0.02em;">
      {{ auth()->user()->lab->name ?? 'Diagnostic Center' }} Dashboard
    </h3>
    <p class="text-muted small mb-0">Daily diagnostic operations, patient counts, and investigation pipelines</p>
  </div>
  <div class="d-flex" style="gap: 8px;">
    <a href="{{ route('frontoffice.index') }}" class="btn btn-primary shadow-sm">
      <i class="fas fa-plus mr-1"></i> New Patient Booking
    </a>
    <a href="{{ route('labadmin.bills') }}" class="btn btn-outline-primary">
      <i class="fas fa-receipt mr-1"></i> View Receipts
    </a>
  </div>
</div>

<!-- Modern Stat Boxes -->
<div class="row">
  <div class="col-lg-3 col-6 mb-4">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ $patientCount }}</h3>
        <p>Registered Patients</p>
      </div>
      <div class="icon">
        <i class="fas fa-user-injured"></i>
      </div>
      <a href="{{ route('labadmin.patients') }}" class="small-box-footer">Patients Directory <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6 mb-4">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>{{ $testCount }}</h3>
        <p>Configured Tests</p>
      </div>
      <div class="icon">
        <i class="fas fa-vials"></i>
      </div>
      <a href="{{ route('labadmin.labtests') }}" class="small-box-footer">Manage Tariff <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6 mb-4">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>{{ $packageCount }}</h3>
        <p>Health Profiles / Packages</p>
      </div>
      <div class="icon">
        <i class="fas fa-cubes"></i>
      </div>
      <a href="{{ route('labadmin.packages') }}" class="small-box-footer">View Packages <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6 mb-4">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>{{ $investigationCount }}</h3>
        <p>Total Orders Booked</p>
      </div>
      <div class="icon">
        <i class="fas fa-file-medical-alt"></i>
      </div>
      <a href="{{ route('labadmin.bills') }}" class="small-box-footer">Invoice Register <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
  </div>
</div>

<!-- Real-time Today's Investigations -->
<div class="card border-0 shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center bg-white">
    <h3 class="card-title font-weight-bold mb-0">Today's Patient Investigations</h3>
    <div class="card-tools">
      <a href="{{ route('labadmin.lastdayreport') }}" class="btn btn-xs btn-outline-primary">
        <i class="fas fa-calendar-day mr-1"></i> Daily Register
      </a>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Order Reference</th>
          <th>Patient Details</th>
          <th>Requested Diagnostic Tests</th>
          <th>Order Total</th>
          <th>Booked Time</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($todayInvestigations as $inv)
          <tr>
            <td>
              <span class="badge badge-primary font-weight-bold">INV-{{ $inv->id }}</span>
            </td>
            <td>
              <strong>{{ $inv->patient->name ?? 'N/A' }}</strong><br>
              <small class="text-muted">{{ $inv->patient->unique_id ?? '' }} | {{ $inv->patient->age ?? '' }}/{{ $inv->patient->gender ?? '' }}</small>
            </td>
            <td>
              @foreach($inv->investigationTests as $it)
                <span class="badge badge-secondary mb-1">{{ $it->diagnosticstest->name ?? 'Test' }}</span>
              @endforeach
            </td>
            <td><strong class="text-dark">Rs. {{ number_format($inv->total_amount, 2) }}</strong></td>
            <td><small class="text-muted">{{ $inv->created_on ? \Carbon\Carbon::parse($inv->created_on)->format('h:i A') : '-' }}</small></td>
            <td>
              <a href="{{ route('print.bill', ['id' => $inv->id]) }}" target="_blank" class="btn btn-xs btn-outline-primary" title="Print Receipt">
                <i class="fas fa-print"></i>
              </a>
              <a href="{{ route('print.report', ['id' => $inv->id]) }}" target="_blank" class="btn btn-xs btn-success" title="View Report">
                <i class="fas fa-file-pdf"></i>
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">No investigations booked today yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
