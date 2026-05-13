@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h4 class="mb-1">Clients</h4>
            <div class="text-muted small">Manage client master data.</div>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2 js-live-search">
                <select class="form-select" name="status">
                    <option value="">Active</option>
                    <option value="archived" @selected(($status ?? '') === 'archived')>Archived</option>
                    <option value="all" @selected(($status ?? '') === 'all')>All</option>
                </select>
                <input class="form-control" type="search" name="q" value="{{ $search ?? '' }}" placeholder="Search client or TIN" data-live-search>
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>
            <a class="btn btn-primary" href="{{ route('admin.clients.create') }}">Add Client</a>
        </div>
    </div>

    @if(session('status') === 'client-created')
        <div class="alert alert-success">Client created.</div>
    @endif
    @if(session('status') === 'client-updated')
        <div class="alert alert-success">Client updated.</div>
    @endif
    @if(session('status') === 'client-deleted')
        <div class="alert alert-success">Client archived.</div>
    @endif
    @if(session('status') === 'client-restored')
        <div class="alert alert-success">Client restored.</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>TIN</th>
                            <th>Business Style</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td class="fw-semibold">{{ $client->name }}</td>
                                <td>{{ $client->address ?? '-' }}</td>
                                <td>{{ $client->tin_number ?? '-' }}</td>
                                <td>{{ $client->business_style ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <button
                                            class="btn btn-sm btn-outline-secondary"
                                            type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#clientViewModal"
                                            data-client-name="{{ $client->name }}"
                                            data-client-address="{{ $client->address }}"
                                            data-client-tin="{{ $client->tin_number }}"
                                            data-client-business="{{ $client->business_style }}"
                                        >
                                            View
                                        </button>
                                        @if($client->trashed())
                                            <form method="POST" action="{{ route('admin.clients.restore', $client->id) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-success">Restore</button>
                                            </form>
                                        @else
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.clients.edit', $client) }}">Edit</a>
                                            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" onsubmit="return confirm('Archive this client?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Archive</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No clients yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($clients->hasPages())
            <div class="card-footer">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="clientViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Client Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <div class="text-muted small">Client Name</div>
                        <div class="fw-semibold" data-client-field="name">-</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">Address</div>
                        <div class="fw-semibold" data-client-field="address">-</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">TIN Number</div>
                        <div class="fw-semibold" data-client-field="tin">-</div>
                    </div>
                    <div class="mb-0">
                        <div class="text-muted small">Business Style</div>
                        <div class="fw-semibold" data-client-field="business">-</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
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

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('clientViewModal');
        if (!modal) {
            return;
        }
        modal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            if (!button) {
                return;
            }
            const name = button.getAttribute('data-client-name') || '-';
            const address = button.getAttribute('data-client-address') || '-';
            const tin = button.getAttribute('data-client-tin') || '-';
            const business = button.getAttribute('data-client-business') || '-';
            modal.querySelector('[data-client-field="name"]').textContent = name;
            modal.querySelector('[data-client-field="address"]').textContent = address;
            modal.querySelector('[data-client-field="tin"]').textContent = tin;
            modal.querySelector('[data-client-field="business"]').textContent = business;
        });
    })();
</script>
@endpush
