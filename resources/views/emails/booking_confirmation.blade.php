<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Diagnostic Order Confirmation</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #F8FAFC; margin: 0; padding: 20px; color: #1E293B; }
    .card { max-width: 600px; margin: 0 auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #E2E8F0; }
    .header { background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%); color: #FFFFFF; padding: 28px; text-align: center; }
    .header h1 { margin: 0 0 6px 0; font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
    .content { padding: 28px; }
    .patient-box { background: #F1F5F9; border-radius: 8px; padding: 16px; margin-bottom: 20px; }
    .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .table th { text-align: left; padding: 10px; border-bottom: 2px solid #E2E8F0; font-size: 12px; text-transform: uppercase; color: #64748B; }
    .table td { padding: 12px 10px; border-bottom: 1px solid #F1F5F9; font-size: 14px; }
    .total-box { background: #EFF6FF; border-left: 4px solid #3B82F6; padding: 14px; border-radius: 6px; margin-bottom: 24px; }
    .btn { display: inline-block; background: #2563EB; color: #FFFFFF !important; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; }
    .footer { text-align: center; padding: 20px; font-size: 12px; color: #94A3B8; border-top: 1px solid #F1F5F9; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <h1>{{ $investigation->lab->name ?? 'RBJ Diagnostics & Research Center' }}</h1>
      <p style="margin: 0; opacity: 0.85; font-size: 13px;">Diagnostic Order & Payment Acknowledgment</p>
    </div>
    <div class="content">
      <p style="font-size: 15px; margin-top: 0;">Dear <strong>{{ $investigation->patient->name ?? 'Patient' }}</strong>,</p>
      <p>Thank you for choosing {{ $investigation->lab->name ?? 'our diagnostic center' }}. Your laboratory investigation booking has been registered successfully.</p>

      <div class="patient-box">
        <table style="width: 100%; font-size: 13px;">
          <tr>
            <td style="padding: 4px 0; color: #64748B; width: 35%;">Order Reference:</td>
            <td style="padding: 4px 0; font-weight: bold; color: #0F172A;">INV-{{ $investigation->id }} (UID: {{ $investigation->patient->unique_id ?? 'N/A' }})</td>
          </tr>
          <tr>
            <td style="padding: 4px 0; color: #64748B;">Date & Time:</td>
            <td style="padding: 4px 0; color: #0F172A;">{{ \Carbon\Carbon::parse($investigation->created_on)->format('d M Y, h:i A') }}</td>
          </tr>
          <tr>
            <td style="padding: 4px 0; color: #64748B;">Referring Doctor:</td>
            <td style="padding: 4px 0; color: #0F172A;">{{ $investigation->doctor->doctor_name ?? ($investigation->refered_by ?: 'Self') }}</td>
          </tr>
        </table>
      </div>

      <h3 style="font-size: 15px; margin-bottom: 10px; color: #0F172A;">Booked Diagnostic Tests</h3>
      <table class="table">
        <thead>
          <tr>
            <th>Test Description</th>
            <th style="text-align: right;">Amount (INR)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($investigation->investigationTests as $it)
            <tr>
              <td>{{ $it->diagnosticstest->name ?? 'Diagnostic Test' }}</td>
              <td style="text-align: right; font-weight: 600;">Rs. {{ number_format($it->diagnosticstest->price ?? 0, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="total-box">
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
          <span>Total Order Value:</span>
          <strong>Rs. {{ number_format($investigation->total_amount, 2) }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 4px; color: #059669;">
          <span>Paid Amount:</span>
          <strong>Rs. {{ number_format($investigation->total_amount - $investigation->balance_amount, 2) }}</strong>
        </div>
        @if($investigation->balance_amount > 0)
          <div style="display: flex; justify-content: space-between; color: #DC2626; font-weight: bold;">
            <span>Balance Due:</span>
            <span>Rs. {{ number_format($investigation->balance_amount, 2) }}</span>
          </div>
        @endif
      </div>

      <div style="text-align: center; margin: 24px 0 10px 0;">
        <a href="{{ route('print.bill', ['id' => $investigation->id]) }}" class="btn">View & Print Receipt</a>
      </div>
    </div>
    <div class="footer">
      <p style="margin: 0 0 4px 0;">{{ $investigation->lab->address ?? 'Diagnostic Laboratory Services' }}</p>
      <p style="margin: 0;">Phone: {{ $investigation->lab->phone ?? 'N/A' }} | Email: {{ $investigation->lab->email ?? 'lab@rbjlis.com' }}</p>
    </div>
  </div>
</body>
</html>
