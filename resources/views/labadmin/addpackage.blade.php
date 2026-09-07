@extends('layouts.app')

@section('title', isset($profiles->id) ? 'Edit Package - RBJLIS' : 'Add Package - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-9">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">{{ isset($profiles->id) ? 'Edit Package / Health Profile' : 'Create Package / Health Profile' }}</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('labadmin.addpackage.post') }}" method="POST">
          @csrf
          @if(isset($profiles->id))
            <input type="hidden" name="id" value="{{ $profiles->id }}">
          @endif

          <div class="form-group">
            <label for="name">Package Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $profiles->name ?? '') }}" class="form-control" required placeholder="e.g. Master Health Checkup, Executive Lipid Profile">
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="price">Standard Patient Price (Rs.) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $profiles->price ?? 0) }}" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="lab_price">B2B / Partner Lab Price (Rs.)</label>
                <input type="number" step="0.01" name="lab_price" id="lab_price" value="{{ old('lab_price', $profiles->lab_price ?? 0) }}" class="form-control">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Select Diagnostic Tests Included in this Package</label>
            @php
              $selectedTests = [];
              if (isset($profiles->profileTests)) {
                  $selectedTests = $profiles->profileTests->pluck('diagnosticstest_id')->toArray();
              }
            @endphp
            <div class="row" style="max-height: 350px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
              @foreach($tests as $t)
                <div class="col-md-6 mb-2">
                  <div class="icheck-primary">
                    <input type="checkbox" id="test_{{ $t->id }}" name="packagetests[]" value="{{ $t->id }}" {{ in_array($t->id, $selectedTests) ? 'checked' : '' }}>
                    <label for="test_{{ $t->id }}"><strong>{{ $t->name }}</strong> <small class="text-muted">(Rs. {{ $t->price }})</small></label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-12">
              <a href="{{ route('labadmin.packages') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success">{{ isset($profiles->id) ? 'Update Package' : 'Create Package' }}</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
