@extends('layouts.app')

@section('title', 'Patient Booking & Investigation - RBJLIS')

@push('css')
<style>
.booking-header {
  background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
  color: #FFFFFF;
  border-radius: 14px 14px 0 0;
  padding: 1.25rem 1.5rem;
}
.testrequired-box {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 12px;
  padding: 1.25rem;
  margin-top: 1.25rem;
}
.test-row-item {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 10px;
  padding: 0.75rem 1rem;
  margin-bottom: 0.5rem;
  transition: all 0.2s ease;
}
.test-row-item:hover {
  border-color: #CBD5E1;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
.pay-bar-container {
  background: linear-gradient(135deg, #F8FAFC 0%, #F1F5F9 100%);
  border: 1px solid #E2E8F0;
  border-radius: 14px;
  padding: 1.25rem;
  margin-top: 1.25rem;
}
.ui-autocomplete {
  border-radius: 10px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  border: 1px solid #E2E8F0;
  padding: 8px 0;
}
.ui-menu-item-wrapper {
  padding: 8px 16px !important;
  font-size: 13px !important;
}
.ui-menu-item-wrapper.ui-state-active {
  background: #4F46E5 !important;
  color: #FFFFFF !important;
  border: none !important;
}
</style>
@endpush

@section('content')
<form id="addnewinvestigation" enctype="multipart/form-data">
  @csrf
  <div class="row">
    <div class="col-lg-11 mx-auto">
      <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
        
        <!-- Header -->
        <div class="booking-header d-flex flex-wrap justify-content-between align-items-center">
          <div>
            <h4 class="font-weight-bold mb-0">
              <i class="fas fa-notes-medical text-primary mr-2"></i> New Investigation Order
              <span class="patientdetails badge badge-warning ml-2 font-weight-normal"></span>
            </h4>
            <small class="text-slate-300 opacity-75">Quickly register patient and assign multi-panel diagnostic tests</small>
          </div>
          <div class="mt-2 mt-md-0">
            <input type="hidden" id="doctor_id" name="doctor_id" value="0" />
            <input type="hidden" id="patient_id" name="patient_id" value="0" />
            <div class="input-group" style="width: 360px;">
              <div class="input-group-prepend">
                <span class="input-group-text bg-white border-right-0 text-muted" style="border-radius: 8px 0 0 8px;">
                  <i class="fas fa-search"></i>
                </span>
              </div>
              <input type="text" class="form-control uniqueid_pid border-left-0" style="border-radius: 0 8px 8px 0;" placeholder="Search Patient (Name / Phone / UID)...">
            </div>
          </div>
        </div>

        <div class="card-body p-4">
          <!-- Step 1: Patient Information -->
          <div class="mb-2 d-flex align-items-center">
            <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2 font-weight-bold" style="width: 24px; height: 24px; font-size: 12px;">1</span>
            <h6 class="font-weight-bold text-dark mb-0 text-uppercase" style="letter-spacing: 0.05em; font-size: 12px;">Patient Demographics</h6>
          </div>

          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Title</label>
                <select class="form-control" name="title" id="patient_title">
                  <option value="Mr">Mr.</option>
                  <option value="Mrs">Mrs.</option>
                  <option value="Miss">Miss</option>
                  <option value="Master">Master</option>
                  <option value="Baby">Baby</option>
                  <option value="Dr">Dr.</option>
                </select>
              </div>
            </div>

            <div class="col-md-5">
              <div class="form-group">
                <label>Patient Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="patient_name" class="form-control" placeholder="e.g. Anand Sharma" required>
              </div>
            </div>

            <div class="col-md-2">
              <div class="form-group">
                <label>Age</label>
                <input type="text" name="age" id="patient_age" placeholder="e.g. 42" class="form-control">
              </div>
            </div>

            <div class="col-md-3">
              <div class="form-group">
                <label>Gender</label>
                <select class="form-control" name="gender" id="patient_gender">
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" id="patient_phone" class="form-control" placeholder="10-digit mobile number">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="patient_email" class="form-control" placeholder="patient@gmail.com">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Referring Doctor</label>
                <select name="doctor_id" id="doctor_select" class="form-control">
                  <option value="0">Self / Direct Walk-in</option>
                  @foreach($doctors as $d)
                    <option value="{{ $d->id }}" data-discount="{{ $d->discount }}">Dr. {{ $d->doctor_name }} ({{ $d->clinic_name }})</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-5">
              <div class="form-group">
                <label>Residential Address</label>
                <input type="text" name="address" id="patient_address" class="form-control" placeholder="Street, Area, City">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Aadhar / National ID</label>
                <input type="text" name="aadhar" id="patient_aadhar" class="form-control" placeholder="ID Number">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>B2B Outsource Partner (Optional)</label>
                <select class="form-control" name="ltol_id" id="ltolid">
                  <option value="0">Direct Patient / No Partner</option>
                  @foreach($associatedlabs as $lab)
                    <option value="{{ $lab->id }}">{{ $lab->lab_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <!-- Step 2: Test Selection -->
          <div class="testrequired-box">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="d-flex align-items-center">
                <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mr-2 font-weight-bold" style="width: 24px; height: 24px; font-size: 12px;">2</span>
                <h6 class="font-weight-bold text-dark mb-0 text-uppercase" style="letter-spacing: 0.05em; font-size: 12px;">Assigned Diagnostic Tests</h6>
              </div>
              <span class="text-muted small">Type test or package name below to search</span>
            </div>

            <div class="requestedtests mb-3"></div>

            <div class="row">
              <div class="col-md-8">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-white"><i class="fas fa-search-plus text-primary"></i></span>
                  </div>
                  <input type="text" id="selecttestname" class="form-control" placeholder="Start typing name of test or profile (e.g. CBC, Lipid, Thyroid)...">
                </div>
              </div>
              <div class="col-md-4">
                <div class="custom-file">
                  <input type="file" name="filedata" class="custom-file-input" id="prescriptionFile">
                  <label class="custom-file-label" for="prescriptionFile">Upload Rx / Doctor Slip</label>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 3: Discounts & Financial Summary -->
          <div class="pay-bar-container">
            <div class="row align-items-center">
              <div class="col-md-3">
                <label class="small font-weight-bold text-muted text-uppercase mb-1">Discount Mode</label>
                <div class="d-flex" style="gap: 12px;">
                  <div class="custom-control custom-radio">
                    <input class="custom-control-input discounttype" type="radio" name="discountype" id="discFlat" value="flat" checked>
                    <label class="custom-control-label small" for="discFlat">Flat (Rs.)</label>
                  </div>
                  <div class="custom-control custom-radio">
                    <input class="custom-control-input discounttype" type="radio" name="discountype" id="discPerc" value="percentage">
                    <label class="custom-control-label small" for="discPerc">Percent (%)</label>
                  </div>
                </div>
              </div>

              <div class="col-md-2">
                <label class="small font-weight-bold text-muted text-uppercase mb-1">Discount Value</label>
                <input type="number" step="0.01" name="discount" class="form-control discountamt" value="0" placeholder="0.00">
              </div>

              <div class="col-md-3">
                <label class="small font-weight-bold text-muted text-uppercase mb-1">Payment Method</label>
                <select class="form-control" name="pay_method">
                  <option value="Cash">Cash</option>
                  <option value="UPI">UPI / GPay / QR</option>
                  <option value="Card">Debit / Credit Card</option>
                  <option value="NetBanking">Net Banking</option>
                </select>
              </div>

              <div class="col-md-2">
                <label class="small font-weight-bold text-muted text-uppercase mb-1">Amount Paid (Rs.)</label>
                <input type="number" step="0.01" name="paid_amount" class="form-control amountpaid font-weight-bold text-success" value="0">
              </div>

              <div class="col-md-2">
                <label class="small font-weight-bold text-muted text-uppercase mb-1">Transaction Ref #</label>
                <input type="text" name="transid" class="form-control" placeholder="Ref/UTR #">
              </div>
            </div>

            <div class="row mt-3 pt-3 border-top align-items-center">
              <div class="col-md-6">
                <input type="text" name="notes" class="form-control" placeholder="Clinical notes, fasting status, or Phlebotomy remarks...">
              </div>
              <div class="col-md-3 text-right">
                <span class="text-muted small d-block">Gross Order Total</span>
                <h4 class="font-weight-bold text-primary mb-0 bill-total-price">Rs. 0.00</h4>
              </div>
              <div class="col-md-3 text-right">
                <span class="text-muted small d-block">Balance Due</span>
                <h4 class="font-weight-bold text-danger mb-0 bill-due-price">Rs. 0.00</h4>
              </div>
            </div>
          </div>

          <!-- Actions Bar -->
          <div class="d-flex flex-wrap justify-content-center align-items-center mt-4" style="gap: 14px;">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow font-weight-bold saveinvestigation">
              <i class="fas fa-check-circle mr-1"></i> Book Order & Print Receipt (F2)
            </button>
            <button type="button" class="btn btn-outline-secondary btn-lg px-4 npatient">
              <i class="fas fa-redo mr-1"></i> New Order (F1)
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  var totalprice = 0;
  var discountamt = 0;
  var paidamt = 0;
  var dueprice = 0;

  // Shortcuts
  $(document).on('keydown', function(e) {
    if (e.which === 112) { e.preventDefault(); resetForm(); }
    else if (e.which === 113) { e.preventDefault(); $('#addnewinvestigation').submit(); }
  });

  $('.npatient').on('click', function() { resetForm(); });

  function resetForm() {
    $('#addnewinvestigation').trigger('reset');
    $('#patient_id').val('0');
    $('.patientdetails').html('');
    $('.requestedtests').html('');
    totalprice = 0;
    discountamt = 0;
    paidamt = 0;
    dueprice = 0;
    recalculateTotals();
    toastr.info('Ready for new patient order');
  }

  // Patient Autocomplete
  $('.uniqueid_pid').autocomplete({
    source: function(request, response) {
      $.ajax({
        url: "{{ route('frontoffice.getpatient') }}",
        dataType: "json",
        data: { term: request.term },
        success: function(data) { response(data); }
      });
    },
    minLength: 2,
    select: function(event, ui) {
      var p = ui.item.patient;
      $('#patient_id').val(p.id);
      $('#patient_name').val(p.name);
      $('#patient_title').val(p.title || 'Mr');
      $('#patient_age').val(p.age);
      $('#patient_gender').val(p.gender || 'Male');
      $('#patient_phone').val(p.phone);
      $('#patient_email').val(p.email);
      $('#patient_address').val(p.address);
      $('#patient_aadhar').val(p.aadhar);
      $('.patientdetails').html('ID: ' + p.unique_id);
      toastr.success('Patient record loaded: ' + p.name);
      return false;
    }
  });

  // Test Search Autocomplete
  $('#selecttestname').autocomplete({
    source: function(request, response) {
      var clabId = $('#ltolid').val();
      $.ajax({
        url: "{{ route('frontoffice.gettest') }}",
        dataType: "json",
        data: { term: request.term, clabid: clabId },
        success: function(data) { response(data); }
      });
    },
    minLength: 1,
    select: function(event, ui) {
      addTestRow(ui.item.id, ui.item.name, ui.item.price, ui.item.type);
      $('#selecttestname').val('');
      return false;
    }
  });

  function addTestRow(id, name, price, type) {
    var exists = $('.requestedtests').find('input[name="testid[]"][value="' + id + '"]').length;
    if (exists > 0) {
      toastr.warning('Test is already added to order');
      return;
    }

    var html = `
      <div class="test-row-item test-row d-flex justify-content-between align-items-center" data-price="${price}">
        <div class="d-flex align-items-center">
          <i class="fas fa-times-circle text-danger mr-3 cursor-pointer delinvestigation" title="Remove Test" style="cursor: pointer; font-size: 16px;"></i>
          <div>
            <strong>${name}</strong>
            ${type === 'profile' ? '<span class="badge badge-info ml-2">Health Profile / Package</span>' : ''}
          </div>
          <input type="hidden" name="testid[]" value="${id}">
          <input type="hidden" name="testtype[]" value="${type}">
          <input type="hidden" name="testprice[]" value="${price}">
        </div>
        <div class="d-flex align-items-center" style="gap: 16px;">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="emer_${id}" name="requiredemergency[]" value="${id}">
            <label class="custom-control-label small text-danger font-weight-bold" for="emer_${id}">STAT (Emergency)</label>
          </div>
          <span class="font-weight-bold text-dark" style="font-size: 15px;">Rs. ${parseFloat(price).toFixed(2)}</span>
        </div>
      </div>
    `;

    $('.requestedtests').append(html);
    recalculateTotals();
  }

  $('body').on('click', '.delinvestigation', function() {
    $(this).closest('.test-row').remove();
    recalculateTotals();
  });

  function recalculateTotals() {
    totalprice = 0;
    $('.test-row').each(function() {
      totalprice += parseFloat($(this).data('price')) || 0;
    });

    var discInput = parseFloat($('.discountamt').val()) || 0;
    var discType = $('input[name="discountype"]:checked').val();

    if (discType === 'percentage') {
      discountamt = (totalprice * discInput) / 100;
    } else {
      discountamt = discInput;
    }

    var payable = Math.max(0, totalprice - discountamt);
    paidamt = parseFloat($('.amountpaid').val()) || 0;
    dueprice = Math.max(0, payable - paidamt);

    $('.bill-total-price').text('Rs. ' + totalprice.toFixed(2));
    $('.bill-due-price').text('Rs. ' + dueprice.toFixed(2));
  }

  $('.discountamt, .amountpaid, input[name="discountype"]').on('input change', function() {
    recalculateTotals();
  });

  $('#doctor_select').on('change', function() {
    var disc = $(this).find(':selected').data('discount');
    if (disc && disc > 0) {
      $('#discPerc').prop('checked', true);
      $('.discountamt').val(disc);
      recalculateTotals();
    }
  });

  $('#addnewinvestigation').on('submit', function(e) {
    e.preventDefault();
    if ($('.test-row').length === 0) {
      toastr.error('Please assign at least one diagnostic test');
      return;
    }

    $('.loader-full').css('display', 'flex');

    $.ajax({
      url: "{{ route('frontoffice.createinvestigation') }}",
      type: "POST",
      data: new FormData(this),
      processData: false,
      contentType: false,
      success: function(resp) {
        $('.loader-full').hide();
        if (resp.err === 0) {
          toastr.success(resp.message);
          window.open("{{ url('/print/bill') }}?id=" + resp.lid, "_blank");
          resetForm();
        } else {
          toastr.error(resp.message || 'Error booking investigation');
        }
      },
      error: function(xhr) {
        $('.loader-full').hide();
        toastr.error(xhr.responseJSON?.message || 'Failed to submit order');
      }
    });
  });
});
</script>
@endpush
