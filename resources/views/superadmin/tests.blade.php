@extends('layouts.app')

@section('title', 'Master Diagnostic Tests - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold">Master Diagnostic Tests Directory</h3>
        <div class="card-tools ml-auto">
          <a href="{{ route('superadmin.addtest') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Master Test</a>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Test Name</th>
              <th>Department</th>
              <th>Reference Value</th>
              <th>Units</th>
              <th>Price</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($mtdata as $t)
              <tr>
                <td>{{ $t->id }}</td>
                <td><strong>{{ $t->name }}</strong></td>
                <td><span class="badge badge-info">{{ $t->department->name ?? 'General' }}</span></td>
                <td>{{ $t->reference_val ?? '-' }}</td>
                <td>{{ $t->units ?? '-' }}</td>
                <td>Rs. {{ number_format($t->price, 2) }}</td>
                <td>
                  <a href="{{ route('superadmin.addtest', ['id' => $t->id]) }}" class="btn btn-sm btn-outline-primary"><i class="far fa-edit"></i> Edit</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No master tests created yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
