@extends('layouts.employeehub')

@section('content')
    <style>
        .billing-search-card {
            overflow: hidden;
        }
        .billing-results-scroll {
            max-height: 65vh;
            overflow-y: auto;
        }
        .billing-results-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 1;
        }
        @media (max-width: 991.98px) {
            .billing-results-scroll {
                max-height: none;
            }
        }
    </style>
    @php
        $type = $documentType ?? 'billing_statement';
        $isService = $type === 'service_invoice';
        $createHref = $isService ? route('billing.service-invoices.create') : route('billing.create');
        $docsHref = $isService ? route('billing.service-invoices.documents') : route('billing.documents');
    @endphp
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h2 class="mb-1">{{ $isService ? 'Service Invoice' : 'Billing' }}</h2>
            <p class="text-muted mb-0">
                Find a JO before creating a {{ $isService ? 'service invoice' : 'billing statement' }}.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-primary" href="{{ $createHref }}">Create {{ $isService ? 'Service Invoice' : 'Billing Statement' }}</a>
            <a class="btn btn-outline-primary" href="{{ $docsHref }}">{{ $isService ? 'SI Documents' : 'Billing Documents' }}</a>
            <a class="btn btn-outline-primary" href="{{ route('billing.storage') }}">Master Billing Storage</a>
            @if(auth()->user()?->hasAccess('admin-container-deposits'))
                <a class="btn btn-outline-success" href="{{ route('accounting.container-deposits') }}">Container Deposits</a>
            @endif
            @if(!$isService)
                <a class="btn btn-outline-secondary" href="{{ route('billing.notes.create') }}">Create Debit/Credit Note</a>
                <a class="btn btn-outline-secondary" href="{{ route('billing.notes.documents') }}">Debit/Credit Note Documents</a>
            @endif
        </div>
    </div>

    <div class="eh-card p-3 mb-4">
        <form method="GET" class="d-flex gap-2">
            <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search consignee, shipper, JO no.">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
    </div>

    <div class="card billing-search-card">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span class="fw-semibold">Job Orders</span>
            <span class="text-muted small">
                Showing {{ $jobOrders->firstItem() ?? 0 }}-{{ $jobOrders->lastItem() ?? 0 }} of {{ $jobOrders->total() }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive billing-results-scroll">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>JO No.</th>
                            <th>Code</th>
                            <th>Consignee</th>
                            <th>Shipper</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobOrders as $jobOrder)
                            <tr class="billing-jo-row" style="cursor:pointer;" data-href="{{ $isService ? route('billing.service-invoices.create', ['job_order_id' => $jobOrder->id]) : route('billing.create', ['job_order_id' => $jobOrder->id]) }}">
                                <td>{{ $jobOrder->number ?? '-' }}</td>
                                <td>{{ $jobOrder->code ?? '-' }}</td>
                                <td>{{ $jobOrder->consignee ?? '-' }}</td>
                                <td>{{ $jobOrder->shipper ?? '-' }}</td>
                                <td>
                                    @if($jobOrder->has_billing_statement)
                                        <span class="badge text-bg-success me-1">Billing Statement</span>
                                    @else
                                        <span class="badge text-bg-secondary me-1">No Billing Statement</span>
                                    @endif

                                    @if($jobOrder->has_service_invoice)
                                        <span class="badge text-bg-info">Service Invoice</span>
                                    @else
                                        <span class="badge text-bg-secondary">No Service Invoice</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="{{ $isService ? route('billing.service-invoices.create', ['job_order_id' => $jobOrder->id]) : route('billing.create', ['job_order_id' => $jobOrder->id]) }}"
                                       onclick="event.stopPropagation();">
                                        @if($isService)
                                            {{ $jobOrder->has_service_invoice ? 'Open Service Invoice' : 'Create Service Invoice' }}
                                        @else
                                            {{ $jobOrder->has_billing_statement ? 'Open Billing Statement' : 'Create Billing Statement' }}
                                        @endif
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No job orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $jobOrders->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.billing-jo-row').forEach((row) => {
            row.addEventListener('click', () => {
                const href = row.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                }
            });
        });
    });
</script>
@endpush
