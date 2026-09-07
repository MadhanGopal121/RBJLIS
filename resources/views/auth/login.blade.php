@extends('layouts.auth')

@section('title', 'Sign In - RBJLIS Healthcare')

@section('content')
<div class="text-center mb-4">
  <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3 shadow" style="width: 58px; height: 58px; background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%) !important;">
    <i class="fas fa-microscope fa-lg"></i>
  </div>
  <h3 class="font-weight-bold text-dark mb-1" style="letter-spacing: -0.02em;">Welcome to RBJLIS</h3>
  <p class="text-muted small">Diagnostic Pathology & Laboratory Information System</p>
</div>

@if(session('error'))
  <div class="alert alert-danger py-2 px-3 small border-0 mb-3" style="border-radius: 8px; background: #FEE2E2; color: #991B1B;">
    <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
  </div>
@endif

<form action="{{ route('login.post') }}" method="POST" id="loginForm">
  @csrf
  <div class="form-group mb-3">
    <label for="email" class="small font-weight-bold text-dark">Email Address</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text bg-light border-right-0" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;">
          <i class="fas fa-envelope text-muted"></i>
        </span>
      </div>
      <input type="email" name="email" id="email" class="form-control border-left-0" style="border-radius: 0 10px 10px 0;" placeholder="user@lab.com" value="{{ old('email', 'admin@rbjlis.com') }}" required autofocus>
    </div>
  </div>

  <div class="form-group mb-3">
    <label for="password" class="small font-weight-bold text-dark">Password</label>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text bg-light border-right-0" style="border-radius: 10px 0 0 10px; border-color: #CBD5E1;">
          <i class="fas fa-lock text-muted"></i>
        </span>
      </div>
      <input type="password" name="password" id="password" class="form-control border-left-0" style="border-radius: 0 10px 10px 0;" placeholder="••••••••" value="admin123" required>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="custom-control custom-checkbox">
      <input type="checkbox" class="custom-control-input" id="remember" name="remember" checked>
      <label class="custom-control-label small text-muted" for="remember">Remember me</label>
    </div>
  </div>

  <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold py-2 shadow-sm mb-4" style="border-radius: 10px;">
    Sign In to Portal <i class="fas fa-arrow-right ml-1"></i>
  </button>
</form>

<!-- Quick 1-Click Demo Logins -->
<div class="pt-3 border-top text-center">
  <small class="text-muted d-block mb-2 font-weight-bold text-uppercase" style="letter-spacing: 0.05em; font-size: 10px;">
    Quick Demo 1-Click Login
  </small>
  <div class="d-flex flex-wrap justify-content-center" style="gap: 6px;">
    <button type="button" class="btn btn-xs btn-outline-primary demo-btn" data-email="admin@rbjlis.com" data-pass="admin123">
      Super Admin
    </button>
    <button type="button" class="btn btn-xs btn-outline-primary demo-btn" data-email="labadmin@rbjlis.com" data-pass="admin123">
      Lab Admin
    </button>
    <button type="button" class="btn btn-xs btn-outline-primary demo-btn" data-email="frontoffice@rbjlis.com" data-pass="admin123">
      Front Office
    </button>
    <button type="button" class="btn btn-xs btn-outline-primary demo-btn" data-email="technician@rbjlis.com" data-pass="admin123">
      Technician
    </button>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('.demo-btn').on('click', function() {
    var email = $(this).data('email');
    var pass = $(this).data('pass');
    $('#email').val(email);
    $('#password').val(pass);
    $('#loginForm').submit();
  });
});
</script>
@endpush
