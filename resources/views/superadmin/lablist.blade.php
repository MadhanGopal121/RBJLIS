@extends('layouts.app')

@section('title', 'List of Laboratories - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold">List of Diagnostic Laboratories</h3>
        <div class="card-tools ml-auto">
          <a href="{{ route('superadmin.addlab') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Lab</a>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Lab Name</th>
              <th>Allowed Users</th>
              <th>Contact Info</th>
              <th>Subscription Period</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($labs as $lab)
              <tr>
                <td>{{ $lab->id }}</td>
                <td>
                  <strong>{{ $lab->name }}</strong>
                  @if($lab->shortname)
                    <span class="badge badge-info">{{ $lab->shortname }}</span>
                  @endif
                </td>
                <td>{{ $lab->user_count }}</td>
                <td>
                  <span class="text-primary font-weight-bold">{{ $lab->contactname }}</span><br>
                  <small><i class="fas fa-envelope"></i> {{ $lab->email }}</small><br>
                  <small><i class="fas fa-phone"></i> {{ $lab->phone }}</small>
                </td>
                <td>
                  <small class="badge badge-secondary">
                    {{ $lab->sub_from ? $lab->sub_from->format('d/m/Y') : '-' }} to {{ $lab->sub_to ? $lab->sub_to->format('d/m/Y') : '-' }}
                  </small>
                </td>
                <td>
                  @if($lab->status)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-danger">Inactive</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('superadmin.addlab', ['id' => $lab->id]) }}" class="btn btn-sm btn-outline-primary" title="Edit Lab">
                    <i class="far fa-edit"></i> Edit
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No laboratories found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
