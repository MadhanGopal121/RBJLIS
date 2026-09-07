@extends('layouts.app')

@section('title', 'Special B2B Rates - ' . $labdet->lab_name)

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Special Tariff Rates for: {{ $labdet->lab_name }}</h3>
        <div class="card-tools">
          <a href="{{ route('labadmin.labs') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back to Labs</a>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-4 p-3 bg-light rounded">
          <div class="col-md-5">
            <label>Select Diagnostic Test</label>
            <select id="selecttest" class="form-control">
              <option value="">-- Choose Test --</option>
              @foreach($tests as $t)
                <option value="{{ $t->id }}" data-price="{{ $t->lab_price ?: $t->price }}">{{ $t->name }} (Std: Rs. {{ $t->price }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label>Special B2B Price (Rs.)</label>
            <input type="number" step="0.01" id="specialprice" class="form-control" placeholder="Rate in Rs.">
          </div>
          <div class="col-md-3">
            <label>&nbsp;</label>
            <button type="button" id="savespecialrate" class="btn btn-success btn-block"><i class="fas fa-save"></i> Save Special Rate</button>
          </div>
        </div>

        <h5>Configured Special Rates</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="specialratestble">
            <thead class="thead-light">
              <tr>
                <th>Test Name</th>
                <th>Special B2B Price</th>
                <th>Configured Date</th>
              </tr>
            </thead>
            <tbody>
              @forelse($sps as $sp)
                <tr>
                  <td><strong>{{ $sp->diagnosticstest->name ?? ($sp->mastertest->name ?? 'Test') }}</strong></td>
                  <td><span class="badge badge-success" style="font-size: 14px;">Rs. {{ number_format($sp->sp_price, 2) }}</span></td>
                  <td>{{ $sp->created_at ? $sp->created_at->format('d M Y') : '-' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center text-muted py-3">No special tariff rates assigned yet. Standard rates apply.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('#selecttest').on('change', function() {
    var price = $(this).find(':selected').data('price');
    if (price) {
      $('#specialprice').val(price);
    }
  });

  $('#savespecialrate').on('click', function() {
    var testid = $('#selecttest').val();
    var price = $('#specialprice').val();
    var ltolid = "{{ $ltolid }}";

    if (!testid || !price) {
      toastr.error('Please select test and enter special price');
      return;
    }

    $.ajax({
      url: "{{ route('labadmin.addspecialrates') }}",
      type: "POST",
      data: {
        testid: testid,
        price: price,
        ltolid: ltolid,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function() {
        toastr.success('Special rate saved successfully');
        setTimeout(function() {
          location.reload();
        }, 800);
      },
      error: function() {
        toastr.error('Failed to save special rate');
      }
    });
  });
});
</script>
@endpush
