@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h2 class="mb-1">Master Billing Storage</h2>
            <p class="text-muted mb-0">One page for all Billing Statements, Service Invoices, and Debit/Credit Notes.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if(auth()->user()?->hasAccess('admin-container-deposits'))
                <a class="btn btn-outline-success" href="{{ route('accounting.container-deposits') }}">Container Deposits</a>
            @endif
            <a class="btn btn-outline-secondary" href="{{ route('billing') }}">Back to Billing</a>
        </div>
    </div>

    <div class="eh-card p-3 mb-3">
        <form method="GET" class="d-flex gap-2">
            <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search JO no., code, consignee, shipper">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            @if(!empty($search))
                <a class="btn btn-outline-secondary" href="{{ route('billing.storage') }}">Reset</a>
            @endif
        </form>
    </div>

    <div class="eh-card p-3">
        <div class="accordion" id="billingStorageAccordion">
            @forelse($groups as $index => $group)
                @php
                    $jo = $group['job_order'];
                    $headerId = 'storage-heading-' . $index;
                    $collapseId = 'storage-collapse-' . $index;
                @endphp
                <div class="accordion-item mb-2 border rounded">
                    <h2 class="accordion-header" id="{{ $headerId }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                            <div class="d-flex flex-wrap w-100 gap-3 align-items-center">
                                <span class="fw-semibold">JO: {{ $jo?->number ?? '-' }}</span>
                                <span class="text-muted">Code: {{ $jo?->code ?? '-' }}</span>
                                <span class="text-muted">Consignee: {{ $jo?->consignee ?? '-' }}</span>
                                <span class="badge text-bg-success">BS {{ $group['billing_statements']->count() }}</span>
                                <span class="badge text-bg-info">SI {{ $group['service_invoices']->count() }}</span>
                                <span class="badge text-bg-secondary">DCN {{ $group['debit_credit_notes']->count() }}</span>
                            </div>
                        </button>
                    </h2>
                    <div id="{{ $collapseId }}" class="accordion-collapse collapse" aria-labelledby="{{ $headerId }}" data-bs-parent="#billingStorageAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <div class="border rounded p-2 h-100">
                                        <div class="fw-semibold mb-2">Billing Statements</div>
                                        <div class="list-group list-group-flush">
                                            @forelse($group['billing_statements'] as $doc)
                                                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('billing.show', $doc) }}">
                                                    <span>{{ optional($doc->created_at)->format('M d, Y h:i A') }}</span>
                                                    <span class="small text-muted">{{ $doc->createdBy?->name ?? '-' }}</span>
                                                </a>
                                            @empty
                                                <div class="text-muted small px-2 py-1">No Billing Statements</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="border rounded p-2 h-100">
                                        <div class="fw-semibold mb-2">Service Invoices</div>
                                        <div class="list-group list-group-flush">
                                            @forelse($group['service_invoices'] as $doc)
                                                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('billing.service-invoices.show', $doc) }}">
                                                    <span>{{ optional($doc->created_at)->format('M d, Y h:i A') }}</span>
                                                    <span class="small text-muted">{{ $doc->createdBy?->name ?? '-' }}</span>
                                                </a>
                                            @empty
                                                <div class="text-muted small px-2 py-1">No Service Invoices</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="border rounded p-2 h-100">
                                        <div class="fw-semibold mb-2">Debit/Credit Notes</div>
                                        <div class="list-group list-group-flush">
                                            @forelse($group['debit_credit_notes'] as $doc)
                                                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('billing.notes.show', $doc) }}">
                                                    <span>{{ optional($doc->note_date)->format('M d, Y') ?? optional($doc->created_at)->format('M d, Y') }}</span>
                                                    <span class="small text-muted">{{ $doc->createdBy?->name ?? '-' }}</span>
                                                </a>
                                            @empty
                                                <div class="text-muted small px-2 py-1">No Debit/Credit Notes</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">No billing documents found.</div>
            @endforelse
        </div>
    </div>
@endsection
