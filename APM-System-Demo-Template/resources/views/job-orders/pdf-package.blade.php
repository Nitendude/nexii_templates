@php
    $joNumber = trim(implode(' ', array_filter([$jobOrder->code, $jobOrder->mo, $jobOrder->number]))) ?: ($jobOrder->number ?? $jobOrder->id);
    $fmtMoney = fn ($amount) => number_format((float) $amount, 2);
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 34px; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.35;
        }
        h1, h2, h3 {
            margin: 0;
            color: #111827;
        }
        h1 {
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        h2 {
            font-size: 15px;
            margin-top: 22px;
            padding-bottom: 5px;
            border-bottom: 2px solid #2563eb;
        }
        h3 {
            font-size: 12px;
            margin: 10px 0 5px;
        }
        .muted { color: #6b7280; }
        .header {
            border-bottom: 3px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .grid th,
        .grid td {
            border: 1px solid #d1d5db;
            padding: 6px 7px;
            vertical-align: top;
        }
        .grid th {
            background: #eff6ff;
            color: #1e3a8a;
            text-align: left;
            width: 24%;
        }
        .compact th,
        .compact td {
            padding: 5px 6px;
        }
        .pill {
            display: inline-block;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            border-radius: 999px;
            padding: 2px 8px;
            font-weight: 700;
        }
        .page-break {
            page-break-before: always;
        }
        .attachment-image {
            max-width: 100%;
            max-height: 8.8in;
            border: 1px solid #d1d5db;
            margin-top: 8px;
        }
        .note-box {
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            padding: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>APM JO Complete Package</h1>
        <div class="muted">Generated {{ now()->format('F d, Y h:i A') }}</div>
    </div>

    <h2>Job Order</h2>
    <table class="grid">
        <tr>
            <th>JO No.</th>
            <td>{{ $joNumber }}</td>
            <th>JO Date</th>
            <td>{{ optional($jobOrder->jo_date)->format('F d, Y') ?? '-' }}</td>
        </tr>
        <tr>
            <th>Consignee</th>
            <td>{{ $jobOrder->consignee ?? '-' }}</td>
            <th>Shipper</th>
            <td>{{ $jobOrder->shipper ?? '-' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $jobOrder->status ?? '-' }}</td>
            <th>Port</th>
            <td>{{ $jobOrder->port ?? '-' }}</td>
        </tr>
        <tr>
            <th>Invoice No.</th>
            <td>{{ $jobOrder->invoice_no ?? '-' }}</td>
            <th>B/L or AWB No.</th>
            <td>{{ $jobOrder->bl_awb_no ?? '-' }}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td colspan="3">{{ $jobOrder->description ?? '-' }}</td>
        </tr>
    </table>

    <h2>Included Documents</h2>
    <table class="grid compact">
        <tr>
            <th>Document Type</th>
            <th>Count</th>
            <th>Included In This PDF</th>
        </tr>
        <tr>
            <td>JO Attachments</td>
            <td>{{ $jobOrder->attachments->count() }}</td>
            <td>Images are shown in this summary. PDF attachments are appended after this summary.</td>
        </tr>
        <tr>
            <td>Billing Statements</td>
            <td>{{ $jobOrder->billingStatements->count() }}</td>
            <td>Each Billing Statement PDF package is appended, including its uploaded attachments.</td>
        </tr>
        <tr>
            <td>Service Invoices</td>
            <td>{{ $jobOrder->serviceInvoices->count() }}</td>
            <td>Each Service Invoice PDF package is appended, including its uploaded attachments.</td>
        </tr>
        <tr>
            <td>Debit/Credit Notes</td>
            <td>{{ $jobOrder->debitCreditNotes->count() }}</td>
            <td>Full note details are included below. PDF attachments are appended after the billing documents.</td>
        </tr>
    </table>

    <h2>JO Attachments</h2>
    @if($jobOrder->attachments->isNotEmpty())
        <table class="grid compact">
            <tr>
                <th>Filename</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
            @foreach($jobOrder->attachments as $attachment)
                <tr>
                    <td>{{ $attachment->filename }}</td>
                    <td>{{ $attachment->mime_type ?? '-' }}</td>
                    <td>
                        @if($attachment->mime_type === 'application/pdf')
                            Appended after JO summary
                        @elseif(in_array($attachment->mime_type, ['image/jpeg', 'image/png'], true))
                            Shown in JO image attachments section
                        @else
                            Listed only because this file type cannot be embedded safely in PDF
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="muted">No JO attachments uploaded.</p>
    @endif

    <h2>Billing Statements</h2>
    @if($jobOrder->billingStatements->isNotEmpty())
        <table class="grid compact">
            <tr>
                <th>Statement No.</th>
                <th>Created</th>
                <th>Billing Attachments</th>
            </tr>
            @foreach($jobOrder->billingStatements as $statement)
                <tr>
                    <td>{{ $statement->statement_no ?? $statement->id }}</td>
                    <td>{{ optional($statement->created_at)->format('F d, Y') ?? '-' }}</td>
                    <td>{{ $statement->attachments->count() }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="muted">No Billing Statements found for this JO.</p>
    @endif

    <h2>Service Invoices</h2>
    @if($jobOrder->serviceInvoices->isNotEmpty())
        <table class="grid compact">
            <tr>
                <th>Service Invoice No.</th>
                <th>Created</th>
                <th>Billing Attachments</th>
            </tr>
            @foreach($jobOrder->serviceInvoices as $invoice)
                <tr>
                    <td>{{ $invoice->statement_no ?? $invoice->id }}</td>
                    <td>{{ optional($invoice->created_at)->format('F d, Y') ?? '-' }}</td>
                    <td>{{ $invoice->attachments->count() }}</td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="muted">No Service Invoices found for this JO.</p>
    @endif

    <h2>Debit/Credit Notes</h2>
    @forelse($jobOrder->debitCreditNotes as $note)
        @php
            $data = $note->data ?? [];
            $rows = collect($data['rows'] ?? [])->values();
        @endphp
        <div class="note-box">
            <h3>{{ ucfirst($note->note_type ?? 'Debit/Credit') }} Note #{{ $note->note_no ?? $note->id }}</h3>
            <div>Date: {{ optional($note->note_date)->format('F d, Y') ?? ($data['note_date'] ?? '-') }}</div>
            <div>Amount: {{ $fmtMoney($note->amount ?? ($data['net_total'] ?? 0)) }}</div>
            <div>Description: {{ $note->description ?? ($data['description'] ?? '-') }}</div>
            <div>Attachments: {{ $note->attachments->count() }}</div>
            @if(!empty($note->remarks) || !empty($data['remarks']))
                <div>Remarks: {{ $note->remarks ?? $data['remarks'] }}</div>
            @endif
        </div>
        @if($rows->isNotEmpty())
            <table class="grid compact">
                <tr>
                    <th>Description</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
                @foreach($rows as $row)
                    <tr>
                        <td>{{ $row['description'] ?? '-' }}</td>
                        <td>{{ ($row['side'] ?? '') === 'debit' ? $fmtMoney($row['amount'] ?? 0) : '-' }}</td>
                        <td>{{ ($row['side'] ?? '') === 'credit' ? $fmtMoney($row['amount'] ?? 0) : '-' }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    @empty
        <p class="muted">No Debit/Credit Notes found for this JO.</p>
    @endforelse

    @foreach($joImageAttachments as $attachment)
        <div class="page-break">
            <h2>JO Image Attachment</h2>
            <div class="pill">{{ $attachment['filename'] }}</div>
            <img class="attachment-image" src="{{ $attachment['data_uri'] }}" alt="{{ $attachment['filename'] }}">
        </div>
    @endforeach
</body>
</html>
