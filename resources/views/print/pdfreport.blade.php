<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Diagnostic Report - {{ $investigation->patient->name ?? '' }}</title>
  <style>
    @page {
      margin: 15mm 15mm 20mm 15mm;
    }
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      font-size: 11px;
      color: #333;
      line-height: 1.4;
    }
    .header-table {
      width: 100%;
      border-bottom: 2px solid #0056b3;
      padding-bottom: 8px;
      margin-bottom: 12px;
    }
    .lab-title {
      font-size: 18px;
      font-weight: bold;
      color: #0056b3;
      margin: 0;
    }
    .patient-box {
      width: 100%;
      border: 1px solid #ddd;
      background-color: #f9fbfd;
      border-radius: 4px;
      margin-bottom: 15px;
      border-collapse: collapse;
    }
    .patient-box td {
      padding: 5px 8px;
      font-size: 11px;
    }
    .test-section {
      margin-bottom: 20px;
    }
    .test-header {
      font-size: 13px;
      font-weight: bold;
      color: #0056b3;
      border-bottom: 1px solid #0056b3;
      padding-bottom: 3px;
      margin-bottom: 6px;
    }
    .report-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }
    .report-table th {
      background-color: #e9ecef;
      border-bottom: 1px solid #ccc;
      padding: 6px;
      text-align: left;
      font-size: 10px;
      text-transform: uppercase;
    }
    .report-table td {
      border-bottom: 1px solid #eee;
      padding: 5px 6px;
      font-size: 11px;
    }
    .bold-val {
      font-weight: bold;
      color: #d9534f;
    }
    .heading-row {
      background-color: #f8f9fa;
      font-weight: bold;
      color: #495057;
    }
    .signatures {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
    }
    .signatures td {
      text-align: center;
      width: 33.33%;
      vertical-align: bottom;
      font-size: 10px;
    }
    .qr-img {
      width: 65px;
      height: 65px;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <table class="header-table">
    <tr>
      <td style="width: 75%;">
        @if($lab->logo)
          <img src="{{ public_path('img/' . $lab->logo) }}" style="max-height: 45px;" alt="Logo"><br>
        @endif
        <h1 class="lab-title">{{ $lab->name }}</h1>
        <small>{{ $lab->address }}</small><br>
        <small>Phone: {{ $lab->phone }} | Email: {{ $lab->email }}</small>
      </td>
      <td style="width: 25%; text-align: right; vertical-align: top;">
        @if(!empty($qrcodePath) && file_exists(public_path('img/' . $qrcodePath)))
          <img src="{{ public_path('img/' . $qrcodePath) }}" class="qr-img" alt="QR Code"><br>
          <small style="font-size: 8px;">Scan to Verify</small>
        @endif
      </td>
    </tr>
  </table>

  <!-- Patient Details Box -->
  <table class="patient-box">
    <tr>
      <td style="width: 50%;"><strong>Patient Name:</strong> {{ $investigation->patient->title }} {{ $investigation->patient->name }}</td>
      <td style="width: 50%;"><strong>Patient UID:</strong> {{ $investigation->patient->unique_id }}</td>
    </tr>
    <tr>
      <td><strong>Age / Gender:</strong> {{ $investigation->patient->age }} Yrs / {{ $investigation->patient->gender }}</td>
      <td><strong>Order Ref:</strong> INV-{{ $investigation->id }}</td>
    </tr>
    <tr>
      <td><strong>Referred By:</strong> {{ $investigation->labtolab ? $investigation->labtolab->lab_name : ($investigation->doctor ? ('Dr. ' . $investigation->doctor->doctor_name) : ($investigation->refered_by ?: 'Self / Direct')) }}</td>
      <td><strong>Registered Date:</strong> {{ $investigation->created_on ? \Carbon\Carbon::parse($investigation->created_on)->format('d-M-Y h:i A') : '-' }}</td>
    </tr>
  </table>

  <!-- Diagnostic Tests & Results -->
  @foreach($investigation->investigationTests as $it)
    @php
      $test = $it->diagnosticstest;
      $results = $it->investigationTestResults->keyBy('parameter_id');
      $params = $test ? $test->parameters : collect();
    @endphp

    <div class="test-section">
      <div class="test-header">
        {{ $test->name ?? 'Diagnostic Test' }}
        @if($test && $test->department)
          <span style="font-size: 10px; font-weight: normal; color: #666;">({{ $test->department->name }})</span>
        @endif
      </div>

      <table class="report-table">
        <thead>
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
                <tr class="heading-row">
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
                  <td class="{{ $isBold ? 'bold-val' : '' }}"><strong>{{ $val }}</strong></td>
                  <td>{{ $p->units ?: '-' }}</td>
                  <td>{{ $p->default_value ?: '-' }}</td>
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
              <td class="{{ $isBold ? 'bold-val' : '' }}"><strong>{{ $val }}</strong></td>
              <td>{{ $test->units ?? '-' }}</td>
              <td>{{ $test->reference_val ?? '-' }}</td>
            </tr>
          @endif
        </tbody>
      </table>

      @if($it->notes)
        <div style="font-size: 10px; color: #555; margin-top: 4px; padding: 4px; background: #fafafa; border-left: 3px solid #0056b3;">
          <strong>Notes / Interpretation:</strong> {{ $it->notes }}
        </div>
      @endif
    </div>
  @endforeach

  <!-- Signatures -->
  <table class="signatures">
    <tr>
      <td>
        <div style="height: 35px;"></div>
        <strong>Medical Lab Technician</strong><br>
        <span style="color: #666;">Prepared By</span>
      </td>
      <td>
        @if($lab->lab_seal && file_exists(public_path('img/' . $lab->lab_seal)))
          <img src="{{ public_path('img/' . $lab->lab_seal) }}" style="max-height: 45px;" alt="Seal">
        @else
          <div style="height: 35px;"></div>
        @endif
        <br><strong>Laboratory In-Charge</strong>
      </td>
      <td>
        @if($pathologist && $pathologist->signature && file_exists(public_path('img/' . $pathologist->signature)))
          <img src="{{ public_path('img/' . $pathologist->signature) }}" style="max-height: 40px;" alt="Signature"><br>
        @else
          <div style="height: 35px;"></div>
        @endif
        <strong>{{ $pathologist->name ?? 'Chief Pathologist' }}</strong><br>
        <span style="color: #666;">Consultant Pathologist (MD)</span>
      </td>
    </tr>
  </table>

  <div style="text-align: center; font-size: 9px; color: #888; margin-top: 20px; border-top: 1px dotted #ccc; padding-top: 5px;">
    *** End of Diagnostic Pathology Report ***
  </div>
</body>
</html>
