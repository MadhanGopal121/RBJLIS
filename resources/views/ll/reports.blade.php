@extends('layouts.app')

@section('title', 'Completed Test Reports - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Search Patient Reports</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('ll.reports') }}" method="GET" class="form-inline">
          <input type="text" name="patientname" class="form-control mr-2" placeholder="Search patient name..." value="{{ request('patientname') }}">
          <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
          <a href="{{ route('ll.reports') }}" class="btn btn-secondary ml-2">Reset</a>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">B2B Outsource Investigation Reports</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-sm text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Order #</th>
              <th>Patient Name</th>
              <th>Tests Ordered</th>
              <th>Amount (Rs.)</th>
              <th>Status</th>
              <th>Order Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($orders as $o)
              @php
                $isCompleted = $o->investigationTests->every(fn($t) => $t->approved_by > 0);
              @endphp
              <tr>
                <td><strong>INV-{{ $o->id }}</strong></td>
                <td>{{ $o->patient->name ?? 'N/A' }} ({{ $o->patient->age ?? '' }} / {{ $o->patient->gender ?? '' }})</td>
                <td>
                  @foreach($o->investigationTests as $it)
                    <span class="badge badge-light border">{{ $it->diagnosticstest->name ?? '' }}</span>
                  @endforeach
                </td>
                <td>Rs. {{ number_format($o->total_amount, 2) }}</td>
                <td>
                  @if($isCompleted)
                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Ready</span>
                  @else
                    <span class="badge badge-warning"><i class="fas fa-spinner fa-spin"></i> In Testing</span>
                  @endif
                </td>
                <td>{{ $o->created_on ? \Carbon\Carbon::parse($o->created_on)->format('d/m/Y') : '-' }}</td>
                <td>
                  @if($isCompleted)
                    <a href="{{ route('print.report', ['id' => $o->id]) }}" target="_blank" class="btn btn-xs btn-primary">
                      <i class="fas fa-file-pdf"></i> Download Report
                    </a>
                  @else
                    <span class="text-muted"><small>Processing</small></span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No investigation reports found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="p-3">
          {{ $orders->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
