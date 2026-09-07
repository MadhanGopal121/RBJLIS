@extends('layouts.app')

@section('title', 'Pathology Reports Directory - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Filter -->
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title font-weight-bold"><i class="fas fa-search"></i> Search Patient Reports</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('reports.index') }}" method="GET">
          <div class="row">
            <div class="col-md-3">
              <label>From Date</label>
              <input type="date" name="fromdate" class="form-control" value="{{ request('fromdate') }}">
            </div>
            <div class="col-md-3">
              <label>To Date</label>
              <input type="date" name="todate" class="form-control" value="{{ request('todate') }}">
            </div>
            <div class="col-md-3">
              <label>Patient Name</label>
              <input type="text" name="patientname" class="form-control" placeholder="Search patient name" value="{{ request('patientname') }}">
            </div>
            <div class="col-md-3">
              <label>Phone Number</label>
              <input type="text" name="patientphone" class="form-control" placeholder="Search mobile" value="{{ request('patientphone') }}">
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter Reports</button>
              <a href="{{ route('reports.index') }}" class="btn btn-secondary ml-2">Reset</a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Reports Table -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Diagnostic Investigation Reports</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-sm text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Order / UID</th>
              <th>Patient Details</th>
              <th>Tests Conducted</th>
              <th>Referred By</th>
              <th>Status</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($reports as $r)
              @php
                $isAllApproved = $r->investigationTests->every(fn($it) => $it->approved_by > 0);
              @endphp
              <tr>
                <td><span class="badge badge-primary">{{ $r->patient->unique_id ?? ('INV-' . $r->id) }}</span></td>
                <td>
                  <strong>{{ $r->patient->name ?? 'N/A' }}</strong><br>
                  <small>{{ $r->patient->age ?? '' }} / {{ $r->patient->gender ?? '' }} | {{ $r->patient->phone ?? '' }}</small>
                </td>
                <td>
                  @foreach($r->investigationTests as $it)
                    <span class="badge badge-light border">{{ $it->diagnosticstest->name ?? '' }}</span>
                  @endforeach
                </td>
                <td>{{ $r->labtolab ? $r->labtolab->lab_name : ($r->doctor ? $r->doctor->doctor_name : ($r->refered_by ?: 'Self')) }}</td>
                <td>
                  @if($isAllApproved)
                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Approved & Ready</span>
                  @else
                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Testing in Progress</span>
                  @endif
                </td>
                <td>{{ $r->created_on ? \Carbon\Carbon::parse($r->created_on)->format('d M Y') : '-' }}</td>
                <td>
                  <a href="{{ route('print.report', ['id' => $r->id]) }}" target="_blank" class="btn btn-xs btn-primary"><i class="fas fa-file-pdf"></i> View Report</a>
                  <a href="{{ route('print.bill', ['id' => $r->id]) }}" target="_blank" class="btn btn-xs btn-info"><i class="fas fa-print"></i> Receipt</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No diagnostic reports found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="p-3">
          {{ $reports->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
