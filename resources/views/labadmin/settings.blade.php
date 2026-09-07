@extends('layouts.app')

@section('title', 'Laboratory Settings & Integrations - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12 col-lg-10">
    <div class="card card-primary card-outline card-outline-tabs shadow-sm" style="border-radius: 12px; overflow: hidden;">
      <div class="card-header p-0 border-bottom-0 bg-light">
        <ul class="nav nav-tabs font-weight-bold" id="settingsTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active py-3 px-4" id="tab-branding-tab" data-toggle="pill" href="#tab-branding" role="tab">
              <i class="fas fa-palette mr-1 text-primary"></i> Branding & Notes
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-3 px-4" id="tab-upi-tab" data-toggle="pill" href="#tab-upi" role="tab">
              <i class="fas fa-qrcode mr-1 text-success"></i> UPI QR Payments
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-3 px-4" id="tab-stripe-tab" data-toggle="pill" href="#tab-stripe" role="tab">
              <i class="fab fa-stripe mr-1 text-info"></i> Stripe Gateway
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-3 px-4" id="tab-sms-tab" data-toggle="pill" href="#tab-sms" role="tab">
              <i class="fas fa-sms mr-1 text-warning"></i> SMS Alerts
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link py-3 px-4" id="tab-email-tab" data-toggle="pill" href="#tab-email" role="tab">
              <i class="fas fa-envelope mr-1 text-danger"></i> Email & SMTP
            </a>
          </li>
        </ul>
      </div>

      <div class="card-body p-4">
        <form action="{{ route('labadmin.settings.post') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="tab-content" id="settingsTabContent">
            <!-- TAB 1: Branding -->
            <div class="tab-pane fade show active" id="tab-branding" role="tabpanel">
              <h5 class="font-weight-bold text-dark mb-3"><i class="fas fa-hospital-alt text-primary mr-1"></i> Laboratory Profile & Report Letterhead</h5>
              
              <div class="form-group mb-3">
                <label>Laboratory Name</label>
                <p class="form-control-static font-weight-bold text-dark">{{ $labinfo->name }} (Code: {{ $labinfo->shortname }})</p>
              </div>

              <div class="form-group mb-3">
                <label for="address" class="font-weight-bold">Address Printed on Reports / Invoices</label>
                <textarea name="address" id="address" class="form-control" rows="3" placeholder="Full laboratory street address, city, state, pin code">{{ old('address', $labinfo->address) }}</textarea>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="logoimg" class="font-weight-bold">Lab Logo</label>
                    @if($labinfo->logo)
                      <div class="mb-2 p-2 bg-light rounded text-center border">
                        <img src="{{ asset('img/' . $labinfo->logo) }}" alt="Logo" style="max-height: 50px;">
                      </div>
                    @endif
                    <input type="file" name="logoimg" id="logoimg" class="form-control-file">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="letterhead" class="font-weight-bold">Custom Header Letterhead</label>
                    @if($labinfo->letter_head)
                      <div class="mb-2 p-2 bg-light rounded text-center border">
                        <img src="{{ asset('img/' . $labinfo->letter_head) }}" alt="Letterhead" style="max-height: 50px;">
                      </div>
                    @endif
                    <input type="file" name="letterhead" id="letterhead" class="form-control-file">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="sealimg" class="font-weight-bold">Lab Official Seal / Stamp</label>
                    @if($labinfo->lab_seal)
                      <div class="mb-2 p-2 bg-light rounded text-center border">
                        <img src="{{ asset('img/' . $labinfo->lab_seal) }}" alt="Seal" style="max-height: 50px;">
                      </div>
                    @endif
                    <input type="file" name="sealimg" id="sealimg" class="form-control-file">
                  </div>
                </div>
              </div>

              <div class="form-group mb-0">
                <label for="defult_notes" class="font-weight-bold">Default Report Footer Notes / Disclaimer</label>
                <textarea name="defult_notes" id="defult_notes" class="form-control" rows="3" placeholder="Standard clinical disclaimer printed at the bottom of all test reports">{{ old('defult_notes', $labinfo->defult_notes) }}</textarea>
              </div>
            </div>

            <!-- TAB 2: UPI Payments -->
            <div class="tab-pane fade" id="tab-upi" role="tabpanel">
              <h5 class="font-weight-bold text-dark mb-2"><i class="fas fa-qrcode text-success mr-1"></i> Dynamic UPI QR Code Configuration</h5>
              <p class="text-muted small mb-4">Generates NPCI-compliant instant payment QR codes on bills and patient checkout portal (Supports Google Pay, PhonePe, Paytm, BHIM, Cred).</p>

              <div class="custom-control custom-switch mb-3">
                <input type="checkbox" class="custom-control-input" id="enable_upi" name="enable_upi" {{ ($labinfo->enable_upi ?? true) ? 'checked' : '' }}>
                <label class="custom-control-label font-weight-bold text-dark" for="enable_upi">Enable UPI QR Code Generation on Invoices & Portals</label>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="upi_id" class="font-weight-bold">Lab UPI VPA ID <span class="text-danger">*</span></label>
                    <input type="text" name="upi_id" id="upi_id" class="form-control" placeholder="e.g. rbjdiagnostics@okaxis or 9876543210@paytm" value="{{ old('upi_id', $labinfo->upi_id ?? 'rbjlab@upi') }}">
                    <small class="text-muted">Payments will be routed directly to this bank-linked Virtual Payment Address.</small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="upi_name" class="font-weight-bold">Payee Business / Lab Name</label>
                    <input type="text" name="upi_name" id="upi_name" class="form-control" placeholder="e.g. RBJ Diagnostics & Research" value="{{ old('upi_name', $labinfo->upi_name ?? $labinfo->name) }}">
                    <small class="text-muted">Display name visible to patient when scanning QR code.</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- TAB 3: Stripe Gateway -->
            <div class="tab-pane fade" id="tab-stripe" role="tabpanel">
              <h5 class="font-weight-bold text-dark mb-2"><i class="fab fa-stripe text-info mr-1"></i> Stripe Credit / Debit Card Gateway</h5>
              <p class="text-muted small mb-4">Accept online patient payments via Visa, MasterCard, RuPay, Amex, Apple Pay, and Google Pay.</p>

              <div class="custom-control custom-switch mb-3">
                <input type="checkbox" class="custom-control-input" id="enable_stripe" name="enable_stripe" {{ ($labinfo->enable_stripe ?? false) ? 'checked' : '' }}>
                <label class="custom-control-label font-weight-bold text-dark" for="enable_stripe">Enable Stripe Card Payment Gateway</label>
              </div>

              <div class="form-group">
                <label for="stripe_key" class="font-weight-bold">Stripe Publishable Key</label>
                <input type="text" name="stripe_key" id="stripe_key" class="form-control" placeholder="pk_test_..." value="{{ old('stripe_key', $labinfo->stripe_key) }}">
              </div>

              <div class="form-group">
                <label for="stripe_secret" class="font-weight-bold">Stripe Secret Key</label>
                <input type="password" name="stripe_secret" id="stripe_secret" class="form-control" placeholder="sk_test_..." value="{{ old('stripe_secret', $labinfo->stripe_secret) }}">
              </div>

              <div class="form-group mb-0">
                <label for="stripe_webhook_secret" class="font-weight-bold">Stripe Webhook Signing Secret (Optional)</label>
                <input type="password" name="stripe_webhook_secret" id="stripe_webhook_secret" class="form-control" placeholder="whsec_..." value="{{ old('stripe_webhook_secret', $labinfo->stripe_webhook_secret) }}">
              </div>
            </div>

            <!-- TAB 4: SMS Notifications -->
            <div class="tab-pane fade" id="tab-sms" role="tabpanel">
              <h5 class="font-weight-bold text-dark mb-2"><i class="fas fa-sms text-warning mr-1"></i> SMS & WhatsApp Multi-Gateway Alerts</h5>
              <p class="text-muted small mb-4">Automatically dispatch booking receipts, sample collection status, and report ready alerts to patient mobile numbers.</p>

              <div class="custom-control custom-switch mb-3">
                <input type="checkbox" class="custom-control-input" id="enable_sms" name="enable_sms" {{ ($labinfo->enable_sms ?? false) ? 'checked' : '' }}>
                <label class="custom-control-label font-weight-bold text-dark" for="enable_sms">Enable Automatic SMS Notifications</label>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="sms_provider" class="font-weight-bold">SMS Gateway Provider</label>
                    <select name="sms_provider" id="sms_provider" class="form-control">
                      <option value="bulksmsgateway" {{ ($labinfo->sms_provider ?? '') == 'bulksmsgateway' ? 'selected' : '' }}>BulkSMSGateway</option>
                      <option value="fast2sms" {{ ($labinfo->sms_provider ?? '') == 'fast2sms' ? 'selected' : '' }}>Fast2SMS (India)</option>
                      <option value="msg91" {{ ($labinfo->sms_provider ?? '') == 'msg91' ? 'selected' : '' }}>Msg91</option>
                      <option value="twilio" {{ ($labinfo->sms_provider ?? '') == 'twilio' ? 'selected' : '' }}>Twilio (Global)</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="sms_api_key" class="font-weight-bold">Gateway API Key / Auth Token</label>
                    <input type="password" name="sms_api_key" id="sms_api_key" class="form-control" placeholder="API Key / Auth Token" value="{{ old('sms_api_key', $labinfo->sms_api_key) }}">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="sms_sender_id" class="font-weight-bold">Sender ID / Header</label>
                    <input type="text" name="sms_sender_id" id="sms_sender_id" class="form-control" placeholder="e.g. RBJLIS / TXTIND" value="{{ old('sms_sender_id', $labinfo->sms_sender_id ?? 'RBJLIS') }}">
                  </div>
                </div>
              </div>
            </div>

            <!-- TAB 5: Email & SMTP -->
            <div class="tab-pane fade" id="tab-email" role="tabpanel">
              <h5 class="font-weight-bold text-dark mb-2"><i class="fas fa-envelope text-danger mr-1"></i> Email & SMTP Server Settings</h5>
              <p class="text-muted small mb-4">Send official PDF pathology reports and booking receipts directly to patient email addresses.</p>

              <div class="custom-control custom-switch mb-3">
                <input type="checkbox" class="custom-control-input" id="enable_email" name="enable_email" {{ ($labinfo->enable_email ?? true) ? 'checked' : '' }}>
                <label class="custom-control-label font-weight-bold text-dark" for="enable_email">Enable Automatic Email Notifications with PDF Reports</label>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="smtp_host" class="font-weight-bold">SMTP Host</label>
                    <input type="text" name="smtp_host" id="smtp_host" class="form-control" placeholder="e.g. smtp.gmail.com or smtp.mailgun.org" value="{{ old('smtp_host', $labinfo->smtp_host) }}">
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label for="smtp_port" class="font-weight-bold">SMTP Port</label>
                    <input type="text" name="smtp_port" id="smtp_port" class="form-control" placeholder="587 / 465" value="{{ old('smtp_port', $labinfo->smtp_port ?? '587') }}">
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label for="smtp_encryption" class="font-weight-bold">Encryption</label>
                    <select name="smtp_encryption" id="smtp_encryption" class="form-control">
                      <option value="tls" {{ ($labinfo->smtp_encryption ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                      <option value="ssl" {{ ($labinfo->smtp_encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                      <option value="none" {{ ($labinfo->smtp_encryption ?? '') == 'none' ? 'selected' : '' }}>None</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="smtp_user" class="font-weight-bold">SMTP Username / Email</label>
                    <input type="text" name="smtp_user" id="smtp_user" class="form-control" placeholder="user@domain.com" value="{{ old('smtp_user', $labinfo->smtp_user) }}">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="smtp_pass" class="font-weight-bold">SMTP Password / App Key</label>
                    <input type="password" name="smtp_pass" id="smtp_pass" class="form-control" placeholder="Password" value="{{ old('smtp_pass', $labinfo->smtp_pass) }}">
                  </div>
                </div>
              </div>

              <!-- Test Email Panel -->
              <div class="p-3 mt-3 rounded" style="background: #F0FDF4; border: 1px solid #BBF7D0;">
                <h6 class="font-weight-bold text-success mb-2">
                  <i class="fas fa-paper-plane mr-1"></i> Test SMTP Email Connection Now
                </h6>
                <p class="text-muted small mb-2">Enter any email address to test your SMTP configuration and receive an instant test email.</p>
                <div class="input-group" style="max-width: 500px;">
                  <input type="email" id="test_email_input" class="form-control" placeholder="Enter recipient email (e.g. yourname@gmail.com)">
                  <div class="input-group-append">
                    <button type="button" id="btn_send_test_email" class="btn btn-success font-weight-bold">
                      <i class="fas fa-paper-plane mr-1"></i> Send Test Email
                    </button>
                  </div>
                </div>
                <div id="test_email_feedback" class="mt-2 small" style="display: none;"></div>
              </div>
            </div>
          </div>

          <hr class="my-4">

          <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('labadmin.index') }}" class="btn btn-outline-secondary font-weight-bold">
              <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
            <button type="submit" class="btn btn-success font-weight-bold px-4 py-2 shadow-sm" style="border-radius: 8px;">
              <i class="fas fa-save mr-1"></i> Save Laboratory Settings & Integrations
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('#btn_send_test_email').on('click', function() {
    var email = $('#test_email_input').val();
    if (!email) {
      alert('Please enter a recipient email address to test.');
      return;
    }

    var $btn = $(this);
    var $feedback = $('#test_email_feedback');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Sending...');
    $feedback.hide().removeClass('text-success text-danger');

    $.ajax({
      url: "{{ route('labadmin.testemail') }}",
      type: "POST",
      data: {
        test_email: email,
        smtp_host: $('#smtp_host').val(),
        smtp_port: $('#smtp_port').val(),
        smtp_encryption: $('#smtp_encryption').val(),
        smtp_user: $('#smtp_user').val(),
        smtp_pass: $('#smtp_pass').val(),
        _token: "{{ csrf_token() }}"
      },
      success: function(resp) {
        $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Send Test Email');
        $feedback.show().addClass('text-success font-weight-bold').html('<i class="fas fa-check-circle mr-1"></i> ' + resp.message);
        if (typeof toastr !== 'undefined') {
          toastr.success(resp.message);
        }
      },
      error: function(xhr) {
        $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Send Test Email');
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error sending test email. Please check your SMTP settings.';
        $feedback.show().addClass('text-danger font-weight-bold').html('<i class="fas fa-times-circle mr-1"></i> ' + msg);
        if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        }
      }
    });
  });
});
</script>
@endpush
