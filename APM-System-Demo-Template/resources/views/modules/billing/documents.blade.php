@extends('layouts.employeehub')

@section('content')
    @php
        $type = $documentType ?? 'billing_statement';
        $isService = $type === 'service_invoice';
        $backHref = $isService ? route('billing.service-invoices') : route('billing');
        $groupedStatements = $statements->getCollection()->groupBy(function ($statement) {
            return $statement->job_order_id ?: 'no-jo';
        });
    @endphp
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">{{ $isService ? 'Service Invoice Documents' : 'Billing Documents' }}</h2>
            <p class="text-muted mb-0">View existing {{ $isService ? 'service invoices' : 'billing statements' }}.</p>
        </div>
        <a class="btn btn-outline-primary" href="{{ $backHref }}">Back to {{ $isService ? 'Service Invoice' : 'Billing' }}</a>
    </div>

    @if(!empty($jobOrderId))
        <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2">
            <span>Showing documents for selected JO only.</span>
            <a class="btn btn-sm btn-outline-secondary" href="{{ $isService ? route('billing.service-invoices.documents') : route('billing.documents') }}">Show All</a>
        </div>
    @endif

    <div class="eh-card p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>JO No.</th>
                        <th>Code</th>
                        <th>Consignee</th>
                        <th class="text-center">Documents</th>
                        <th>Created By</th>
                        <th>Latest Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedStatements as $groupKey => $group)
                        @php
                            $latest = $group->first();
                            $collapseId = 'jo-docs-' . md5((string) $groupKey);
                        @endphp
                        <tr>
                            <td>{{ $latest->jobOrder?->number ?? '-' }}</td>
                            <td>{{ $latest->jobOrder?->code ?? '-' }}</td>
                            <td>{{ $latest->jobOrder?->consignee ?? '-' }}</td>
                            <td class="text-center fw-semibold">{{ $group->count() }}</td>
                            <td>{{ $latest->createdBy?->name ?? '-' }}</td>
                            <td>{{ optional($latest->created_at)->format('M d, Y') ?? '-' }}</td>
                            <td class="text-end">
                                @if($group->count() > 1)
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                        View Existing
                                    </button>
                                @else
                                    <a class="btn btn-sm btn-outline-primary" href="{{ $isService ? route('billing.service-invoices.show', $latest) : route('billing.show', $latest) }}">
                                        Open
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @if($group->count() > 1)
                            <tr class="collapse-row">
                                <td colspan="7" class="p-0 border-0">
                                    <div class="collapse" id="{{ $collapseId }}">
                                        <div class="p-3 border-top border-bottom bg-light-subtle">
                                            <div class="table-responsive">
                                                <table class="table table-sm align-middle mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Created By</th>
                                                            <th>Date & Time</th>
                                                            <th class="text-end">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($group as $doc)
                                                            <tr>
                                                                <td>{{ $doc->createdBy?->name ?? '-' }}</td>
                                                                <td>{{ optional($doc->created_at)->format('M d, Y h:i A') ?? '-' }}</td>
                                                                <td class="text-end">
                                                                    <a class="btn btn-sm btn-outline-primary" href="{{ $isService ? route('billing.service-invoices.show', $doc) : route('billing.show', $doc) }}">
                                                                        Open
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                {{ $isService ? 'No service invoices yet.' : 'No billing statements yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $statements->links() }}
        </div>
    </div>
@endsection
