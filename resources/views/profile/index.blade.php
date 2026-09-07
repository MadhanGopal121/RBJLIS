@extends('layouts.app')

@section('title', 'My Profile - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Update My Profile</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label>Name</label>
            <p class="form-control-static font-weight-bold">{{ $user->name }}</p>
          </div>

          <div class="form-group">
            <label for="designation">Designation</label>
            <input type="text" value="{{ old('designation', $user->designation) }}" name="designation" id="designation" class="form-control" placeholder="e.g. Chief Pathologist / Lab Technician">
          </div>

          <div class="form-group">
            <label>Email</label>
            <p class="form-control-static font-weight-bold">{{ $user->email }}</p>
          </div>

          <div class="form-group">
            <label>Phone</label>
            <p class="form-control-static font-weight-bold">{{ $user->phone ?? 'N/A' }}</p>
          </div>

          <div class="form-group">
            <label for="signimg">Signature Image</label>
            @if($user->signature)
              <div class="mb-2">
                <img src="{{ asset('img/' . $user->signature) }}" alt="Current Signature" style="max-height: 70px; border: 1px solid #ddd; padding: 4px; background: #fff;">
              </div>
            @endif
            <input type="file" name="signimg" id="signimg" class="form-control">
            <small class="text-muted">Allowed formats: PNG, JPG. Max 2MB.</small>
          </div>

          <div class="row">
            <div class="col-12">
              <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success">Update Profile</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
