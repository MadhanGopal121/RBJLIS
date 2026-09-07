@extends('layouts.app')

@section('title', 'Specimen & Sample Collection Queue - RBJLIS')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-primary card-outline">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold"><i class="fas fa-vial"></i> Phlebotomy & Specimen Collection Queue</h3>
        <div class="card-tools ml-auto">
          <a href="{{ route('labinvestigation.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back to Worklist</a>
        </div>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-sm text-nowrap">
          <thead class="thead-light">
            <tr>
              <th>Patient UID</th>
              <th>Patient Details</th>
              <th>Required Specimen Tests</th>
              <th>Sample Type</th>
              <th>Order Time</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($samples as $inv)
              @foreach($inv->investigationTests as $it)
                @if($it->specimen_by == 0)
                  <tr id="col_row_{{ $it->id }}">
                    <td><span class="badge badge-primary">{{ $inv->patient->unique_id ?? ('INV-' . $inv->id) }}</span></td>
                    <td>
                      <strong>{{ $inv->patient->name ?? 'N/A' }}</strong><br>
                      <small>{{ $inv->patient->age ?? '' }} / {{ $inv->patient->gender ?? '' }} | Phone: {{ $inv->patient->phone ?? '' }}</small>
                    </td>
                    <td><strong>{{ $it->diagnosticstest->name ?? 'Test' }}</strong></td>
                    <td><span class="badge badge-info">{{ $it->diagnosticstest->sample_type ?: 'Whole Blood' }}</span></td>
                    <td>{{ $inv->created_on ? \Carbon\Carbon::parse($inv->created_on)->format('d/m/Y h:i A') : '-' }}</td>
                    <td>
                      <button type="button" class="btn btn-sm btn-success marksamplecollected" data-id="{{ $it->id }}">
                        <i class="fas fa-check"></i> Collect Sample
                      </button>
                    </td>
                  </tr>
                @endif
              @endforeach
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">No pending specimen collections. All samples are collected.</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <div class="p-3">
          {{ $samples->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('.marksamplecollected').on('click', function() {
    var id = $(this).data('id');
    $.ajax({
      url: "{{ route('labinvestigation.collectsample') }}",
      type: "POST",
      data: {
        investigationtest_id: id,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function() {
        toastr.success('Sample collected successfully');
        $('#col_row_' + id).fadeOut();
      },
      error: function() {
        toastr.error('Failed to collect sample');
      }
    });
  });
});
</script>
@endpush
