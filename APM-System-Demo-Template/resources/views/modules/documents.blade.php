@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h2 class="mb-1">Documents</h2>
            <p class="text-muted mb-0">Browse JO attachments by folder.</p>
        </div>
        <form method="GET" class="d-flex gap-2 js-live-search">
            <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search client, consignee, JO no." data-live-search>
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
    </div>

    <div class="eh-card p-3">
        @if($attachments->isEmpty())
            <div class="text-center text-muted py-4">No documents uploaded yet.</div>
        @else
            <div class="accordion" id="joDocuments">
                @foreach($attachments as $jobOrderId => $files)
                    @php
                        $jobOrder = $files->first()->jobOrder;
                        $joNumber = trim(($jobOrder->code ?? '') . ' ' . ($jobOrder->mo ?? '') . ' ' . ($jobOrder->number ?? ''));
                        $folderLabel = $joNumber !== '' ? $joNumber : ('JO #' . $jobOrderId);
                        $consignee = $jobOrder?->consignee ?? 'No consignee';
                    @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-{{ $jobOrderId }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $jobOrderId }}" aria-expanded="false" aria-controls="collapse-{{ $jobOrderId }}">
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $folderLabel }}</span>
                                    <span class="text-muted small">{{ $consignee }} · {{ $files->count() }} file(s)</span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse-{{ $jobOrderId }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $jobOrderId }}" data-bs-parent="#joDocuments">
                            <div class="accordion-body">
                                <div class="list-group">
                                    @foreach($files as $attachment)
                                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ asset('storage/' . $attachment->path) }}" target="_blank" rel="noopener">
                                            <span>{{ $attachment->filename }}</span>
                                            <span class="text-muted small">{{ strtoupper(pathinfo($attachment->filename, PATHINFO_EXTENSION)) }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.querySelector('.js-live-search');
        if (!form) {
            return;
        }
        const input = form.querySelector('[data-live-search]');
        if (!input) {
            return;
        }
        let timer;
        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(() => form.requestSubmit(), 400);
        });
    })();
</script>
@endpush
