@extends('layouts.app')

@section('title', 'Registered Patients - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Patient Master Directory</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Patient ID</th>
              <th>Full Name</th>
              <th>Age / Gender</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Aadhar #</th>
              <th>Registered Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($patients as $p)
              <tr>
                <td><span class="badge badge-primary">{{ $p->unique_id }}</span></td>
                <td><strong>{{ $p->title }} {{ $p->name }}</strong></td>
                <td>{{ $p->age }} Yrs / {{ $p->gender }}</td>
                <td>{{ $p->phone ?? '-' }}</td>
                <td>{{ $p->email ?? '-' }}</td>
                <td>{{ $p->aadhar ?? '-' }}</td>
                <td>{{ $p->created_on ? \Carbon\Carbon::parse($p->created_on)->format('d/m/Y') : '-' }}</td>
                <td>
                  <a href="{{ route('labadmin.editpatient', ['id' => $p->id]) }}" class="btn btn-sm btn-outline-primary"><i class="far fa-edit"></i> Edit</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">No patients registered yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="p-3">
          {{ $patients->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
