@extends('layouts.app')

@section('title', 'Add Department - RBJLIS')

@section('content')
<div class="row">
  <div class="col-md-6">
    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Add Master Department</h3>
      </div>
      <div class="card-body">
        <form action="{{ route('superadmin.adddepartment.post') }}" method="POST">
          @csrf
          <div class="form-group">
            <label for="name">Department Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" required placeholder="e.g. Hematology / Biochemistry">
          </div>

          <div class="form-group">
            <label for="price">Base Department Fee (optional)</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" value="0.00">
          </div>

          <div class="form-group">
            <div class="icheck-primary">
              <input type="checkbox" id="is_default" name="is_default">
              <label for="is_default">Default Department for new labs</label>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-12">
              <a href="{{ route('superadmin.departments') }}" class="btn btn-secondary">Cancel</a>
              <button type="submit" class="btn btn-success">Save Department</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
