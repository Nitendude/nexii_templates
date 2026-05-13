@extends('layouts.employeehub')

@section('content')
    @php
        $groupedNotes = $notes->getCollection()->groupBy(function ($note) {
            return $note->job_order_id ?: 'no-jo';
        });
    @endphp
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">Debit/Credit Note Documents</h2>
            <p class="text-muted mb-0">View existing Debit/Credit Notes.</p>
        </div>
        <a class="btn btn-outline-primary" href="{{ route('billing.notes') }}">Back to Debit/Credit Note</a>
    </div>

    <div class="eh-card p-3 mb-3">
        <form method="GET" class="d-flex gap-2">
            <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search consignee, JO no., code">
            @if(!empty($jobOrderId))
                <input type="hidden" name="job_order_id" value="{{ $jobOrderId }}">
            @endif
            <button class="btn btn-outline-primary" type="submit">Search</button>
            @if(!empty($jobOrderId))
                <a class="btn btn-outline-secondary" href="{{ route('billing.notes.documents') }}">Show All</a>
            @endif
        </form>
    </div>

    @if(!empty($jobOrderId))
        <div class="alert alert-info">
            Showing notes for selected JO only.
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
                        <th>Net Amount</th>
                        <th>Latest Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedNotes as $groupKey => $group)
                        @php
                            $latest = $group->first();
                            $collapseId = 'dcn-jo-docs-' . md5((string) $groupKey);
                        @endphp
                        <tr>
                            <td>{{ $latest->jobOrder?->number ?? '-' }}</td>
                            <td>{{ $latest->jobOrder?->code ?? '-' }}</td>
                            <td>{{ $latest->jobOrder?->consignee ?? '-' }}</td>
                            <td class="text-center fw-semibold">{{ $group->count() }}</td>
                            <td>{{ $latest->createdBy?->name ?? '-' }}</td>
                            <td>PHP {{ number_format((float) $latest->amount, 2) }}</td>
                            <td>{{ optional($latest->note_date)->format('M d, Y') ?? '-' }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    @if($group->count() > 1)
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                            View Existing
                                        </button>
                                    @else
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('billing.notes.show', $latest) }}">
                                            Open
                                        </a>
                                    @endif
                                    <a class="btn btn-sm btn-outline-warning" href="{{ route('billing.notes.edit', $latest) }}">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @if($group->count() > 1)
                            <tr class="collapse-row">
                                <td colspan="8" class="p-0 border-0">
                                    <div class="collapse" id="{{ $collapseId }}">
                                        <div class="p-3 border-top border-bottom bg-light-subtle">
                                            <div class="table-responsive">
                                                <table class="table table-sm align-middle mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Created By</th>
                                                            <th>Net Amount</th>
                                                            <th>Date</th>
                                                            <th class="text-end">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($group as $doc)
                                                            <tr>
                                                                <td>{{ $doc->createdBy?->name ?? '-' }}</td>
                                                                <td>PHP {{ number_format((float) $doc->amount, 2) }}</td>
                                                                <td>{{ optional($doc->note_date)->format('M d, Y') ?? '-' }}</td>
                                                                <td class="text-end">
                                                                    <div class="d-flex justify-content-end gap-2">
                                                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('billing.notes.show', $doc) }}">
                                                                            Open
                                                                        </a>
                                                                        <a class="btn btn-sm btn-outline-warning" href="{{ route('billing.notes.edit', $doc) }}">
                                                                            Edit
                                                                        </a>
                                                                    </div>
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
                            <td colspan="8" class="text-center text-muted py-3">No debit/credit notes yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $notes->links() }}
        </div>
    </div>
@endsection
