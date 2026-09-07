@extends('layouts.app')

@section('title', 'Referring Doctors - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold">Referring Doctors Directory</h3>
        <div class="card-tools ml-auto">
          <a href="{{ route('labadmin.adddoctor') }}" class="btn btn-primary"><i class="fas fa-user-md"></i> Add New Doctor</a>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Doctor Name</th>
              <th>Clinic / Hospital Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Commission / Discount (%)</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($doctors as $d)
              <tr>
                <td>{{ $d->id }}</td>
                <td><strong>Dr. {{ $d->doctor_name }}</strong></td>
                <td>{{ $d->clinic_name ?? '-' }}</td>
                <td>{{ $d->phone ?? '-' }}</td>
                <td>{{ $d->email ?? '-' }}</td>
                <td><span class="badge badge-success">{{ $d->discount }} %</span></td>
                <td>
                  <a href="{{ route('labadmin.adddoctor', ['id' => $d->id]) }}" class="btn btn-sm btn-outline-primary"><i class="far fa-edit"></i> Edit</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No referring doctors registered yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
