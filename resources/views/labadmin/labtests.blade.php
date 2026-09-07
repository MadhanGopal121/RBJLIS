@extends('layouts.app')

@section('title', 'Predefined Diagnostic Tests - RBJLIS')

@push('css')
<style>
.small-txt { width: 110px; }
.note-editing-area { background-color: #fff; }
</style>
@endpush

@section('content')
<!-- Default Notes & Interpretation Modal -->
<div class="modal fade" id="defaultnotesModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="updatenotesinterpretation" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Default Notes & Interpretation for <span class="tstname font-weight-bold"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" class="defaulttestid" />
          <div class="form-group">
            <label>Note / Summary</label>
            <textarea class="form-control defaultnote" name="note" rows="2"></textarea>
          </div>
          <div class="form-group">
            <label>Default Notes (Rich Text)</label>
            <textarea class="form-control defaultnotes" name="notes"></textarea>
          </div>
          <div class="form-group">
            <label>Clinical Interpretation (Rich Text)</label>
            <textarea class="form-control interpretation" name="interpretation"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Notes</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Parameters Modal -->
<div class="modal fade" id="listparameterModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Test Parameters Management</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" class="testid" />
        <table class="table text-nowrap table-head-fixed mb-3">
          <tr>
            <th>Type: 
              <select id="choosetype" name="choosetype" class="form-control-sm ml-2">
                <option value="parameter">Parameter</option>
                <option value="heading">Heading / Section</option>
              </select>
            </th>
          </tr>
        </table>

        <div class="addtype addtype-heading" style="display: none;">
          <div class="input-group mb-3">
            <input type="text" name="parametername" class="form-control headingname" placeholder="Section Heading Name">
            <div class="input-group-append">
              <button type="button" class="btn btn-primary parametersave">Add Heading</button>
            </div>
          </div>
        </div>

        <div class="addtype addtype-parameter">
          <div class="row mb-3">
            <div class="col-md-3">
              <input type="text" name="parametername" class="form-control parametername" placeholder="Parameter Name">
            </div>
            <div class="col-md-3">
              <input type="text" name="parameterdefault" class="form-control parameterdefault" placeholder="Reference / Default Value">
            </div>
            <div class="col-md-2">
              <input type="text" name="parameterunits" class="form-control parameterunits" placeholder="Units (e.g. g/dL)">
            </div>
            <div class="col-md-2">
              <input type="text" name="method" class="form-control method" placeholder="Method">
            </div>
            <div class="col-md-2">
              <button type="button" class="btn btn-primary btn-block parametersave">Add Parameter</button>
            </div>
          </div>
        </div>

        <div class="card-body paralist" style="padding: 0px; max-height: 350px; overflow-y: auto;"></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold">Laboratory Predefined Diagnostic Tests</h3>
        <div class="card-tools ml-auto">
          <form action="{{ route('labadmin.labtests') }}" method="GET" class="form-inline">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Search tests..." value="{{ request('q') }}">
            <button type="submit" class="btn btn-sm btn-primary ml-1"><i class="fas fa-search"></i></button>
          </form>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table id="labtesttble" class="table table-hover table-sm text-nowrap table-head-fixed">
          <thead class="thead-light">
            <tr>
              <th>Test Code</th>
              <th>Test Name</th>
              <th>Department</th>
              <th>Sample Type</th>
              <th>Patient Price</th>
              <th>B2B Price</th>
              <th>Created Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="labtests">
            @forelse($labtests as $mt)
              <tr>
                <td>
                  <textarea id="defaultnote_{{ $mt->id }}" style="display: none;">{{ $mt->notes }}</textarea>
                  <textarea id="note_{{ $mt->id }}" style="display: none;">{{ $mt->note }}</textarea>
                  <textarea id="interpretation_{{ $mt->id }}" style="display: none;">{{ $mt->interpretation }}</textarea>
                  <input class="small-txt form-control form-control-sm" type="text" id="testcode-{{ $mt->id }}" value="{{ $mt->test_code }}" placeholder="Code">
                </td>
                <td><strong>{{ $mt->name }}</strong></td>
                <td><span class="badge badge-info">{{ $mt->department->name ?? 'General' }}</span></td>
                <td><input class="small-txt form-control form-control-sm" type="text" id="sampletype-{{ $mt->id }}" value="{{ $mt->sample_type }}" placeholder="e.g. Serum"></td>
                <td><input class="small-txt form-control form-control-sm" type="number" step="0.01" id="testprice-{{ $mt->id }}" value="{{ $mt->price }}"></td>
                <td><input class="small-txt form-control form-control-sm" type="number" step="0.01" id="labtestprice-{{ $mt->id }}" value="{{ $mt->lab_price }}"></td>
                <td>{{ $mt->created_on ? \Carbon\Carbon::parse($mt->created_on)->format('d/m/Y') : '-' }}</td>
                <td>
                  <a href="javascript:void(0)" class="btn btn-xs btn-success updatelabtest" id="{{ $mt->id }}" title="Save inline changes"><i class="fas fa-check"></i> Save</a>
                  <a href="javascript:void(0)" data-id="{{ $mt->id }}" class="btn btn-xs btn-info testparameters" title="Manage Parameters"><i class="fas fa-grip-vertical"></i> Parameters</a>
                  <a href="javascript:void(0)" testname="{{ $mt->name }}" class="btn btn-xs btn-secondary updatetestnotes" id="{{ $mt->id }}" title="Notes & Interpretation"><i class="fas fa-notes-medical"></i> Notes</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">No diagnostic tests found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="p-3">
          {{ $labtests->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('.defaultnotes, .interpretation, .defaultnote').summernote({ height: 120 });

  $('body').on('change', '#choosetype', function(){
    var sel = $(this).val();
    $('.addtype').hide();
    $('.addtype-' + sel).show();
  });

  // Save inline test info
  $('body').on('click', '.updatelabtest', function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    var test_code = $('#testcode-' + id).val();
    var sample_type = $('#sampletype-' + id).val();
    var price = $('#testprice-' + id).val();
    var lab_price = $('#labtestprice-' + id).val();

    $.ajax({
      url: "{{ route('labadmin.updatetest') }}",
      type: "POST",
      data: {
        id: id,
        test_code: test_code,
        sample_type: sample_type,
        price: price,
        lab_price: lab_price,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function(resp) {
        toastr.success(resp.message || 'Test updated successfully');
      },
      error: function() {
        toastr.error('Failed to update test');
      }
    });
  });

  // Open Notes Modal
  $('body').on('click', '.updatetestnotes', function(e) {
    e.preventDefault();
    var tstid = $(this).attr('id');
    var tstname = $(this).attr('testname');
    var notes = $('#defaultnote_' + tstid).val();
    var note = $('#note_' + tstid).val();
    var interpretation = $('#interpretation_' + tstid).val();

    $('.tstname').html(tstname);
    $('.defaulttestid').val(tstid);
    $('.defaultnotes').summernote('code', notes);
    $('.defaultnote').summernote('code', note);
    $('.interpretation').summernote('code', interpretation);
    $('#defaultnotesModal').modal('show');
  });

  // Submit Notes
  $('#updatenotesinterpretation').on('submit', function(e) {
    e.preventDefault();
    var id = $('.defaulttestid').val();
    var notes = $('.defaultnotes').val();
    var note = $('.defaultnote').val();
    var interpretation = $('.interpretation').val();

    $.ajax({
      url: "{{ route('labadmin.updatetest') }}",
      type: "POST",
      data: {
        id: id,
        notes: notes,
        note: note,
        interpretation: interpretation,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function() {
        toastr.success('Notes & Interpretation saved successfully');
        $('#defaultnote_' + id).val(notes);
        $('#note_' + id).val(note);
        $('#interpretation_' + id).val(interpretation);
        $('#defaultnotesModal').modal('hide');
      }
    });
  });

  // Open Parameters Modal
  $('body').on('click', '.testparameters', function(e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    $('.testid').val(id);
    loadParameters(id);
    $('#listparameterModal').modal('show');
  });

  function loadParameters(testId) {
    $.ajax({
      url: "{{ route('labadmin.listparameters') }}",
      type: "GET",
      data: { id: testId },
      success: function(html) {
        $('.paralist').html(html);
      }
    });
  }

  // Add parameter
  $('body').on('click', '.parametersave', function() {
    var testId = $('.testid').val();
    var addtype = $('#choosetype').val();
    var parametername = (addtype === 'heading') ? $('.headingname').val() : $('.parametername').val();
    var parameterdefault = $('.parameterdefault').val();
    var parameterunits = $('.parameterunits').val();
    var method = $('.method').val();

    if (!parametername) {
      toastr.error('Parameter name is required');
      return;
    }

    $.ajax({
      url: "{{ route('labadmin.addparameters') }}",
      type: "POST",
      data: {
        testid: testId,
        addtype: addtype,
        parametername: parametername,
        parameterdefault: parameterdefault,
        parameterunits: parameterunits,
        method: method,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function() {
        toastr.success('Parameter added');
        $('.parametername, .parameterdefault, .parameterunits, .method, .headingname').val('');
        loadParameters(testId);
      }
    });
  });

  // Update parameter row
  $('body').on('click', '.updatetestparameters', function() {
    var id = $(this).attr('id');
    var parname = $("#paramname_" + id).val();
    var pardefaults = $("#paramdefault_" + id).val();
    var parunits = $("#paramunits_" + id).val();
    var parmethod = $("#parammethod_" + id).val();

    $.ajax({
      url: "{{ route('labadmin.updatetestparams') }}",
      type: "POST",
      data: {
        id: id,
        parname: parname,
        pardefaults: pardefaults,
        parunits: parunits,
        parmethod: parmethod,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function(msg) {
        toastr.success(msg || 'Parameter updated');
      }
    });
  });

  // Delete parameter row
  $('body').on('click', '.deleteparameter', function() {
    if (!confirm('Are you sure you want to delete this parameter?')) return;
    var id = $(this).attr('data-id');
    var testId = $('.testid').val();

    $.ajax({
      url: "{{ route('labadmin.deleteparameter') }}",
      type: "POST",
      data: {
        id: id,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function() {
        toastr.success('Parameter deleted');
        loadParameters(testId);
      }
    });
  });
});
</script>
@endpush
