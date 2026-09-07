@extends('layouts.app')

@section('title', 'B2B Payment Ledger - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Summary -->
    <div class="row">
      <div class="col-md-4">
        <div class="info-box bg-info">
          <span class="info-box-icon"><i class="fas fa-file-invoice"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Outsource Billed</span>
            <span class="info-box-number">Rs. {{ number_format($totalBilled, 2) }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box bg-success">
          <span class="info-box-icon"><i class="fas fa-check-double"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Amount Paid</span>
            <span class="info-box-number">Rs. {{ number_format($totalPaid, 2) }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box bg-danger">
          <span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Balance Outstanding Due</span>
            <span class="info-box-number">Rs. {{ number_format($dueBalance, 2) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Payments Ledger -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Settlement & Payment Transaction History</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Receipt / Trans #</th>
              <th>Payment Mode</th>
              <th>Amount Settled (Rs.)</th>
              <th>Transaction Reference</th>
              <th>Date Paid</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $p)
              <tr>
                <td><strong>RCPT-{{ $p->id }}</strong></td>
                <td><span class="badge badge-info">{{ $p->paymenttype }}</span></td>
                <td><span class="text-success font-weight-bold">Rs. {{ number_format($p->payment_amount, 2) }}</span></td>
                <td>{{ $p->trans_number ?? '-' }}</td>
                <td>{{ $p->received_date ? \Carbon\Carbon::parse($p->received_date)->format('d M Y h:i A') : '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No payment transactions recorded.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="p-3">
          {{ $payments->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
