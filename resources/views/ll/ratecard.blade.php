@extends('layouts.app')

@section('title', 'Wholesale Rate Card - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold">B2B Partner Wholesale Diagnostic Rate Card</h3>
        <div class="card-tools ml-auto">
          <button onclick="window.print()" class="btn btn-sm btn-info"><i class="fas fa-print"></i> Print Rate Card</button>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Test Code</th>
              <th>Test Name</th>
              <th>Department</th>
              <th>Sample Type</th>
              <th>Standard Patient MRP</th>
              <th>Your B2B Contract Rate</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tests as $t)
              @php
                $contractRate = $specialPrices->has($t->id) ? $specialPrices->get($t->id)->sp_price : ($t->lab_price ?: $t->price);
              @endphp
              <tr>
                <td><code>{{ $t->test_code ?: ('T-' . $t->id) }}</code></td>
                <td><strong>{{ $t->name }}</strong></td>
                <td><span class="badge badge-secondary">{{ $t->department->name ?? 'General' }}</span></td>
                <td>{{ $t->sample_type ?: 'Blood / Serum' }}</td>
                <td><span class="text-muted"><del>Rs. {{ number_format($t->price, 2) }}</del></span></td>
                <td><span class="text-success font-weight-bold" style="font-size: 15px;">Rs. {{ number_format($contractRate, 2) }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
