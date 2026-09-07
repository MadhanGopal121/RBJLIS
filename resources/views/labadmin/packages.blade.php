@extends('layouts.app')

@section('title', 'Packages & Profiles - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold">Diagnostic Packages & Profiles</h3>
        <div class="card-tools ml-auto">
          <a href="{{ route('labadmin.addpackage') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create Package</a>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Package / Profile Name</th>
              <th>Included Tests</th>
              <th>Patient Price</th>
              <th>B2B / Partner Price</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($packages as $pkg)
              <tr>
                <td>{{ $pkg->id }}</td>
                <td><strong>{{ $pkg->name }}</strong></td>
                <td>
                  @foreach($pkg->profileTests as $pt)
                    <span class="badge badge-info">{{ $pt->diagnosticstest->name ?? '' }}</span>
                  @endforeach
                </td>
                <td>Rs. {{ number_format($pkg->price, 2) }}</td>
                <td>Rs. {{ number_format($pkg->lab_price, 2) }}</td>
                <td>
                  <a href="{{ route('labadmin.addpackage', ['id' => $pkg->id]) }}" class="btn btn-sm btn-outline-primary"><i class="far fa-edit"></i> Edit</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">No packages or profiles configured yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
