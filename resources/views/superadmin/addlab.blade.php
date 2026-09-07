@extends('layouts.app')

@section('title', isset($labdata->id) ? 'Update Lab Center - RBJLIS' : 'Add Lab Center - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">{{ isset($labdata->id) ? 'Update Laboratory Center' : 'Add Laboratory Center' }}</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('superadmin.addlab.post') }}" method="POST" id="addlab">
          @csrf
          @if(isset($labdata->id))
            <input type="hidden" name="id" value="{{ $labdata->id }}">
          @endif

          <div class="form-group">
            <label for="name">Laboratory Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $labdata->name ?? '') }}" class="form-control" required placeholder="e.g. Apex Diagnostics">
          </div>

          <div class="form-group">
            <label for="shortname">Lab Short Code <span class="text-danger">*</span></label>
            <input type="text" name="shortname" id="shortname" value="{{ old('shortname', $labdata->shortname ?? '') }}" class="form-control" maxlength="4" placeholder="Max 4 characters (e.g. APEX)" {{ isset($labdata->id) ? 'readonly' : 'required' }}>
          </div>

          <div class="form-group">
            <label for="address">Laboratory Address</label>
            <textarea name="address" id="address" class="form-control" rows="3">{{ old('address', $labdata->address ?? '') }}</textarea>
          </div>

          <div class="form-group">
            <label>Select Accessible Departments</label>
            <div class="row">
              @php
                $selectedDepts = [];
                if (isset($labdata->labDepartments)) {
                    foreach ($labdata->labDepartments as $ld) {
                        if ($ld->status) {
                            $selectedDepts[] = $ld->department_id;
                        }
                    }
                }
              @endphp

              @foreach($departments as $d)
                <div class="form-check col-md-4 mb-2">
                  <input class="form-check-input" type="checkbox" name="department[]" value="{{ $d->id }}" id="dept_{{ $d->id }}" {{ in_array($d->id, $selectedDepts) || !isset($labdata->id) ? 'checked' : '' }}>
                  <label class="form-check-label" for="dept_{{ $d->id }}">{{ $d->name }}</label>
                </div>
              @endforeach
            </div>
          </div>

          <div class="form-group">
            <label for="user_count">Maximum User Limit</label>
            <input type="number" name="user_count" id="user_count" value="{{ old('user_count', $labdata->user_count ?? 5) }}" class="form-control" min="1">
          </div>

          <div class="form-group">
            <label for="contactname">Contact Person Name <span class="text-danger">*</span></label>
            <input type="text" name="contactname" id="contactname" value="{{ old('contactname', $labdata->contactname ?? '') }}" class="form-control" required placeholder="Administrator Name">
          </div>

          <div class="form-group">
            <label for="email">Contact / Login Email <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email', $labdata->email ?? '') }}" class="form-control" required placeholder="admin@labdomain.com">
          </div>

          <div class="form-group">
            <label for="passworda">Password {{ isset($labdata->id) ? '(Leave empty to keep current)' : '' }} <span class="text-danger">{{ !isset($labdata->id) ? '*' : '' }}</span></label>
            <input type="password" name="passworda" id="passworda" class="form-control" {{ !isset($labdata->id) ? 'required minlength=6' : '' }}>
          </div>

          <div class="form-group">
            <label for="phone">Contact Phone Number</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $labdata->phone ?? '') }}" class="form-control" placeholder="10-digit mobile number">
          </div>

          @if(!isset($labdata->id))
            <div class="form-group">
              <label for="membership">Membership Subscription Duration</label>
              <select name="membership" id="membership" class="form-control">
                <option value="3">3 Months</option>
                <option value="6">6 Months</option>
                <option value="12" selected>12 Months (1 Year)</option>
              </select>
            </div>
          @endif

          <div class="row mt-4">
            <div class="col-12">
              <a href="{{ route('superadmin.lablist') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success">{{ isset($labdata->id) ? 'Save Changes' : 'Register Laboratory' }}</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
