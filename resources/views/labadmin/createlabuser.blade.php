@extends('layouts.app')

@section('title', isset($userinfo->id) ? 'Edit User - RBJLIS' : 'Add User - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">{{ isset($userinfo->id) ? 'Edit Staff User' : 'Register Staff User' }}</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('labadmin.addlabuser.post') }}" method="POST">
          @csrf
          @if(isset($userinfo->id))
            <input type="hidden" name="id" value="{{ $userinfo->id }}">
          @endif

          <div class="form-group">
            <label for="name">Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $userinfo->name ?? '') }}" class="form-control" required placeholder="e.g. Ramesh Kumar">
          </div>

          <div class="form-group">
            <label for="email">Email Address <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" value="{{ old('email', $userinfo->email ?? '') }}" class="form-control" required placeholder="user@lab.com">
          </div>

          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $userinfo->phone ?? '') }}" class="form-control" placeholder="10-digit mobile number">
          </div>

          <div class="form-group">
            <label for="designation">Designation</label>
            <input type="text" name="designation" id="designation" value="{{ old('designation', $userinfo->designation ?? '') }}" class="form-control" placeholder="e.g. Senior Technician, Receptionist">
          </div>

          <div class="form-group">
            <label for="role_id">System Role <span class="text-danger">*</span></label>
            <select name="role_id" id="role_id" class="form-control" required>
              <option value="">-- Select Role --</option>
              @foreach($roles as $r)
                <option value="{{ $r->id }}" {{ (old('role_id', $userinfo->role_id ?? '') == $r->id) ? 'selected' : '' }}>{{ $r->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label>Assigned Department Access</label>
            <div class="row">
              @php
                $userDepts = $userinfo->dep ?? [];
              @endphp
              @foreach($departments as $d)
                <div class="col-md-4 mb-2">
                  <div class="icheck-primary">
                    <input type="checkbox" id="dept_{{ $d->id }}" name="department[]" value="{{ $d->id }}" {{ in_array($d->id, $userDepts) ? 'checked' : '' }}>
                    <label for="dept_{{ $d->id }}">{{ $d->name }}</label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="form-group">
            <label for="passworda">Password {{ isset($userinfo->id) ? '(Leave blank to keep unchanged)' : '' }} <span class="text-danger">{{ !isset($userinfo->id) ? '*' : '' }}</span></label>
            <input type="password" name="passworda" id="passworda" class="form-control" {{ !isset($userinfo->id) ? 'required minlength=6' : '' }}>
          </div>

          <div class="row mt-4">
            <div class="col-12">
              <a href="{{ route('labadmin.labusers') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success">{{ isset($userinfo->id) ? 'Save Changes' : 'Create User' }}</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
