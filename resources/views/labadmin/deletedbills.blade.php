@extends('layouts.app')

@section('title', 'Cancelled Invoices - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold text-danger"><i class="fas fa-trash"></i> Cancelled / Deleted Invoices</h3>
        <div class="card-tools ml-auto">
          <a href="{{ route('labadmin.bills') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back to Active Receipts</a>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-sm text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Bill #</th>
              <th>Patient Details</th>
              <th>Tests</th>
              <th>Amount</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bills as $b)
              <tr>
                <td><span class="badge badge-secondary">{{ $b->patient->unique_id ?? ('INV-' . $b->id) }}</span></td>
                <td>
                  <strong>{{ $b->patient->name ?? 'N/A' }}</strong><br>
                  <small>{{ $b->patient->phone ?? '' }}</small>
                </td>
                <td>
                  @foreach($b->investigationTests as $it)
                    <span class="badge badge-light">{{ $it->diagnosticstest->name ?? '' }}</span>
                  @endforeach
                </td>
                <td>Rs. {{ number_format($b->total_amount, 2) }}</td>
                <td>{{ $b->created_on ? \Carbon\Carbon::parse($b->created_on)->format('d/m/Y h:i A') : '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No cancelled invoices.</td>
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
