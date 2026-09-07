@extends('layouts.app')

@section('title', 'Change Password - RBJLIS')

@section('content')
<div class="row">
  <div class="col-lg-6 col-md-8 mx-auto">
    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
      <div class="card-header bg-white p-3 border-bottom d-flex align-items-center">
        <div class="rounded-circle bg-primary-light text-primary d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: #EEF2FF;">
          <i class="fas fa-key fa-lg"></i>
        </div>
        <div>
          <h5 class="card-title font-weight-bold text-dark mb-0">Change Account Password</h5>
          <small class="text-muted">Ensure your account is using a strong security password</small>
        </div>
      </div>
      
      <div class="card-body p-4">
        <form action="{{ route('password.change.post') }}" method="POST">
          @csrf
          
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark">User Account</label>
            <div class="p-2 bg-light rounded border d-flex justify-content-between align-items-center">
              <div>
                <strong>{{ auth()->user()->name }}</strong><br>
                <small class="text-muted">{{ auth()->user()->email }}</small>
              </div>
              <span class="badge badge-info">{{ auth()->user()->role->name ?? 'User' }}</span>
            </div>
          </div>

          <div class="form-group mb-3">
            <label for="oldPassword" class="font-weight-bold text-dark">Current Password <span class="text-danger">*</span></label>
            <input type="password" name="old_password" id="oldPassword" class="form-control" placeholder="Enter current password" required>
          </div>

          <div class="form-group mb-4">
            <label for="newPassword" class="font-weight-bold text-dark">New Password <span class="text-danger">*</span></label>
            <input type="password" name="new_password" id="newPassword" class="form-control" placeholder="Minimum 6 characters" required minlength="6">
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
              <i class="fas fa-arrow-left mr-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary font-weight-bold px-4">
              <i class="fas fa-check-circle mr-1"></i> Update Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
