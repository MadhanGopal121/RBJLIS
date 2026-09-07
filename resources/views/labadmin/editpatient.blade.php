@extends('layouts.app')

@section('title', 'Edit Patient Demographics - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Edit Patient: {{ $patient->name }} ({{ $patient->unique_id }})</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('labadmin.editpatient.post') }}" method="POST">
          @csrf
          <input type="hidden" name="id" value="{{ $patient->id }}">

          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label for="title">Title</label>
                <select name="title" id="title" class="form-control">
                  @foreach(['Mr', 'Mrs', 'Ms', 'Master', 'Baby', 'Dr'] as $t)
                    <option value="{{ $t }}" {{ $patient->title == $t ? 'selected' : '' }}>{{ $t }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-9">
              <div class="form-group">
                <label for="name">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $patient->name) }}" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="age">Age</label>
                <input type="text" name="age" id="age" value="{{ old('age', $patient->age) }}" class="form-control" placeholder="e.g. 35">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="gender">Gender</label>
                <select name="gender" id="gender" class="form-control">
                  <option value="Male" {{ $patient->gender == 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ $patient->gender == 'Female' ? 'selected' : '' }}>Female</option>
                  <option value="Other" {{ $patient->gender == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $patient->phone) }}" class="form-control">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $patient->email) }}" class="form-control">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="aadhar">Aadhar / ID Number</label>
            <input type="text" name="aadhar" id="aadhar" value="{{ old('aadhar', $patient->aadhar) }}" class="form-control">
          </div>

          <div class="form-group">
            <label for="address">Address</label>
            <textarea name="address" id="address" class="form-control" rows="3">{{ old('address', $patient->address) }}</textarea>
          </div>

          <div class="row mt-4">
            <div class="col-12">
              <a href="{{ route('labadmin.patients') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success">Update Patient</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
