<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Diagnostic Report Ready</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #F8FAFC; margin: 0; padding: 20px; color: #1E293B; }
    .card { max-width: 600px; margin: 0 auto; background: #FFFFFF; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #E2E8F0; }
    .header { background: linear-gradient(135deg, #065F46 0%, #047857 100%); color: #FFFFFF; padding: 28px; text-align: center; }
    .header h1 { margin: 0 0 6px 0; font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
    .content { padding: 28px; }
    .status-badge { display: inline-block; background: #D1FAE5; color: #065F46; padding: 6px 14px; border-radius: 20px; font-weight: bold; font-size: 13px; margin-bottom: 16px; }
    .patient-box { background: #F8FAFC; border-radius: 8px; padding: 16px; margin-bottom: 20px; border: 1px solid #E2E8F0; }
    .btn { display: inline-block; background: #059669; color: #FFFFFF !important; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: 700; font-size: 15px; }
    .footer { text-align: center; padding: 20px; font-size: 12px; color: #94A3B8; border-top: 1px solid #F1F5F9; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <h1>{{ $investigation->lab->name ?? 'RBJ Diagnostics & Research Center' }}</h1>
      <p style="margin: 0; opacity: 0.9; font-size: 13px;">Official Diagnostic Pathology Report</p>
    </div>
    <div class="content">
      <div style="text-align: center;">
        <span class="status-badge">&#10004; Verified, Signed & Digitally Authenticated</span>
      </div>

      <p style="font-size: 15px; margin-top: 10px;">Dear <strong>{{ $investigation->patient->name ?? 'Patient' }}</strong>,</p>
      <p>We are pleased to inform you that your laboratory investigation report is finalized, digitally signed by our pathologist, and attached to this email as a PDF document.</p>

      <div class="patient-box">
        <table style="width: 100%; font-size: 13px;">
          <tr>
            <td style="padding: 4px 0; color: #64748B; width: 35%;">Patient UID:</td>
            <td style="padding: 4px 0; font-weight: bold; color: #0F172A;">{{ $investigation->patient->unique_id ?? 'N/A' }}</td>
          </tr>
          <tr>
            <td style="padding: 4px 0; color: #64748B;">Order Reference:</td>
            <td style="padding: 4px 0; color: #0F172A;">INV-{{ $investigation->id }}</td>
          </tr>
          <tr>
            <td style="padding: 4px 0; color: #64748B;">Completed Tests:</td>
            <td style="padding: 4px 0; font-weight: 600; color: #059669;">
              {{ $investigation->investigationTests->pluck('diagnosticstest.name')->filter()->join(', ') }}
            </td>
          </tr>
        </table>
      </div>

      <div style="text-align: center; margin: 30px 0 16px 0;">
        <a href="{{ route('print.report', ['id' => $investigation->id]) }}" class="btn">Download Official PDF Report</a>
      </div>
      <p style="text-align: center; font-size: 12px; color: #64748B;">
        The report contains a dynamic QR security code for online tamper-proof validation.
      </p>
    </div>
    <div class="footer">
      <p style="margin: 0 0 4px 0;">{{ $investigation->lab->address ?? 'Diagnostic Laboratory Services' }}</p>
      <p style="margin: 0;">Phone: {{ $investigation->lab->phone ?? 'N/A' }} | Email: {{ $investigation->lab->email ?? 'lab@rbjlis.com' }}</p>
    </div>
  </div>
</body>
</html>
