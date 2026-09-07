<input type="hidden" name="invtestid" value="{{ $invtest->investigation_id }}">
<input type="hidden" name="invtid" value="{{ $invtest->investigation_id }}">
<input type="hidden" name="resultid" value="{{ $invtest->id }}">
<input type="hidden" name="investigationtest_id" value="{{ $invtest->id }}">
<input type="hidden" name="diagnostictestid" value="{{ $test->id }}">

<div class="row mb-3 p-3 bg-light rounded" style="border: 1px solid #E2E8F0;">
  <div class="col-md-6">
    <p class="mb-1"><strong>Patient:</strong> {{ $invtest->investigation->patient->name ?? 'N/A' }}</p>
    <p class="mb-0 text-muted small"><strong>UID:</strong> {{ $invtest->investigation->patient->unique_id ?? '' }} | {{ $invtest->investigation->patient->age ?? '' }} Yrs / {{ $invtest->investigation->patient->gender ?? '' }}</p>
  </div>
  <div class="col-md-6 text-md-right mt-2 mt-md-0">
    <span class="badge badge-info mb-1">{{ $test->sample_type ?: 'Whole Blood / Serum' }}</span><br>
    <small class="text-muted"><strong>Method:</strong> {{ $test->method ?: 'Automated Analyzer' }}</small>
  </div>
</div>

<table class="table table-bordered table-sm mb-3">
  <thead>
    <tr>
      <th style="width: 35%;">Parameter Name</th>
      <th style="width: 25%;">Observed Result Value</th>
      <th style="width: 15%;">Units</th>
      <th style="width: 15%;">Reference Range</th>
      <th style="width: 10%; text-align: center;">Abnormal</th>
    </tr>
  </thead>
  <tbody>
    @forelse($params as $p)
      @if($p->type == 'heading')
        <tr class="table-secondary font-weight-bold">
          <td colspan="5" class="py-2"><i class="fas fa-heading mr-1 text-muted"></i> {{ $p->name }}</td>
        </tr>
      @else
        @php
          $currentVal = $existingResults->has($p->id) ? $existingResults->get($p->id)->result : ($p->default_value ?: '');
          $isBold = $existingResults->has($p->id) ? $existingResults->get($p->id)->is_bold : false;
        @endphp
        <tr>
          <td>
            <strong>{{ $p->name }}</strong>
            <input type="hidden" name="parsort_{{ $p->id }}_{{ $test->id }}" value="{{ $p->sort }}">
          </td>
          <td>
            <input type="text" name="parresultval_{{ $p->id }}_{{ $test->id }}" class="form-control form-control-sm font-weight-bold" value="{{ $currentVal }}" required>
          </td>
          <td><small class="text-muted">{{ $p->units ?: '-' }}</small></td>
          <td><small class="text-muted">{{ $p->default_value ?: '-' }}</small></td>
          <td class="text-center">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="bold_{{ $p->id }}" name="pardefaultval_{{ $p->id }}_{{ $test->id }}" {{ $isBold ? 'checked' : '' }}>
              <label class="custom-control-label" for="bold_{{ $p->id }}"></label>
            </div>
          </td>
        </tr>
      @endif
    @empty
      @php
        $currentVal = $existingResults->has(0) ? $existingResults->get(0)->result : ($test->reference_val ?: '');
        $isBold = $existingResults->has(0) ? $existingResults->get(0)->is_bold : false;
      @endphp
      <tr>
        <td><strong>{{ $test->name }}</strong></td>
        <td>
          <input type="text" name="parresultval_" class="form-control form-control-sm font-weight-bold" value="{{ $currentVal }}" required>
        </td>
        <td><small class="text-muted">{{ $test->units ?? '-' }}</small></td>
        <td><small class="text-muted">{{ $test->reference_val ?? '-' }}</small></td>
        <td class="text-center">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="bold_single" name="pardefaultval_" {{ $isBold ? 'checked' : '' }}>
            <label class="custom-control-label" for="bold_single"></label>
          </div>
        </td>
      </tr>
    @endforelse
  </tbody>
</table>

<div class="form-group mb-2">
  <label for="reportnotes" class="font-weight-bold text-dark">Clinical Notes / Interpretation</label>
  <textarea name="reportnotes" id="reportnotes" class="form-control" rows="2" placeholder="Clinical observations, microscopic findings, or pathologist comments...">{{ $invtest->notes ?: $test->notes }}</textarea>
</div>

<div class="form-group mb-0">
  <div class="custom-control custom-checkbox">
    <input type="checkbox" class="custom-control-input" id="highlight" name="highlight" {{ $invtest->highlight ? 'checked' : '' }}>
    <label class="custom-control-label font-weight-bold text-danger" for="highlight">
      <i class="fas fa-exclamation-triangle mr-1"></i> Flag as Critical Abnormal Finding
    </label>
  </div>
</div>
