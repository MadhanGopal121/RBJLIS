@extends('layouts.app')

@section('title', 'Add Partner Lab (B2B) - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Add Partner Laboratory Center (B2B)</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('labadmin.addlabtolab.post') }}" method="POST">
          @csrf
          <div class="form-group">
            <label for="lab_name">Laboratory Center Name <span class="text-danger">*</span></label>
            <input type="text" name="lab_name" id="lab_name" class="form-control" required placeholder="e.g. Metro Diagnostic Center">
          </div>

          <div class="form-group">
            <label for="contact_name">Contact Person</label>
            <input type="text" name="contact_name" id="contact_name" class="form-control" placeholder="Doctor / Manager Name">
          </div>

          <div class="form-group">
            <label for="contact_email">Contact / Login Email <span class="text-danger">*</span></label>
            <input type="email" name="contact_email" id="contact_email" class="form-control" required placeholder="b2b@metrodiagnostics.com">
          </div>

          <div class="form-group">
            <label for="contact_phone">Phone Number</label>
            <input type="text" name="contact_phone" id="contact_phone" class="form-control" placeholder="Mobile / Landline">
          </div>

          <div class="form-group">
            <label for="address">Center Address</label>
            <textarea name="address" id="address" class="form-control" rows="3"></textarea>
          </div>

          <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> A dedicated B2B portal login (Role: Lab-to-Lab) will be automatically created with default password <code>Test@1234</code>.
          </div>

          <div class="row mt-4">
            <div class="col-12">
              <a href="{{ route('labadmin.labs') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success">Register Partner Center</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
