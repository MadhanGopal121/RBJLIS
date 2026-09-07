@extends('layouts.app')

@section('title', ($viewTitle ?? 'Investigation Worklist') . ' - RBJLIS')

@section('content')
<!-- Result Entry Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
      <form action="{{ route('labinvestigation.updateresult') }}" method="POST" id="updateresultfrm">
        @csrf
        <div class="modal-header bg-dark text-white p-3">
          <h5 class="modal-title font-weight-bold mb-0">
            <i class="fas fa-microscope text-primary mr-2"></i> Enter Results: <span id="modal_test_name" class="text-info font-weight-bold"></span>
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4" id="modal_parameters_body">
          <div class="text-center py-5">
            <div class="spinner-border text-primary mb-2" role="status"></div>
            <p class="text-muted small">Loading test parameters...</p>
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary font-weight-bold px-4">
            <i class="fas fa-save mr-1"></i> Save & Authorize Results
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Decline Sample Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
      <form action="{{ route('frontoffice.declinetest') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="investigationtest_id" id="decline_it_id">
        <div class="modal-header bg-danger text-white p-3">
          <h5 class="modal-title font-weight-bold mb-0">
            <i class="fas fa-ban mr-2"></i> Reject / Decline Specimen Sample
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4">
          <div class="form-group mb-3">
            <label class="font-weight-bold text-dark">Reason for Specimen Rejection <span class="text-danger">*</span></label>
            <textarea name="reason" id="reason" class="form-control" rows="3" required placeholder="e.g. Hemolyzed specimen, Insufficient volume, Clotted blood, Wrong tube used"></textarea>
          </div>
          <div class="form-group mb-0">
            <label class="font-weight-bold text-dark">Photo Evidence (Optional)</label>
            <input type="file" name="filedata" id="filedata" class="form-control">
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger font-weight-bold px-4">Confirm Sample Rejection</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
  <div>
    <h3 class="font-weight-bold text-dark mb-1" style="letter-spacing: -0.02em;">
      {{ $viewTitle ?? 'Investigation Worklist' }}
    </h3>
    <p class="text-muted small mb-0">Monitor phlebotomy collection, result entry, pathologist authentication, and sign-offs</p>
  </div>
  <div class="mt-2 mt-md-0">
    <a href="{{ route('labinvestigation.collectsample') }}" class="btn btn-dark shadow-sm font-weight-bold">
      <i class="fas fa-barcode mr-1"></i> Phlebotomy Collector
    </a>
  </div>
</div>

<!-- Queue Navigation Tabs -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-body p-2 d-flex flex-wrap" style="gap: 6px;">
    <a href="{{ route('labinvestigation.index') }}" class="btn btn-sm {{ request()->routeIs('labinvestigation.index') ? 'btn-primary shadow-sm' : 'btn-outline-primary' }}">
      <i class="fas fa-list mr-1"></i> All Worklist
    </a>
    <a href="{{ route('labinvestigation.pendingtest') }}" class="btn btn-sm {{ request()->routeIs('labinvestigation.pendingtest') ? 'btn-warning shadow-sm' : 'btn-outline-primary' }}">
      <i class="fas fa-hourglass-half mr-1"></i> Pending Result Entry
    </a>
    <a href="{{ route('labinvestigation.processedtests') }}" class="btn btn-sm {{ request()->routeIs('labinvestigation.processedtests') ? 'btn-info shadow-sm' : 'btn-outline-primary' }}">
      <i class="fas fa-clipboard-check mr-1"></i> Processed
    </a>
    <a href="{{ route('labinvestigation.verifiedtest') }}" class="btn btn-sm {{ request()->routeIs('labinvestigation.verifiedtest') ? 'btn-secondary shadow-sm' : 'btn-outline-primary' }}">
      <i class="fas fa-user-check mr-1"></i> Verified
    </a>
    <a href="{{ route('labinvestigation.approvedtests') }}" class="btn btn-sm {{ request()->routeIs('labinvestigation.approvedtests') ? 'btn-success shadow-sm' : 'btn-outline-primary' }}">
      <i class="fas fa-stamp mr-1"></i> Approved & Signed
    </a>
  </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm">
  <div class="card-body table-responsive p-0">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Order / Patient UID</th>
          <th>Patient Information</th>
          <th>Diagnostic Test</th>
          <th>Specimen Status</th>
          <th>Technician Entry</th>
          <th>Approval Stage</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($samples as $inv)
          @foreach($inv->investigationTests as $it)
            <tr id="row_it_{{ $it->id }}">
              <td>
                <span class="badge badge-primary font-weight-bold">{{ $inv->patient->unique_id ?? ('INV-' . $inv->id) }}</span>
                @if($it->is_emergency)
                  <span class="badge badge-danger ml-1">STAT</span>
                @endif
              </td>
              <td>
                <strong>{{ $inv->patient->name ?? 'N/A' }}</strong><br>
                <small class="text-muted">{{ $inv->patient->age ?? '' }} Yrs / {{ $inv->patient->gender ?? '' }} | {{ $inv->patient->phone ?? '' }}</small>
              </td>
              <td>
                <strong>{{ $it->diagnosticstest->name ?? 'Diagnostic Test' }}</strong>
                @if($it->is_declined)
                  <span class="badge badge-danger ml-1">Declined</span>
                @endif
              </td>
              <td>
                @if($it->specimen_by > 0)
                  <span class="badge badge-success"><i class="fas fa-check"></i> Collected</span>
                  <br><small class="text-muted">{{ $it->specimen_time ? \Carbon\Carbon::parse($it->specimen_time)->format('h:i A') : '' }}</small>
                @else
                  <button type="button" class="btn btn-xs btn-outline-primary collectsamplebtn" data-id="{{ $it->id }}">
                    <i class="fas fa-vial"></i> Collect
                  </button>
                @endif
              </td>
              <td>
                @if($it->test_by > 0)
                  <span class="badge badge-info"><i class="fas fa-check-circle"></i> Entered</span>
                  <br><small class="text-muted">{{ $it->technician->name ?? 'Technician' }}</small>
                @else
                  <span class="badge badge-secondary">Pending</span>
                @endif
              </td>
              <td>
                @if($it->approved_by > 0)
                  <span class="badge badge-success"><i class="fas fa-stamp"></i> Approved</span>
                  <br><small class="text-muted">{{ $it->approver->name ?? 'Pathologist' }}</small>
                @elseif($it->authenticated_by > 0)
                  <span class="badge badge-warning"><i class="fas fa-clipboard-check"></i> Verified</span>
                  <br><small class="text-muted">{{ $it->authenticator->name ?? 'Doctor' }}</small>
                @else
                  <span class="badge badge-light border">In Progress</span>
                @endif
              </td>
              <td>
                <div class="d-flex" style="gap: 4px;">
                  @if($it->specimen_by > 0 && !$it->is_declined)
                    <button type="button" class="btn btn-xs btn-primary openresultmodal" data-testid="{{ $it->test_id }}" data-invtestid="{{ $it->id }}" data-testname="{{ $it->diagnosticstest->name ?? '' }}">
                      <i class="fas fa-edit"></i> Result Entry
                    </button>
                  @endif

                  @if(!$it->is_declined && $it->approved_by == 0)
                    <button type="button" class="btn btn-xs btn-outline-danger declinetestbtn" data-id="{{ $it->id }}" title="Reject Sample">
                      <i class="fas fa-ban"></i>
                    </button>
                  @endif

                  @if($it->approved_by > 0)
                    <a href="{{ route('print.report', ['id' => $inv->id]) }}" target="_blank" class="btn btn-xs btn-success" title="Download Report">
                      <i class="fas fa-file-pdf"></i> Report
                    </a>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-5">
              <i class="fas fa-clipboard-list fa-3x mb-3 text-slate-300 d-block"></i>
              No investigation orders in this queue.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="p-3">
      {{ $samples->links('pagination::bootstrap-4') }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('.collectsamplebtn').on('click', function() {
    var itId = $(this).data('id');
    $.ajax({
      url: "{{ route('labinvestigation.collectsample') }}",
      type: "POST",
      data: {
        investigationtest_id: itId,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function() {
        toastr.success('Sample collected successfully');
        setTimeout(function() { location.reload(); }, 500);
      },
      error: function() {
        toastr.error('Failed to collect sample');
      }
    });
  });

  $('.openresultmodal').on('click', function() {
    var testId = $(this).data('testid');
    var invtestId = $(this).data('invtestid');
    var testName = $(this).data('testname');

    $('#modal_test_name').text(testName);
    $('#modal_parameters_body').html('<div class="text-center py-5"><div class="spinner-border text-primary mb-2"></div><p class="text-muted small">Loading test parameters...</p></div>');
    $('#resultModal').modal('show');

    $.ajax({
      url: "{{ route('labinvestigation.getparameters') }}",
      type: "GET",
      data: { testid: testId, invtestid: invtestId },
      success: function(html) {
        $('#modal_parameters_body').html(html);
      },
      error: function() {
        $('#modal_parameters_body').html('<div class="alert alert-danger">Failed to load parameters.</div>');
      }
    });
  });

  $('.declinetestbtn').on('click', function() {
    var itId = $(this).data('id');
    $('#decline_it_id').val(itId);
    $('#declineModal').modal('show');
  });
});
</script>
@endpush
