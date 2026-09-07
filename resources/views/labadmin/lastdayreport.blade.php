@extends('layouts.app')

@section('title', 'Daily Register & Transaction Report - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Filter -->
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title font-weight-bold"><i class="fas fa-calendar-alt"></i> Daily Collection Register</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('labadmin.lastdayreport') }}" method="GET" class="form-inline">
          <label class="mr-2">Select Date:</label>
          <input type="date" name="reportdate" class="form-control mr-2" value="{{ $date }}">
          <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Load Report</button>
        </form>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
      <div class="col-md-4">
        <div class="info-box bg-info">
          <span class="info-box-icon"><i class="fas fa-file-invoice"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Billed</span>
            <span class="info-box-number">Rs. {{ number_format($totalCollection, 2) }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box bg-success">
          <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Received Cash/Online</span>
            <span class="info-box-number">Rs. {{ number_format($totalReceived, 2) }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="info-box bg-warning">
          <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Total Discounts Given</span>
            <span class="info-box-number">Rs. {{ number_format($totalDiscount, 2) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Transaction List -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Day Transactions: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-sm text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Bill #</th>
              <th>Patient</th>
              <th>Age/Gender</th>
              <th>Phone</th>
              <th>Tests</th>
              <th>Total (Rs.)</th>
              <th>Paid (Rs.)</th>
              <th>Discount (Rs.)</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            @forelse($investigations as $inv)
              @php
                $paid = $inv->labPayments->sum('payment_amount');
              @endphp
              <tr>
                <td><strong>{{ $inv->patient->unique_id ?? ('INV-' . $inv->id) }}</strong></td>
                <td>{{ $inv->patient->name ?? 'N/A' }}</td>
                <td>{{ $inv->patient->age ?? '' }} / {{ $inv->patient->gender ?? '' }}</td>
                <td>{{ $inv->patient->phone ?? '' }}</td>
                <td>
                  @foreach($inv->investigationTests as $it)
                    <span class="badge badge-light">{{ $it->diagnosticstest->name ?? '' }}</span>
                  @endforeach
                </td>
                <td>Rs. {{ number_format($inv->total_amount, 2) }}</td>
                <td><span class="text-success font-weight-bold">Rs. {{ number_format($paid, 2) }}</span></td>
                <td>Rs. {{ number_format($inv->discount, 2) }}</td>
                <td>{{ $inv->created_on ? \Carbon\Carbon::parse($inv->created_on)->format('h:i A') : '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-4">No transactions recorded on this date.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
