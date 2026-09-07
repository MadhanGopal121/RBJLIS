@extends('layouts.app')

@section('title', 'Front Office Invoices - RBJLIS')

@section('content')
<!-- Payment Collection Modal -->
<div class="modal fade" id="collectPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('frontoffice.collectpayment') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title font-weight-bold">Collect Balance Payment</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="investigation_id" id="modal_inv_id">
          <div class="form-group">
            <label>Patient Name</label>
            <p id="modal_patient_name" class="font-weight-bold mb-1"></p>
          </div>
          <div class="form-group">
            <label>Remaining Due (Rs.)</label>
            <p id="modal_due_amt" class="text-danger font-weight-bold mb-1"></p>
          </div>
          <div class="form-group">
            <label for="modal_amount">Paying Amount (Rs.) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="amount" id="modal_amount" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="modal_pay_method">Payment Mode</label>
            <select name="paymenttype" id="modal_pay_method" class="form-control">
              <option value="Cash">Cash</option>
              <option value="UPI">UPI / GPay / PhonePe</option>
              <option value="Card">Debit / Credit Card</option>
              <option value="NetBanking">Net Banking</option>
            </select>
          </div>
          <div class="form-group">
            <label for="modal_trans_num">Transaction ID / Ref #</label>
            <input type="text" name="trans_number" id="modal_trans_num" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Record Payment</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <!-- Filters -->
    <div class="card card-default">
      <div class="card-header">
        <h3 class="card-title font-weight-bold"><i class="fas fa-filter"></i> Search & Filter Receipts</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('frontoffice.bills') }}" method="GET">
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
              <input type="text" name="patientname" class="form-control" placeholder="Search name" value="{{ request('patientname') }}">
            </div>
            <div class="col-md-3">
              <label>Phone Number</label>
              <input type="text" name="patientphone" class="form-control" placeholder="Search phone" value="{{ request('patientphone') }}">
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
              <a href="{{ route('frontoffice.bills') }}" class="btn btn-secondary ml-2">Reset</a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Bills Table -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Patient Investigation Receipts</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-sm text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Receipt #</th>
              <th>Patient Information</th>
              <th>Referred Doctor / Lab</th>
              <th>Tests</th>
              <th>Total</th>
              <th>Paid</th>
              <th>Due Balance</th>
              <th>Created Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bills as $b)
              @php
                $paid = $b->labPayments->sum('payment_amount');
                $balance = $b->total_amount - ($paid + $b->discount);
              @endphp
              <tr>
                <td><span class="badge badge-primary">{{ $b->patient->unique_id ?? ('INV-' . $b->id) }}</span></td>
                <td>
                  <strong>{{ $b->patient->name ?? 'N/A' }}</strong><br>
                  <small>{{ $b->patient->age ?? '' }} / {{ $b->patient->gender ?? '' }} | {{ $b->patient->phone ?? '' }}</small>
                </td>
                <td>{{ $b->labtolab ? $b->labtolab->lab_name : ($b->doctor ? $b->doctor->doctor_name : ($b->refered_by ?: 'Self')) }}</td>
                <td>
                  @foreach($b->investigationTests as $it)
                    <span class="badge badge-light border">{{ $it->diagnosticstest->name ?? '' }}</span>
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
                  @if($balance > 0)
                    <button type="button" class="btn btn-xs btn-warning collectpaybtn" data-id="{{ $b->id }}" data-name="{{ $b->patient->name ?? 'N/A' }}" data-due="{{ $balance }}">
                      <i class="fas fa-hand-holding-usd"></i> Pay
                    </button>
                    <a href="{{ route('payment.checkout', ['id' => $b->id]) }}" target="_blank" class="btn btn-xs btn-success" title="Online UPI / Stripe Portal">
                      <i class="fas fa-qrcode"></i> UPI / Card
                    </a>
                  @endif
                  <a href="{{ route('print.bill', ['id' => $b->id]) }}" target="_blank" class="btn btn-xs btn-info" title="Print Receipt"><i class="fas fa-print"></i> Receipt</a>
                  <a href="{{ route('print.report', ['id' => $b->id]) }}" target="_blank" class="btn btn-xs btn-primary" title="View Report"><i class="fas fa-file-pdf"></i> Report</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-muted py-4">No receipts found.</td>
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
  $('.collectpaybtn').on('click', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var due = $(this).data('due');

    $('#modal_inv_id').val(id);
    $('#modal_patient_name').text(name);
    $('#modal_due_amt').text('Rs. ' + parseFloat(due).toFixed(2));
    $('#modal_amount').val(parseFloat(due).toFixed(2));
    $('#collectPaymentModal').modal('show');
  });
});
</script>
@endpush
