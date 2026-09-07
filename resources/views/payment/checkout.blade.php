@extends('layouts.auth')

@section('title', 'Secure Online Payment - RBJLIS LIMS')

@section('content')
<div class="card shadow-lg border-0" style="border-radius: 16px; max-width: 680px; margin: 0 auto;">
  <div class="card-header bg-dark text-white p-4 text-center" style="border-top-left-radius: 16px; border-top-right-radius: 16px; background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%) !important;">
    <div class="d-inline-flex align-items-center justify-content-center bg-primary rounded-circle mb-2" style="width: 48px; height: 48px;">
      <i class="fas fa-lock text-white fa-lg"></i>
    </div>
    <h4 class="font-weight-bold mb-1">Secure Diagnostic Payment</h4>
    <p class="text-white-50 small mb-0">{{ $lab->name ?? 'RBJ Diagnostics & Research Center' }}</p>
  </div>

  <div class="card-body p-4">
    @if(session('error'))
      <div class="alert alert-danger border-0 mb-3" style="border-radius: 8px;">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
      </div>
    @endif

    <!-- Patient & Bill Details -->
    <div class="p-3 mb-4 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
      <div class="row">
        <div class="col-sm-6">
          <p class="mb-1 text-muted small text-uppercase font-weight-bold">Patient Name</p>
          <h5 class="font-weight-bold text-dark mb-2">{{ $investigation->patient->name ?? 'N/A' }}</h5>
          <span class="badge badge-light border">{{ $investigation->patient->unique_id ?? 'UID-N/A' }}</span>
        </div>
        <div class="col-sm-6 text-sm-right mt-3 mt-sm-0">
          <p class="mb-1 text-muted small text-uppercase font-weight-bold">Amount Payable</p>
          <h3 class="font-weight-bold text-primary mb-0">Rs. {{ number_format($amountToPay, 2) }}</h3>
          <small class="text-muted">Order Ref: INV-{{ $investigation->id }}</small>
        </div>
      </div>
    </div>

    <!-- Payment Tabs -->
    <ul class="nav nav-pills nav-fill mb-3" id="paymentTab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active font-weight-bold py-2" id="upi-tab" data-toggle="pill" href="#upi-pane" role="tab">
          <i class="fas fa-qrcode mr-2"></i> UPI QR & Apps
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link font-weight-bold py-2" id="stripe-tab" data-toggle="pill" href="#stripe-pane" role="tab">
          <i class="fas fa-credit-card mr-2"></i> Card / Stripe
        </a>
      </li>
    </ul>

    <div class="tab-content" id="paymentTabContent">
      <!-- UPI Pane -->
      <div class="tab-pane fade show active" id="upi-pane" role="tabpanel">
        <div class="text-center p-3">
          <p class="text-muted small mb-3">Scan this QR with any UPI App (Google Pay, PhonePe, Paytm, BHIM, Cred)</p>
          <div class="d-inline-block p-2 bg-white rounded shadow-sm border mb-3">
            <img src="{{ $upiQrUrl }}" alt="UPI QR Code" style="width: 200px; height: 200px;" class="img-fluid">
          </div>
          
          <div class="mb-3">
            <span class="badge badge-secondary p-2 font-weight-normal" style="font-size: 13px;">
              <i class="fas fa-at text-info mr-1"></i> UPI VPA: <strong>{{ $vpa }}</strong>
            </span>
          </div>

          <div class="d-block d-md-none mb-4">
            <a href="{{ $upiIntentUrl }}" class="btn btn-success btn-block font-weight-bold py-2">
              <i class="fas fa-external-link-alt mr-1"></i> Open in UPI App (Pay Rs. {{ number_format($amountToPay, 2) }})
            </a>
          </div>

          <!-- UTR Submission -->
          <div class="border-top pt-3 text-left">
            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-check-circle text-success mr-1"></i> Paid via UPI? Enter Reference / UTR Number:</h6>
            <form action="{{ route('payment.upi.submit', ['id' => $investigation->id]) }}" method="POST">
              @csrf
              <input type="hidden" name="payment_amount" value="{{ $amountToPay }}">
              <div class="input-group mb-2">
                <input type="text" name="trans_number" class="form-control" placeholder="Enter 12-digit UPI UTR / Ref No (e.g. 423987123456)" required>
                <div class="input-group-append">
                  <button type="submit" class="btn btn-primary font-weight-bold">
                    Confirm Payment
                  </button>
                </div>
              </div>
              <small class="text-muted">You will find the 12-digit UPI Reference Number in your PhonePe / GPay / Paytm payment receipt.</small>
            </form>
          </div>
        </div>
      </div>

      <!-- Stripe Pane -->
      <div class="tab-pane fade" id="stripe-pane" role="tabpanel">
        <div class="text-center p-4">
          <i class="fab fa-stripe fa-4x text-primary mb-3"></i>
          <h5 class="font-weight-bold text-dark">Pay with Credit / Debit Card</h5>
          <p class="text-muted small mb-4">Supports Visa, MasterCard, American Express, RuPay, and International Cards.</p>

          <form action="{{ route('payment.stripe.checkout', ['id' => $investigation->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="amount" value="{{ $amountToPay }}">
            <button type="submit" class="btn btn-primary btn-lg btn-block font-weight-bold py-3 shadow-sm" style="border-radius: 10px;">
              <i class="fas fa-shield-alt mr-2"></i> Pay Rs. {{ number_format($amountToPay, 2) }} with Card
            </button>
          </form>
          <div class="mt-3 text-muted small">
            <i class="fas fa-lock text-success mr-1"></i> 256-Bit SSL Encrypted & Secure Checkout
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-footer bg-light p-3 text-center border-top" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
    <a href="{{ route('frontoffice.bills') }}" class="text-muted small">
      <i class="fas fa-arrow-left mr-1"></i> Back to Bills & Invoices
    </a>
  </div>
</div>
@endsection
