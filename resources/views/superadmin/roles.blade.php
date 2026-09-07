@extends('layouts.app')

@section('title', 'Global Roles - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">System Roles & Permissions Structure</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Role ID</th>
              <th>Role Name</th>
              <th>Assigned Users</th>
              <th>Scope</th>
            </tr>
          </thead>
          <tbody>
            @foreach($roles as $r)
              <tr>
                <td>{{ $r->id }}</td>
                <td><strong>{{ $r->name }}</strong></td>
                <td><span class="badge badge-primary">{{ $r->users_count }} Users</span></td>
                <td>
                  @if($r->id == 1)
                    <span class="badge badge-danger">Super Administrator</span>
                  @elseif($r->id == 2)
                    <span class="badge badge-warning">Lab Administrator</span>
                  @elseif($r->id == 3)
                    <span class="badge badge-info">Front Office / Billing</span>
                  @elseif(in_array($r->id, [4, 5, 6, 7]))
                    <span class="badge badge-success">Lab Technician / Pathologist</span>
                  @elseif($r->id == 8)
                    <span class="badge badge-secondary">B2B Partner Lab</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
