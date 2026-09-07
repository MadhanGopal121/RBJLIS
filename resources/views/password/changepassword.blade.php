@extends('layouts.app')

@section('title', 'Change Password - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-8">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Update My Password</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('password.changepassword.post') }}" method="POST">
          @csrf
          <div class="form-group">
            <label>Name</label>
            <p class="form-control-static font-weight-bold">{{ auth()->user()->name }}</p>
          </div>

          <div class="form-group">
            <label>Email</label>
            <p class="form-control-static font-weight-bold">{{ auth()->user()->email }}</p>
          </div>

          <div class="form-group">
            <label for="oldPassword">Old Password</label>
            <input type="password" name="old_password" id="oldPassword" class="form-control" required>
          </div>

          <div class="form-group">
            <label for="newPassword">New Password</label>
            <input type="password" name="new_password" id="newPassword" class="form-control" required minlength="6">
          </div>

          <div class="row">
            <div class="col-12">
              <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success">Update Password</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
