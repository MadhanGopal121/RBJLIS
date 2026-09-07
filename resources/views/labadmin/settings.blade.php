@extends('layouts.app')

@section('title', 'Laboratory Settings - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-9">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title font-weight-bold">Laboratory Branding & Report Configuration</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('labadmin.settings.post') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label>Laboratory Name</label>
            <p class="form-control-static font-weight-bold">{{ $labinfo->name }} (Code: {{ $labinfo->shortname }})</p>
          </div>

          <div class="form-group">
            <label for="address">Address Printed on Reports / Letterheads</label>
            <textarea name="address" id="address" class="form-control" rows="3">{{ old('address', $labinfo->address) }}</textarea>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="logoimg">Lab Logo</label>
                @if($labinfo->logo)
                  <div class="mb-2">
                    <img src="{{ asset('img/' . $labinfo->logo) }}" alt="Logo" style="max-height: 60px; border: 1px solid #ddd; padding: 3px;">
                  </div>
                @endif
                <input type="file" name="logoimg" id="logoimg" class="form-control">
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label for="letterhead">Custom Letterhead Header</label>
                @if($labinfo->letter_head)
                  <div class="mb-2">
                    <img src="{{ asset('img/' . $labinfo->letter_head) }}" alt="Letterhead" style="max-height: 60px; border: 1px solid #ddd; padding: 3px;">
                  </div>
                @endif
                <input type="file" name="letterhead" id="letterhead" class="form-control">
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label for="sealimg">Lab Official Seal / Stamp</label>
                @if($labinfo->lab_seal)
                  <div class="mb-2">
                    <img src="{{ asset('img/' . $labinfo->lab_seal) }}" alt="Seal" style="max-height: 60px; border: 1px solid #ddd; padding: 3px;">
                  </div>
                @endif
                <input type="file" name="sealimg" id="sealimg" class="form-control">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="defult_notes">Default Report Footer Notes / Disclaimer</label>
            <textarea name="defult_notes" id="defult_notes" class="form-control" rows="3">{{ old('defult_notes', $labinfo->defult_notes) }}</textarea>
          </div>

          <div class="row mt-4">
            <div class="col-12">
              <a href="{{ route('labadmin.index') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success">Save Laboratory Settings</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
