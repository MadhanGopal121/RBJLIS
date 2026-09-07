<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Receipt #{{ $bill->patient->unique_id ?? ('INV-' . $bill->id) }} - {{ $lab->name }}</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 13px;
      color: #333;
      margin: 0;
      padding: 20px;
    }
    .bill-container {
      max-width: 750px;
      margin: 0 auto;
      border: 1px solid #ccc;
      padding: 20px;
    }
    .header-table {
      width: 100%;
      border-bottom: 2px solid #007bff;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    .logo {
      max-height: 55px;
    }
    .patient-info {
      width: 100%;
      margin-bottom: 15px;
      border-collapse: collapse;
    }
    .patient-info td {
      padding: 4px;
      vertical-align: top;
    }
    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }
    .items-table th, .items-table td {
      border: 1px solid #ddd;
      padding: 8px;
    }
    .items-table th {
      background-color: #f2f2f2;
      text-align: left;
    }
    .totals-table {
      width: 40%;
      margin-left: auto;
      border-collapse: collapse;
    }
    .totals-table td {
      padding: 4px;
    }
    .footer {
      margin-top: 25px;
      text-align: center;
      font-size: 11px;
      color: #777;
      border-top: 1px solid #ddd;
      padding-top: 8px;
    }
    @media print {
      body { padding: 0; }
      .bill-container { border: none; }
      .no-print { display: none; }
    }
  </style>
</head>
<body onload="window.print()">
  <div class="no-print" style="text-align: center; margin-bottom: 15px;">
    <button onclick="window.print()" style="padding: 8px 16px; font-size: 14px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
      Print Receipt
    </button>
  </div>

  <div class="bill-container">
    <table class="header-table">
      <tr>
        <td style="width: 60%;">
          @if($lab->logo)
            <img src="{{ asset('img/' . $lab->logo) }}" class="logo" alt="{{ $lab->name }}"><br>
          @endif
          <h2 style="margin: 5px 0 2px 0; color: #007bff;">{{ $lab->name }}</h2>
          <small>{{ $lab->address }}</small><br>
          <small>Phone: {{ $lab->phone }} | Email: {{ $lab->email }}</small>
        </td>
        <td style="width: 40%; text-align: right; vertical-align: bottom;">
          <h3 style="margin: 0; text-transform: uppercase;">Investigation Receipt</h3>
          <p style="margin: 2px 0;"><strong>Receipt #:</strong> {{ $bill->patient->unique_id ?? ('INV-' . $bill->id) }}</p>
          <p style="margin: 2px 0;"><strong>Date:</strong> {{ $bill->created_on ? \Carbon\Carbon::parse($bill->created_on)->format('d M Y h:i A') : date('d M Y') }}</p>
        </td>
      </tr>
    </table>

    <table class="patient-info">
      <tr>
        <td style="width: 50%;">
          <strong>Patient Name:</strong> {{ $bill->patient->title }} {{ $bill->patient->name }}<br>
          <strong>Age / Gender:</strong> {{ $bill->patient->age }} Yrs / {{ $bill->patient->gender }}<br>
          <strong>Phone:</strong> {{ $bill->patient->phone ?? '-' }}
        </td>
        <td style="width: 50%;">
          <strong>Patient UID:</strong> {{ $bill->patient->unique_id }}<br>
          <strong>Referred By:</strong> {{ $bill->labtolab ? $bill->labtolab->lab_name : ($bill->doctor ? ('Dr. ' . $bill->doctor->doctor_name) : ($bill->refered_by ?: 'Self')) }}<br>
          <strong>Hospital/Clinic:</strong> {{ $bill->clinicname ?? '-' }}
        </td>
      </tr>
    </table>

    <table class="items-table">
      <thead>
        <tr>
          <th style="width: 10%;">#</th>
          <th style="width: 65%;">Investigation Description</th>
          <th style="width: 25%; text-align: right;">Amount (Rs.)</th>
        </tr>
      </thead>
      <tbody>
        @foreach($bill->investigationTests as $index => $it)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td><strong>{{ $it->diagnosticstest->name ?? 'Diagnostic Test' }}</strong></td>
            <td style="text-align: right;">Rs. {{ number_format($it->diagnosticstest->price ?? 0, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    @php
      $paid = $bill->labPayments->sum('payment_amount');
      $balance = $bill->total_amount - ($paid + $bill->discount);
    @endphp

    <table class="totals-table">
      <tr>
        <td><strong>Gross Total:</strong></td>
        <td style="text-align: right;">Rs. {{ number_format($bill->total_amount, 2) }}</td>
      </tr>
      @if($bill->discount > 0)
        <tr>
          <td><strong>Discount:</strong></td>
          <td style="text-align: right; color: #28a745;">- Rs. {{ number_format($bill->discount, 2) }}</td>
        </tr>
      @endif
      <tr>
        <td><strong>Amount Paid:</strong></td>
        <td style="text-align: right; color: #007bff; font-weight: bold;">Rs. {{ number_format($paid, 2) }}</td>
      </tr>
      <tr>
        <td><strong>Balance Due:</strong></td>
        <td style="text-align: right; color: {{ $balance > 0 ? '#dc3545' : '#28a745' }}; font-weight: bold;">
          Rs. {{ number_format($balance, 2) }}
        </td>
      </tr>
    </table>

    <div class="footer">
      <p>{{ $lab->defult_notes ?: 'Thank you for choosing our laboratory services. Reports can be verified online.' }}</p>
      <p>Printed by system user on {{ date('d-m-Y H:i:s') }}</p>
    </div>
  </div>
</body>
</html>
