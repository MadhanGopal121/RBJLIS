@extends('layouts.app')

@section('title', 'Laboratory Staff Users - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold">Staff & Technical Users</h3>
        <div class="card-tools ml-auto">
          <a href="{{ route('labadmin.addlabuser') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Staff User</a>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Role</th>
              <th>Assigned Departments</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $u)
              <tr>
                <td>{{ $u->id }}</td>
                <td><strong>{{ $u->name }}</strong><br><small class="text-muted">{{ $u->designation }}</small></td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->phone ?? '-' }}</td>
                <td><span class="badge badge-info">{{ $u->role->name ?? 'Staff' }}</span></td>
                <td>
                  @foreach($u->userDepartments as $ud)
                    <span class="badge badge-secondary">{{ $ud->department->name ?? '' }}</span>
                  @endforeach
                </td>
                <td><span class="badge badge-{{ $u->status ? 'success' : 'danger' }}">{{ $u->status ? 'Active' : 'Inactive' }}</span></td>
                <td>
                  <a href="{{ route('labadmin.addlabuser', ['id' => $u->id]) }}" class="btn btn-sm btn-outline-primary"><i class="far fa-edit"></i> Edit</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">No staff users registered.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
