@extends('layouts.app')

@section('title', 'Superadmin Dashboard - RBJLIS Enterprise')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="font-weight-bold text-dark mb-1" style="letter-spacing: -0.02em;">Superadmin Overview</h3>
    <p class="text-muted small mb-0">System performance, licensed diagnostic laboratories, and master catalogs</p>
  </div>
  <div>
    <a href="{{ route('superadmin.addlab') }}" class="btn btn-primary shadow-sm">
      <i class="fas fa-plus mr-1"></i> Register New Laboratory
    </a>
  </div>
</div>

<!-- Modern Stat Cards Grid -->
<div class="row">
  <div class="col-lg-3 col-6 mb-4">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{ $labCount }}</h3>
        <p>Licensed Laboratories</p>
      </div>
      <div class="icon">
        <i class="fas fa-hospital-alt"></i>
      </div>
      <a href="{{ route('superadmin.lablist') }}" class="small-box-footer">Manage Labs <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6 mb-4">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>{{ $testCount }}</h3>
        <p>Master Diagnostic Tests</p>
      </div>
      <div class="icon">
        <i class="fas fa-vials"></i>
      </div>
      <a href="{{ route('superadmin.tests') }}" class="small-box-footer">View Catalog <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6 mb-4">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>{{ $departmentCount }}</h3>
        <p>Pathology Departments</p>
      </div>
      <div class="icon">
        <i class="fas fa-clinic-medical"></i>
      </div>
      <a href="{{ route('superadmin.departments') }}" class="small-box-footer">Departments <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6 mb-4">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>{{ $userCount }}</h3>
        <p>Total Staff & Doctors</p>
      </div>
      <div class="icon">
        <i class="fas fa-users"></i>
      </div>
      <a href="{{ route('superadmin.roles') }}" class="small-box-footer">Global Roles <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
  </div>
</div>

<!-- Recent Laboratories Card -->
<div class="card border-0 shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center bg-white">
    <h3 class="card-title font-weight-bold mb-0">Recently Enrolled Laboratories</h3>
    <div class="card-tools">
      <a href="{{ route('superadmin.lablist') }}" class="btn btn-xs btn-outline-primary">View All Labs</a>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Laboratory Name</th>
          <th>Administrator</th>
          <th>Email Address</th>
          <th>Phone</th>
          <th>Subscription Expiry</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentLabs as $lab)
          <tr>
            <td>
              <div class="d-flex align-items-center">
                <div class="rounded-circle bg-light text-primary font-weight-bold d-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px; font-size: 11px;">
                  {{ strtoupper(substr($lab->name, 0, 2)) }}
                </div>
                <div>
                  <strong>{{ $lab->name }}</strong><br>
                  <span class="badge badge-info" style="font-size: 10px;">{{ $lab->shortname }}</span>
                </div>
              </div>
            </td>
            <td>{{ $lab->contactname }}</td>
            <td>{{ $lab->email }}</td>
            <td>{{ $lab->phone }}</td>
            <td>
              <span class="badge badge-secondary">
                {{ $lab->sub_to ? $lab->sub_to->format('d M Y') : 'Active' }}
              </span>
            </td>
            <td>
              <a href="{{ route('superadmin.addlab', ['id' => $lab->id]) }}" class="btn btn-xs btn-outline-primary">
                <i class="far fa-edit"></i> Edit
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">No laboratories registered yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
