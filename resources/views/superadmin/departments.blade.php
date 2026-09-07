@extends('layouts.app')

@section('title', 'Master Departments - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold">Master Pathology Departments</h3>
        <div class="card-tools ml-auto">
          <a href="{{ route('superadmin.adddepartment') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Department</a>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Department Name</th>
              <th>Total Tests</th>
              <th>Default Package Price</th>
              <th>Status</th>
              <th>Created On</th>
            </tr>
          </thead>
          <tbody>
            @foreach($departments as $dept)
              <tr>
                <td>{{ $dept->id }}</td>
                <td><strong>{{ $dept->name }}</strong></td>
                <td><span class="badge badge-info">{{ $dept->mastertests_count }} Tests</span></td>
                <td>Rs. {{ number_format($dept->price, 2) }}</td>
                <td><span class="badge badge-{{ $dept->status ? 'success' : 'danger' }}">{{ $dept->status ? 'Active' : 'Inactive' }}</span></td>
                <td>{{ $dept->created_at ? $dept->created_at->format('d/m/Y') : '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
