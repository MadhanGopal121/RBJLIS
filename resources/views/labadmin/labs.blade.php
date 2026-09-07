@extends('layouts.app')

@section('title', 'Lab-to-Lab Centers (B2B) - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold">Partner & Outsource Laboratories (B2B)</h3>
        <div class="card-tools ml-auto">
          <a href="{{ route('labadmin.addlabtolab') }}" class="btn btn-primary"><i class="fas fa-handshake"></i> Add Partner Center</a>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Center / Lab Name</th>
              <th>Contact Person</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Special Rate Card</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($labs as $l)
              <tr>
                <td>{{ $l->id }}</td>
                <td><strong>{{ $l->lab_name }}</strong></td>
                <td>{{ $l->contact_name ?? '-' }}</td>
                <td>{{ $l->contact_email ?? '-' }}</td>
                <td>{{ $l->contact_phone ?? '-' }}</td>
                <td>
                  <a href="{{ route('labadmin.speciallabrates', ['llid' => $l->id]) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-tags"></i> Special Rates
                  </a>
                </td>
                <td><span class="badge badge-{{ $l->status ? 'success' : 'danger' }}">{{ $l->status ? 'Active' : 'Inactive' }}</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No B2B partner laboratories added yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
