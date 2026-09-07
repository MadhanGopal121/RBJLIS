@extends('layouts.app')

@section('title', isset($test->id) ? 'Edit Master Test - RBJLIS' : 'Add Master Test - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">{{ isset($test->id) ? 'Edit Master Test' : 'Add Master Test' }}</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('superadmin.addtest.post') }}" method="POST">
          @csrf
          @if(isset($test->id))
            <input type="hidden" name="id" value="{{ $test->id }}">
          @endif

          <div class="form-group">
            <label for="department_id">Department <span class="text-danger">*</span></label>
            <select name="department_id" id="department_id" class="form-control" required>
              <option value="">-- Select Department --</option>
              @foreach($departments as $d)
                <option value="{{ $d->id }}" {{ (old('department_id', $test->department_id ?? '') == $d->id) ? 'selected' : '' }}>{{ $d->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label for="name">Test Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $test->name ?? '') }}" class="form-control" required placeholder="e.g. Complete Blood Count (CBC)">
          </div>

          <div class="form-group">
            <label for="reference_val">Reference Value / Range</label>
            <input type="text" name="reference_val" id="reference_val" value="{{ old('reference_val', $test->reference_val ?? '') }}" class="form-control" placeholder="e.g. 13.0 - 17.0">
          </div>

          <div class="form-group">
            <label for="units">Measurement Units</label>
            <input type="text" name="units" id="units" value="{{ old('units', $test->units ?? '') }}" class="form-control" placeholder="e.g. g/dL, mg/dL, %">
          </div>

          <div class="form-group">
            <label for="price">Standard Price (Rs.)</label>
            <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $test->price ?? 0) }}" class="form-control">
          </div>

          <div class="form-group">
            <label for="method">Methodology / Technique</label>
            <input type="text" name="method" id="method" value="{{ old('method', $test->method ?? '') }}" class="form-control" placeholder="e.g. Automated Hematology Analyzer">
          </div>

          <div class="form-group">
            <label for="notes">Clinical Notes / Comments</label>
            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $test->notes ?? '') }}</textarea>
          </div>

          <div class="row mt-3">
            <div class="col-12">
              <a href="{{ route('superadmin.tests') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success">{{ isset($test->id) ? 'Update Master Test' : 'Save Master Test' }}</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
