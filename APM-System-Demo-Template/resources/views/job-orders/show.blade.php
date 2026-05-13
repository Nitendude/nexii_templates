@extends('layouts.employeehub')

@section('content')
    @php
        $joNumber = trim(implode(' ', array_filter([$jobOrder->code, $jobOrder->mo, $jobOrder->number]))) ?: ($jobOrder->number ?? 'JO');
    @endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">JO Details</h2>
            <p class="text-muted mb-0">View job order information.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('operations.job-orders.index') }}">Back to list</a>
            <a class="btn btn-outline-success" href="{{ route('operations.job-orders.package.download', $jobOrder) }}">
                Download JO Package
            </a>
            @if($canManage)
                <a class="btn btn-primary" href="{{ route('operations.job-orders.edit', $jobOrder) }}">Edit JO</a>
            @endif
            @if($jobOrder->attachments->count() > 0)
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Attachments ({{ $jobOrder->attachments->count() }})
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach($jobOrder->attachments as $attachment)
                            <li>
                                <a class="dropdown-item" href="{{ asset('storage/' . $attachment->path) }}" target="_blank" rel="noopener">
                                    {{ $attachment->filename }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    @if(session('status') === 'job-order-updated')
        <div class="alert alert-success">Job order updated.</div>
    @endif
    @if(session('status') === 'job-order-attachments-uploaded')
        <div class="alert alert-success">Attachment uploaded.</div>
    @endif
    @if(session('status') === 'job-order-server-scan-attached')
        <div class="alert alert-success">Server scan attached to this JO.</div>
    @endif
    @if(session('status') === 'job-order-package-emailed')
        <div class="alert alert-success">Complete JO PDF package emailed successfully.</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="eh-card p-3 mb-3">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted small">JO Date</div>
                <div class="fw-semibold">{{ optional($jobOrder->jo_date)->format('M d, Y') ?? '-' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">JO No.</div>
                <div class="fw-semibold">{{ $jobOrder->code ?? '-' }} {{ $jobOrder->mo ?? '' }} {{ $jobOrder->number ?? '' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Created By</div>
                <div class="fw-semibold">{{ $jobOrder->createdBy?->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="eh-card p-3 mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
            <div class="fw-semibold">Attachments</div>
            <span class="text-muted small">{{ $jobOrder->attachments->count() }} file(s)</span>
        </div>

        @if($canManage)
            <form method="POST" action="{{ route('operations.job-orders.attachments.store', $jobOrder) }}" enctype="multipart/form-data" class="mb-3">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Upload scanned documents / attachments after saving JO</label>
                        <input class="form-control" type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.bmp,.tif,.tiff,image/*" required>
                        <div class="text-muted small mt-1">Scan the document first, then upload the saved file here. Accepted files: PDF, JPG, JPEG, PNG, WEBP, BMP, TIF, TIFF. Max 20MB each. Uploaded files are stored in APM.</div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100" type="submit">Upload Attachments</button>
                    </div>
                </div>
            </form>
            <div class="border rounded-3 bg-light p-3 mb-3">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                    <div>
                        <div class="fw-semibold">Attach Server Scan</div>
                        <div class="text-muted small">
                            Scan from the office scanner using <span class="fw-semibold">Scan to Folder</span>. It saves directly to your personal APM scan folder, then select the file here to attach it to this JO.
                        </div>
                    </div>
                    <span class="badge text-bg-light border">{{ $serverScans->count() }} ready</span>
                </div>

                @if($serverScans->isNotEmpty())
                    <form method="POST" action="{{ route('operations.job-orders.server-scans.attach', $jobOrder) }}">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">Recent scans in your personal folder</label>
                                <select class="form-select" name="scan_file" required>
                                    <option value="">Choose scanned document...</option>
                                    @foreach($serverScans as $scan)
                                        <option value="{{ $scan['filename'] }}">
                                            {{ $scan['filename'] }} - {{ number_format($scan['size'] / 1024, 1) }} KB - {{ $scan['modified_at']->format('M d, Y h:i A') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-outline-primary w-100" type="submit">Attach Selected Scan</button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="text-muted small">
                        No server scans are waiting. Configure the scanner/printer destination folder for <span class="fw-semibold">{{ auth()->user()->email }}</span> to:
                        <span class="fw-semibold d-inline-block">{{ $serverScanInboxPath }}</span>
                        <div class="mt-1">
                            After scanning, refresh this JO page and the file should appear here.
                        </div>
                    </div>
                @endif
            </div>
        @endif

        @if($jobOrder->attachments->count() > 0)
            <div class="list-group">
                @foreach($jobOrder->attachments as $attachment)
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ asset('storage/' . $attachment->path) }}" target="_blank" rel="noopener">
                        <span>{{ $attachment->filename }}</span>
                        <span class="text-muted small">{{ strtoupper(pathinfo($attachment->filename, PATHINFO_EXTENSION)) }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-muted">No attachments uploaded yet.</div>
        @endif
    </div>

    <div class="eh-card p-3 mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <div class="fw-semibold">Email Whole JO Package</div>
                <div class="text-muted small">
                    Sends one PDF attachment containing the JO, JO attachments, Billing Statements, Service Invoices, Debit/Credit Notes, and billing attachments.
                </div>
            </div>
            <span class="badge text-bg-light border">
                From: {{ auth()->user()->email }}
            </span>
        </div>
        <form method="POST" action="{{ route('operations.job-orders.package.email', $jobOrder) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Send To</label>
                    <input class="form-control" type="email" name="email_to" value="{{ old('email_to') }}" required placeholder="client@example.com">
                </div>
                <div class="col-md-4">
                    <label class="form-label">CC</label>
                    <input class="form-control" type="text" name="email_cc" value="{{ old('email_cc') }}" placeholder="email1@example.com, email2@example.com">
                    <div class="text-muted small mt-1">Separate multiple emails with commas.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <input class="form-control" name="email_subject" value="{{ old('email_subject', 'JO ' . $joNumber . ' Complete PDF Package') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Message</label>
                    <textarea class="form-control" name="email_message" rows="4">{{ old('email_message', "Good day,\n\nPlease see attached complete PDF package for JO {$joNumber}.\n\nThank you.") }}</textarea>
                    <div class="text-muted small mt-1">
                        Replies will go to {{ auth()->user()->email }}.
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-danger" type="submit">Email JO PDF Package</button>
                </div>
            </div>
        </form>
    </div>

    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="fw-semibold mb-0">JO Summary</h5>
            <span class="text-muted small">Core identifiers</span>
        </div>
        <div class="border-bottom mb-3"></div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">JO Date</div>
                <div class="fw-semibold">{{ optional($jobOrder->jo_date)->format('M d, Y') ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">JO No.</div>
                <div class="fw-semibold">{{ $jobOrder->number ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Code</div>
                <div class="fw-semibold">{{ $jobOrder->code ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Status</div>
                <div class="fw-semibold">{{ $jobOrder->status ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Costing</div>
                <div class="fw-semibold">{{ $jobOrder->costing ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Port</div>
                <div class="fw-semibold">{{ $jobOrder->port ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="fw-semibold mb-0">Parties & Shipment</h5>
            <span class="text-muted small">Client and shipment info</span>
        </div>
        <div class="border-bottom mb-3"></div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted small">Consignee</div>
                <div class="fw-semibold">{{ $jobOrder->consignee ?? '-' }}</div>
                @if($jobOrder->client?->address)
                    <div class="small text-muted mt-1">{{ $jobOrder->client->address }}</div>
                @endif
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Shipper</div>
                <div class="fw-semibold">{{ $jobOrder->shipper ?? '-' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Origin</div>
                <div class="fw-semibold">{{ $jobOrder->origin ?? '-' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Vessel / Voyage No.</div>
                <div class="fw-semibold">{{ $jobOrder->vessel_voyage_no ?? '-' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">B/L or AWB No.</div>
                <div class="fw-semibold">{{ $jobOrder->bl_awb_no ?? '-' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Shipping Lines</div>
                <div class="fw-semibold">{{ $jobOrder->shipping_lines ?? '-' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">No. of Container</div>
                <div class="fw-semibold">{{ $jobOrder->no_of_container ?? '-' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Co-loader / Forwarder</div>
                <div class="fw-semibold">{{ $jobOrder->co_loader_forwarder ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="fw-semibold mb-0">Timeline</h5>
            <span class="text-muted small">Key dates</span>
        </div>
        <div class="border-bottom mb-3"></div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">ETA</div>
                <div class="fw-semibold">{{ optional($jobOrder->eta)->format('M d, Y') ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Demurrage Date</div>
                <div class="fw-semibold">{{ optional($jobOrder->demurrage_date)->format('M d, Y') ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Detention Date</div>
                <div class="fw-semibold">{{ optional($jobOrder->detention_date)->format('M d, Y') ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Port Storage Date</div>
                <div class="fw-semibold">{{ optional($jobOrder->port_storage_date)->format('M d, Y') ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Discharge Date</div>
                <div class="fw-semibold">{{ optional($jobOrder->discharge_date)->format('M d, Y') ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Date Delivered</div>
                <div class="fw-semibold">{{ optional($jobOrder->date_delivered)->format('M d, Y') ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Date of Arrival</div>
                <div class="fw-semibold">{{ optional($jobOrder->date_of_arrival)->format('M d, Y') ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Date Refunded</div>
                <div class="fw-semibold">{{ optional($jobOrder->date_refunded)->format('M d, Y') ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="fw-semibold mb-0">Cargo</h5>
            <span class="text-muted small">Container details</span>
        </div>
        <div class="border-bottom mb-3"></div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small">Description</div>
                <div class="fw-semibold">{{ $jobOrder->description ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">No. of Packages</div>
                <div class="fw-semibold">{{ $jobOrder->no_of_packages ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Kind of Packages</div>
                <div class="fw-semibold">{{ $jobOrder->kind_of_packages ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Gross Weight</div>
                <div class="fw-semibold">{{ $jobOrder->gross_weight ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">No. of CBM</div>
                <div class="fw-semibold">{{ $jobOrder->no_of_cbm ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="fw-semibold mb-0">References</h5>
            <span class="text-muted small">Documents and notes</span>
        </div>
        <div class="border-bottom mb-3"></div>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">P.O. #</div>
                <div class="fw-semibold">{{ $jobOrder->po_number ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Invoice No.</div>
                <div class="fw-semibold">{{ $jobOrder->invoice_no ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Entry No.</div>
                <div class="fw-semibold">{{ $jobOrder->entry_no ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">CTNR Deposit</div>
                <div class="fw-semibold">{{ $jobOrder->ctnr_deposit ?? '-' }}</div>
            </div>
            <div class="col-md-9">
                <div class="text-muted small">Remarks / Location</div>
                <div class="fw-semibold">{{ $jobOrder->remarks_location ?? '-' }}</div>
            </div>
        </div>
    </div>

    @if($canEditStatus && !$canManage)
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="fw-semibold mb-0">Update Status</h5>
                <span class="text-muted small">Status update only</span>
            </div>
            <div class="border-bottom mb-3"></div>
            <form method="POST" action="{{ route('operations.job-orders.update', $jobOrder) }}">
                @csrf
                @method('PUT')
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <input class="form-control" name="status" value="{{ old('status', $jobOrder->status) }}">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary">Update Status</button>
                    </div>
                </div>
            </form>
        </div>
    @endif
@endsection
