@extends('layouts.app')

@section('title', 'B2B Partner Lab Payments - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-4">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Receive B2B Partner Payment</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('frontoffice.labpayment') }}" method="POST">
          @csrf
          <div class="form-group">
            <label for="labtolab_id">Partner Laboratory <span class="text-danger">*</span></label>
            <select name="labtolab_id" id="labtolab_id" class="form-control" required>
              <option value="">-- Select B2B Lab --</option>
              @foreach($associatedlabs as $l)
                <option value="{{ $l->id }}">{{ $l->lab_name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label for="amount">Payment Amount (Rs.) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" required placeholder="0.00">
          </div>

          <div class="form-group">
            <label for="paymenttype">Payment Mode</label>
            <select name="paymenttype" id="paymenttype" class="form-control">
              <option value="Cash">Cash</option>
              <option value="UPI">UPI / Bank Transfer</option>
              <option value="Cheque">Cheque</option>
              <option value="Card">Card</option>
            </select>
          </div>

          <div class="form-group">
            <label for="trans_number">Transaction / Cheque Ref #</label>
            <input type="text" name="trans_number" id="trans_number" class="form-control" placeholder="Reference Number">
          </div>

          <button type="submit" class="btn btn-success btn-block"><i class="fas fa-save"></i> Save Payment</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Recent B2B Partner Payments</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Partner Lab</th>
              <th>Mode</th>
              <th>Amount (Rs.)</th>
              <th>Transaction #</th>
              <th>Received Date</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $p)
              <tr>
                <td>{{ $p->id }}</td>
                <td><strong>{{ $p->labtolab->lab_name ?? 'B2B Partner' }}</strong></td>
                <td><span class="badge badge-info">{{ $p->paymenttype }}</span></td>
                <td><span class="text-success font-weight-bold">Rs. {{ number_format($p->payment_amount, 2) }}</span></td>
                <td>{{ $p->trans_number ?? '-' }}</td>
                <td>{{ $p->received_date ? \Carbon\Carbon::parse($p->received_date)->format('d/m/Y h:i A') : '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">No B2B payment transactions recorded.</td>
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
