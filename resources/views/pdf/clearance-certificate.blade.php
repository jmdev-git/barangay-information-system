<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 13px; color: #1a1a1a; padding: 40px; }

        .header { text-align: center; border-bottom: 3px double #1e3a8a; padding-bottom: 16px; margin-bottom: 20px; }
        .header .republic { font-size: 11px; color: #555; }
        .header h1 { font-size: 22px; font-weight: bold; color: #1e3a8a; letter-spacing: 1px; margin: 4px 0; }
        .header .barangay { font-size: 14px; color: #334155; }
        .header .office { font-size: 12px; color: #64748b; margin-top: 2px; }

        .cert-title { text-align: center; margin: 24px 0 20px; }
        .cert-title h2 { font-size: 20px; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; color: #1e3a8a; }
        .cert-title .underline { width: 200px; height: 2px; background: #1e3a8a; margin: 6px auto 0; }

        .body-text { line-height: 1.9; text-align: justify; margin-bottom: 16px; font-size: 13.5px; }
        .body-text .name { font-weight: bold; text-transform: uppercase; font-size: 15px; color: #0f172a; }
        .body-text .underlined { border-bottom: 1px solid #0f172a; padding-bottom: 1px; }

        .info-table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 13px; }
        .info-table td { padding: 6px 10px; }
        .info-table td:first-child { font-weight: bold; color: #334155; width: 35%; }
        .info-table tr:nth-child(odd) td { background: #f8fafc; }

        .purpose-box { border-left: 4px solid #1e3a8a; background: #f0f6ff; padding: 10px 14px; margin: 16px 0; font-style: italic; }

        .certify-text { margin-top: 20px; font-size: 13px; line-height: 1.8; }

        .signature-section { margin-top: 50px; display: table; width: 100%; }
        .sig-left { display: table-cell; width: 50%; text-align: center; vertical-align: bottom; }
        .sig-right { display: table-cell; width: 50%; text-align: center; vertical-align: bottom; }
        .sig-line { border-top: 1.5px solid #0f172a; margin: 0 30px; padding-top: 6px; font-weight: bold; font-size: 13px; }
        .sig-role { font-size: 11px; color: #64748b; margin-top: 3px; }

        .footer { text-align: center; margin-top: 40px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #94a3b8; }
        .doc-control { text-align: right; font-size: 10px; color: #94a3b8; margin-top: 10px; }

        .validity-box { border: 1px solid #d1d5db; border-radius: 6px; padding: 10px 14px; margin-top: 20px; font-size: 12px; color: #475569; text-align: center; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div class="republic">Republic of the Philippines</div>
        <h1>BARANGAY MANAGEMENT INFORMATION SYSTEM</h1>
        <div class="barangay">Barangay {{ $resident->household?->barangay ?? 'Centro' }}</div>
        <div class="office">Office of the Barangay Captain</div>
    </div>

    {{-- Certificate Title --}}
    <div class="cert-title">
        <h2>Barangay Clearance</h2>
        <div class="underline"></div>
    </div>

    {{-- Body --}}
    <div class="body-text">
        <p>TO WHOM IT MAY CONCERN:</p>
        <br>
        <p>
            This is to certify that <span class="name">{{ $resident->full_name }}</span>,
            of legal age, a <span class="underlined">{{ ucfirst($resident->gender ?? 'resident') }}</span>,
            and a bona fide resident of
            <span class="underlined">{{ $resident->address ?? $resident->household?->address ?? 'this barangay' }}</span>
            @if($resident->household?->purok)
                , <span class="underlined">Purok {{ $resident->household->purok }}</span>
            @endif
            is personally known to this office and is of good moral character and has no derogatory record on file
            in this barangay.
        </p>
    </div>

    {{-- Resident info --}}
    <table class="info-table">
        <tr><td>Date of Birth</td><td>{{ $resident->birth_date?->format('F d, Y') ?? 'N/A' }}</td></tr>
        <tr><td>Age</td><td>{{ $resident->age ?? 'N/A' }} years old</td></tr>
        <tr><td>Civil Status</td><td>{{ ucfirst($resident->civil_status ?? 'N/A') }}</td></tr>
        <tr><td>Address</td><td>{{ $resident->address ?? 'N/A' }}</td></tr>
        <tr><td>Contact Number</td><td>{{ $resident->contact_number }}</td></tr>
    </table>

    {{-- Purpose --}}
    <div class="body-text">
        <strong>Purpose:</strong>
        <div class="purpose-box">{{ $clearance->purpose }}</div>
    </div>

    {{-- Certifying statement --}}
    <div class="certify-text">
        <p>
            This certification is issued upon the request of the above-named individual for
            <strong>{{ $clearance->purpose }}</strong> and for whatever legal purpose it may serve.
        </p>
        <br>
        <p>
            Issued this <strong>{{ $issuedAt->format('jS') }} day of {{ $issuedAt->format('F, Y') }}</strong>
            at the Barangay Hall.
        </p>
    </div>

    {{-- Signatures --}}
    <div class="signature-section">
        <div class="sig-left">
            <br><br><br>
            <div class="sig-line">BARANGAY CAPTAIN</div>
            <div class="sig-role">Barangay {{ $resident->household?->barangay ?? 'Centro' }}</div>
        </div>
        <div class="sig-right">
            <br><br><br>
            <div class="sig-line">{{ strtoupper($resident->full_name) }}</div>
            <div class="sig-role">Requesting Party / Signature</div>
        </div>
    </div>

    {{-- Validity --}}
    <div class="validity-box">
        This clearance is valid for <strong>six (6) months</strong> from the date of issuance
        and is valid only for the purpose stated herein.
    </div>

    {{-- Footer --}}
    <div class="footer">
        Barangay Management Information System (BMIS) — Official Document
    </div>
    <div class="doc-control">
        Clearance #{{ $clearance->id }} | Generated: {{ $issuedAt->format('Y-m-d H:i:s') }}
    </div>

</body>
</html>
