<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'DejaVu Sans',Arial,sans-serif; font-size:11px; color:#1a1a1a; padding:30px; }

    /* Report Header (Req 12 AC4) */
    .report-header { border-bottom:3px double #1e3a8a; padding-bottom:12px; margin-bottom:16px; }
    .report-header .republic { font-size:10px; color:#555; }
    .report-header h1 { font-size:18px; font-weight:bold; color:#1e3a8a; margin:4px 0 2px; }
    .report-header .subtitle { font-size:12px; color:#334155; }

    .meta-grid { display:table; width:100%; margin-bottom:12px; border:1px solid #e2e8f0; border-radius:4px; }
    .meta-cell { display:table-cell; padding:8px 12px; border-right:1px solid #e2e8f0; vertical-align:top; }
    .meta-cell:last-child { border-right:none; }
    .meta-label { font-size:9px; font-weight:bold; text-transform:uppercase; color:#64748b; letter-spacing:.05em; }
    .meta-value { font-size:11px; color:#0f172a; margin-top:2px; }

    /* Table */
    table { width:100%; border-collapse:collapse; margin-top:8px; }
    thead tr th { background:#1e3a8a; color:#fff; padding:7px 8px; text-align:left; font-size:10px; }
    tbody tr td { padding:6px 8px; border-bottom:1px solid #f1f5f9; font-size:10px; }
    tbody tr:nth-child(even) td { background:#f8fafc; }

    .footer { text-align:center; margin-top:20px; font-size:9px; color:#94a3b8; border-top:1px solid #e2e8f0; padding-top:8px; }
</style>
</head>
<body>

<div class="report-header">
    <div class="republic">Republic of the Philippines — Barangay Management Information System</div>
    <h1>{{ $title }}</h1>
    <div class="subtitle">Official Barangay Report</div>
</div>

{{-- Report Header: type, timestamp, count, filters (Req 12 AC4) --}}
<div class="meta-grid">
    <div class="meta-cell">
        <div class="meta-label">Report Type</div>
        <div class="meta-value">{{ $title }}</div>
    </div>
    <div class="meta-cell">
        <div class="meta-label">Generated At</div>
        <div class="meta-value">{{ $generatedAt->format('F d, Y h:i A') }}</div>
    </div>
    <div class="meta-cell">
        <div class="meta-label">Total Records</div>
        <div class="meta-value">{{ $totalCount }}</div>
    </div>
    <div class="meta-cell">
        <div class="meta-label">Filters Applied</div>
        <div class="meta-value">{{ is_string($filters) ? $filters : 'No filters applied' }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            @foreach($headings as $heading)
                <th>{{ $heading }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                @foreach($keys as $key)
                    <td>{{ data_get($row, $key, '') }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Barangay Management Information System (BMIS) — Generated {{ $generatedAt->toIso8601String() }}
</div>

</body>
</html>
