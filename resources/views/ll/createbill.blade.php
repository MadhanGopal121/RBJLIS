@extends('layouts.app')

@section('title', 'Place B2B Investigation Order - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-9 mx-auto">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Place Outsource Test Order</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('ll.createbill.post') }}" method="POST">
          @csrf
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Title</label>
                <select name="title" class="form-control">
                  <option value="Mr">Mr.</option>
                  <option value="Mrs">Mrs.</option>
                  <option value="Miss">Miss</option>
                  <option value="Master">Master</option>
                  <option value="Dr">Dr.</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Patient Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="Patient Full Name">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Age</label>
                <input type="text" name="age" class="form-control" placeholder="Age">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Gender</label>
                <select name="gender" class="form-control">
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control" placeholder="Mobile Number">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Optional Email">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Select Tests to Outsource</label>
            <div class="row p-3 border rounded" style="max-height: 250px; overflow-y: auto;">
              @foreach($tests as $t)
                @php
                  $rate = $specialPrices->has($t->id) ? $specialPrices->get($t->id)->sp_price : ($t->lab_price ?: $t->price);
                @endphp
                <div class="col-md-6 mb-2">
                  <div class="icheck-primary">
                    <input type="checkbox" id="test_{{ $t->id }}" name="testid[]" value="{{ $t->id }}" data-price="{{ $rate }}" class="ll-test-check">
                    <label for="test_{{ $t->id }}"><strong>{{ $t->name }}</strong> - <span class="text-success font-weight-bold">Rs. {{ number_format($rate, 2) }}</span></label>
                    <input type="hidden" name="testprice[]" id="price_input_{{ $t->id }}" value="{{ $rate }}" disabled>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="form-group">
            <label>Clinical Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Any special instructions or specimen details"></textarea>
          </div>

          <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mt-3">
            <h4 class="mb-0">Total Order Amount: <span class="text-primary font-weight-bold" id="ll_total_display">Rs. 0.00</span></h4>
            <div>
              <a href="{{ route('ll.index') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Submit Order</button>
            </div>
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
  $('.ll-test-check').on('change', function() {
    var total = 0;
    $('.ll-test-check:checked').each(function() {
      var id = $(this).val();
      $('#price_input_' + id).prop('disabled', false);
      total += parseFloat($(this).data('price')) || 0;
    });

    $('.ll-test-check:not(:checked)').each(function() {
      var id = $(this).val();
      $('#price_input_' + id).prop('disabled', true);
    });

    $('#ll_total_display').text('Rs. ' + total.toFixed(2));
  });
});
</script>
@endpush
