@extends('layouts.app')

@section('title', isset($doctor->id) ? 'Edit Doctor - RBJLIS' : 'Add Doctor - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-7">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">{{ isset($doctor->id) ? 'Edit Referring Doctor' : 'Add Referring Doctor' }}</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('labadmin.adddoctor.post') }}" method="POST">
          @csrf
          @if(isset($doctor->id))
            <input type="hidden" name="id" value="{{ $doctor->id }}">
          @endif

          <div class="form-group">
            <label for="doctor_name">Doctor Name <span class="text-danger">*</span></label>
            <input type="text" name="doctor_name" id="doctor_name" value="{{ old('doctor_name', $doctor->doctor_name ?? '') }}" class="form-control" required placeholder="e.g. S. Radhakrishnan">
          </div>

          <div class="form-group">
            <label for="clinic_name">Clinic / Hospital Name</label>
            <input type="text" name="clinic_name" id="clinic_name" value="{{ old('clinic_name', $doctor->clinic_name ?? '') }}" class="form-control" placeholder="e.g. City Health Clinic">
          </div>

          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $doctor->phone ?? '') }}" class="form-control" placeholder="10-digit phone">
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" value="{{ old('email', $doctor->email ?? '') }}" class="form-control" placeholder="doctor@clinic.com">
          </div>

          <div class="form-group">
            <label for="discount">Referral Discount / Cut (%)</label>
            <input type="number" step="0.01" name="discount" id="discount" value="{{ old('discount', $doctor->discount ?? 0) }}" class="form-control" placeholder="e.g. 10">
          </div>

          <div class="row mt-4">
            <div class="col-12">
              <a href="{{ route('labadmin.doctors') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success">{{ isset($doctor->id) ? 'Update Doctor' : 'Save Doctor' }}</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
