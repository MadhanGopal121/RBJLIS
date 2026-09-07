<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Report - {{ $investigation->patient->name ?? '' }} ({{ $investigation->patient->unique_id ?? '' }})</title>
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
  <style>
    body { background-color: #fff; font-size: 13px; }
    .report-container { max-width: 850px; margin: 20px auto; padding: 25px; border: 1px solid #ddd; }
    .bold-val { font-weight: bold; color: #d9534f; }
    @media print {
      body { padding: 0; }
      .report-container { border: none; padding: 0; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>
  <div class="no-print text-center my-3">
    <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Report</button>
    <a href="{{ route('print.report', ['id' => $investigation->id, 'pdf' => 1]) }}" class="btn btn-danger ml-2"><i class="fas fa-file-pdf"></i> Download PDF</a>
  </div>

  <div class="report-container">
    <!-- Header -->
    <div class="row pb-3 mb-3 border-bottom">
      <div class="col-8">
        @if($lab->logo)
          <img src="{{ asset('img/' . $lab->logo) }}" style="max-height: 50px;" alt="Logo"><br>
        @endif
        <h2 class="text-primary font-weight-bold mb-0">{{ $lab->name }}</h2>
        <small class="text-muted">{{ $lab->address }}</small><br>
        <small class="text-muted">Phone: {{ $lab->phone }} | Email: {{ $lab->email }}</small>
      </div>
      <div class="col-4 text-right">
        @if(!empty($qrcodePath) && file_exists(public_path('img/' . $qrcodePath)))
          <img src="{{ asset('img/' . $qrcodePath) }}" style="width: 75px; height: 75px;" alt="QR Code"><br>
          <small class="text-muted">Online Verification QR</small>
        @endif
      </div>
    </div>

    <!-- Patient Details -->
    <div class="card bg-light mb-4">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-6">
            <p class="mb-1"><strong>Patient Name:</strong> {{ $investigation->patient->title }} {{ $investigation->patient->name }}</p>
            <p class="mb-1"><strong>Age / Gender:</strong> {{ $investigation->patient->age }} Yrs / {{ $investigation->patient->gender }}</p>
            <p class="mb-1"><strong>Referred By:</strong> {{ $investigation->labtolab ? $investigation->labtolab->lab_name : ($investigation->doctor ? ('Dr. ' . $investigation->doctor->doctor_name) : ($investigation->refered_by ?: 'Self')) }}</p>
          </div>
          <div class="col-6 text-right">
            <p class="mb-1"><strong>Patient UID:</strong> <span class="badge badge-primary">{{ $investigation->patient->unique_id }}</span></p>
            <p class="mb-1"><strong>Order #:</strong> INV-{{ $investigation->id }}</p>
            <p class="mb-1"><strong>Registered:</strong> {{ $investigation->created_on ? \Carbon\Carbon::parse($investigation->created_on)->format('d M Y h:i A') : '-' }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tests & Results -->
    @foreach($investigation->investigationTests as $it)
      @php
        $test = $it->diagnosticstest;
        $results = $it->investigationTestResults->keyBy('parameter_id');
        $params = $test ? $test->parameters : collect();
      @endphp

      <div class="mb-4">
        <h5 class="text-primary font-weight-bold border-bottom pb-1 mb-2">
          {{ $test->name ?? 'Diagnostic Test' }}
          @if($test && $test->department)
            <small class="text-muted font-weight-normal">({{ $test->department->name }})</small>
          @endif
        </h5>

        <table class="table table-bordered table-sm">
          <thead class="thead-light">
            <tr>
              <th style="width: 35%;">Investigation Parameter</th>
              <th style="width: 25%;">Observed Result</th>
              <th style="width: 15%;">Units</th>
              <th style="width: 25%;">Biological Reference Interval</th>
            </tr>
          </thead>
          <tbody>
            @if($params->count() > 0)
              @foreach($params as $p)
                @if($p->type == 'heading')
                  <tr class="table-secondary font-weight-bold">
                    <td colspan="4">{{ $p->name }}</td>
                  </tr>
                @else
                  @php
                    $resObj = $results->get($p->id);
                    $val = $resObj ? $resObj->result : '';
                    $isBold = $resObj ? $resObj->is_bold : false;
                  @endphp
                  <tr>
                    <td>{{ $p->name }}</td>
                    <td class="{{ $isBold ? 'bold-val font-weight-bold' : '' }}">{{ $val }}</td>
                    <td>{{ $p->units ?: '-' }}</td>
                    <td><small class="text-muted">{{ $p->default_value ?: '-' }}</small></td>
                  </tr>
                @endif
              @endforeach
            @else
              @php
                $resObj = $results->get(0);
                $val = $resObj ? $resObj->result : '';
                $isBold = $resObj ? $resObj->is_bold : false;
              @endphp
              <tr>
                <td>{{ $test->name ?? 'Result' }}</td>
                <td class="{{ $isBold ? 'bold-val font-weight-bold' : '' }}">{{ $val }}</td>
                <td>{{ $test->units ?? '-' }}</td>
                <td><small class="text-muted">{{ $test->reference_val ?? '-' }}</small></td>
              </tr>
            @endif
          </tbody>
        </table>

        @if($it->notes)
          <div class="alert alert-light border p-2 small">
            <strong>Clinical Notes / Interpretation:</strong> {{ $it->notes }}
          </div>
        @endif
      </div>
    @endforeach

    <!-- Signatures -->
    <div class="row pt-5 mt-4 text-center">
      <div class="col-4">
        <div style="height: 40px;"></div>
        <p class="font-weight-bold mb-0">Medical Lab Technician</p>
        <small class="text-muted">Prepared By</small>
      </div>
      <div class="col-4">
        @if($lab->lab_seal && file_exists(public_path('img/' . $lab->lab_seal)))
          <img src="{{ asset('img/' . $lab->lab_seal) }}" style="max-height: 50px;" alt="Seal">
        @else
          <div style="height: 40px;"></div>
        @endif
        <p class="font-weight-bold mb-0">Laboratory Seal</p>
      </div>
      <div class="col-4">
        @if($pathologist && $pathologist->signature && file_exists(public_path('img/' . $pathologist->signature)))
          <img src="{{ asset('img/' . $pathologist->signature) }}" style="max-height: 45px;" alt="Signature"><br>
        @else
          <div style="height: 40px;"></div>
        @endif
        <p class="font-weight-bold mb-0">{{ $pathologist->name ?? 'Chief Pathologist' }}</p>
        <small class="text-muted">Consultant Pathologist (MD)</small>
      </div>
    </div>
  </div>
</body>
</html>
