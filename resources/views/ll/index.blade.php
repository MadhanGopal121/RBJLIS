@extends('layouts.app')

@section('title', 'B2B Partner Portal - RBJLIS')

@section('content')
<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h3 class="font-weight-bold text-dark mb-1" style="letter-spacing: -0.02em;">B2B Outsource Partner Dashboard</h3>
    <p class="text-muted small mb-0">Manage diagnostic referrals, wholesale rate cards, and financial statements</p>
  </div>
  <div class="mt-2 mt-md-0">
    <a href="{{ route('ll.createbill') }}" class="btn btn-primary shadow-sm font-weight-bold">
      <i class="fas fa-plus mr-1"></i> Place New Investigation Order
    </a>
  </div>
</div>

<!-- Stat Cards -->
<div class="row">
  <div class="col-md-4 mb-4">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>Rs. {{ number_format($totalBilled, 2) }}</h3>
        <p>Total Test Orders Placed</p>
      </div>
      <div class="icon">
        <i class="fas fa-file-invoice-dollar"></i>
      </div>
      <a href="{{ route('ll.reports') }}" class="small-box-footer">View Outsource Orders <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>Rs. {{ number_format($totalPaid, 2) }}</h3>
        <p>Total Settled & Paid</p>
      </div>
      <div class="icon">
        <i class="fas fa-money-check-alt"></i>
      </div>
      <a href="{{ route('ll.payments') }}" class="small-box-footer">Payment Ledger <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
  </div>

  <div class="col-md-4 mb-4">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>Rs. {{ number_format($dueBalance, 2) }}</h3>
        <p>Current Balance Due</p>
      </div>
      <div class="icon">
        <i class="fas fa-wallet"></i>
      </div>
      <a href="{{ route('ll.payments') }}" class="small-box-footer">View Ledger Statement <i class="fas fa-arrow-circle-right ml-1"></i></a>
    </div>
  </div>
</div>

<!-- Recent Orders -->
<div class="card border-0 shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center bg-white">
    <h3 class="card-title font-weight-bold mb-0">Recent Outsource Test Orders</h3>
    <div class="card-tools">
      <a href="{{ route('ll.reports') }}" class="btn btn-xs btn-outline-primary">View All Reports</a>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Order Reference</th>
          <th>Patient Name</th>
          <th>Ordered Diagnostic Tests</th>
          <th>Wholesale Amount</th>
          <th>Processing Status</th>
          <th>Order Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentOrders as $o)
          <tr>
            <td><span class="badge badge-primary font-weight-bold">INV-{{ $o->id }}</span></td>
            <td><strong>{{ $o->patient->name ?? 'N/A' }}</strong></td>
            <td>
              @foreach($o->investigationTests as $it)
                <span class="badge badge-secondary mb-1">{{ $it->diagnosticstest->name ?? '' }}</span>
              @endforeach
            </td>
            <td><strong>Rs. {{ number_format($o->total_amount, 2) }}</strong></td>
            <td>
              @if($o->investigationTests->every(fn($t) => $t->approved_by > 0))
                <span class="badge badge-success"><i class="fas fa-check-circle"></i> Ready & Signed</span>
              @else
                <span class="badge badge-warning"><i class="fas fa-spinner fa-spin"></i> In Testing</span>
              @endif
            </td>
            <td><small class="text-muted">{{ $o->created_on ? \Carbon\Carbon::parse($o->created_on)->format('d M Y') : '-' }}</small></td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">No B2B orders submitted yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
