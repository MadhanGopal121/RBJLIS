@extends('layouts.app')

@section('title', 'Receipts & Billing - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Filter Card -->
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title font-weight-bold"><i class="fas fa-filter"></i> Filter Invoices & Receipts</h3>
        <div class="card-tools">
          <a href="{{ route('labadmin.deletedbills') }}" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Cancelled Invoices</a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('labadmin.bills') }}" method="GET">
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
              <input type="text" name="patientname" class="form-control" placeholder="Search by name" value="{{ request('patientname') }}">
            </div>
            <div class="col-md-3">
              <label>Phone Number</label>
              <input type="text" name="patientphone" class="form-control" placeholder="Search by phone" value="{{ request('patientphone') }}">
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search Receipts</button>
              <button type="submit" name="exportexcel" value="1" class="btn btn-success ml-2"><i class="fas fa-file-excel"></i> Export CSV</button>
              <a href="{{ route('labadmin.bills') }}" class="btn btn-secondary ml-2">Reset</a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Receipts Table -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Investigation Receipts</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-sm text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Bill #</th>
              <th>Patient Details</th>
              <th>Referred By</th>
              <th>Investigation Tests</th>
              <th>Total (Rs.)</th>
              <th>Paid (Rs.)</th>
              <th>Balance (Rs.)</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bills as $b)
              @php
                $paid = $b->labPayments->sum('payment_amount');
                $balance = $b->total_amount - ($paid + $b->discount);
              @endphp
              <tr id="bill_row_{{ $b->id }}">
                <td><span class="badge badge-primary">{{ $b->patient->unique_id ?? ('INV-' . $b->id) }}</span></td>
                <td>
                  <strong>{{ $b->patient->name ?? 'N/A' }}</strong><br>
                  <small>{{ $b->patient->age ?? '' }} / {{ $b->patient->gender ?? '' }} | {{ $b->patient->phone ?? '' }}</small>
                </td>
                <td>{{ $b->labtolab ? $b->labtolab->lab_name : ($b->doctor ? $b->doctor->doctor_name : ($b->refered_by ?: 'Self')) }}</td>
                <td>
                  @foreach($b->investigationTests as $it)
                    <span class="badge badge-light border">{{ $it->diagnosticstest->name ?? 'Test' }}</span>
                  @endforeach
                </td>
                <td>Rs. {{ number_format($b->total_amount, 2) }}</td>
                <td><span class="text-success font-weight-bold">Rs. {{ number_format($paid, 2) }}</span></td>
                <td>
                  @if($balance > 0)
                    <span class="badge badge-danger">Rs. {{ number_format($balance, 2) }}</span>
                  @else
                    <span class="badge badge-success">Cleared</span>
                  @endif
                </td>
                <td>{{ $b->created_on ? \Carbon\Carbon::parse($b->created_on)->format('d/m/Y h:i A') : '-' }}</td>
                <td>
                  <a href="{{ route('print.bill', ['id' => $b->id]) }}" target="_blank" class="btn btn-xs btn-info" title="Print Receipt"><i class="fas fa-print"></i> Receipt</a>
                  <a href="{{ route('print.report', ['id' => $b->id]) }}" target="_blank" class="btn btn-xs btn-primary" title="View Report"><i class="fas fa-file-pdf"></i> Report</a>
                  <button type="button" class="btn btn-xs btn-danger deletebillbtn" data-id="{{ $b->id }}" title="Cancel/Delete"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-4">No receipts found matching criteria.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="p-3">
          {{ $bills->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('.deletebillbtn').on('click', function() {
    if (!confirm('Are you sure you want to cancel and delete this invoice?')) return;
    var id = $(this).data('id');

    $.ajax({
      url: "{{ route('labadmin.deletebill') }}",
      type: "POST",
      data: {
        id: id,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function() {
        toastr.success('Invoice cancelled and moved to trash');
        $('#bill_row_' + id).fadeOut();
      },
      error: function() {
        toastr.error('Failed to cancel invoice');
      }
    });
  });
});
</script>
@endpush
