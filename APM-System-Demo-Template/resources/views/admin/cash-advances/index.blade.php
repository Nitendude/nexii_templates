@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">Approve Cash Advance</h2>
            <p class="text-muted mb-0">New requests and approved cash advances are shown separately to avoid confusion.</p>
        </div>
        <form method="GET" action="{{ route('accounting.cash-advances.index') }}" class="d-flex gap-2 align-items-center">
            <label class="form-label mb-0">Employee</label>
            <select class="form-select" name="user_id" onchange="this.form.submit()">
                <option value="">All</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(($userId ?? '') == $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('status') === 'cash-advance-updated')
        <div class="alert alert-success">Cash advance request updated.</div>
    @endif
    @if(session('status') === 'cash-advance-created')
        <div class="alert alert-success">Cash advance created for employee.</div>
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
        <h5 class="mb-3">New JO Cash Advance</h5>
        <form method="POST" action="{{ route('accounting.cash-advances.store') }}">
            @csrf
            <input type="hidden" name="ca_type" value="jo">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employee</label>
                    <select class="form-select" name="user_id" required>
                        <option value="">Select employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(old('user_id') == $employee->id)>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">CA No.</label>
                    <div class="input-group">
                        <input
                            class="form-control ca-no-input"
                            name="ca_no"
                            value="{{ old('ca_no', $nextCaNo) }}"
                            placeholder="{{ $nextCaNo }}">
                        <button
                            class="btn btn-outline-secondary use-next-ca-no"
                            type="button"
                            data-next-number="{{ $nextCaNo }}">
                            Use Next
                        </button>
                    </div>
                    <div class="form-text">You can type a past CA number, or use the suggested next number.</div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-between mt-3 mb-2">
                <label class="form-label mb-0">Entries (J.O No. + Amount)</label>
                <button type="button" class="btn btn-sm btn-outline-primary" id="admin-add-ca-item">Add Entry</button>
            </div>
            <div id="admin-ca-items">
                @php
                    $oldItems = old('items', [['jo_number' => '', 'amount' => '']]);
                @endphp
                @foreach($oldItems as $index => $item)
                    <div class="row g-2 mb-2 ca-item">
                        <div class="col-md-8">
                            <select class="form-select" name="items[{{ $index }}][jo_number]" required>
                                <option value="">Select J.O No.</option>
                                @foreach($jobOrders as $jobOrder)
                                    <option value="{{ $jobOrder['value'] }}" @selected(($item['jo_number'] ?? '') === $jobOrder['value'])>{{ $jobOrder['label'] }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="items[{{ $index }}][reason]" value="">
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" min="1" class="form-control" name="items[{{ $index }}][amount]" placeholder="Amount" value="{{ $item['amount'] ?? '' }}" required>
                        </div>
                        <div class="col-md-1 d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-ca-item">&times;</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="btn btn-primary mt-2">Create JO Cash Advance</button>
        </form>
    </div>

    <div class="eh-card p-3 mb-3">
        <h5 class="mb-3">New Personal Cash Advance</h5>
        <form method="POST" action="{{ route('accounting.cash-advances.store') }}">
            @csrf
            <input type="hidden" name="ca_type" value="personal">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employee</label>
                    <select class="form-select" name="user_id" required>
                        <option value="">Select employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(old('user_id') == $employee->id)>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">CA No.</label>
                    <div class="input-group">
                        <input
                            class="form-control ca-no-input"
                            name="ca_no"
                            value="{{ old('ca_no', $nextCaNo) }}"
                            placeholder="{{ $nextCaNo }}">
                        <button
                            class="btn btn-outline-secondary use-next-ca-no"
                            type="button"
                            data-next-number="{{ $nextCaNo }}">
                            Use Next
                        </button>
                    </div>
                    <div class="form-text">You can type a past CA number, or use the suggested next number.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Salary Deduction Terms</label>
                    <input type="number" min="1" max="60" step="1" class="form-control" name="salary_deduction_terms" value="{{ old('salary_deduction_terms', 1) }}">
                    <div class="text-muted small">Example: 4 means deduct across 4 payslips.</div>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Reason</label>
                    <input class="form-control" name="items[0][reason]" placeholder="Reason for personal CA" required>
                    <input type="hidden" name="items[0][jo_number]" value="">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Amount</label>
                    <input type="number" step="0.01" min="1" class="form-control" name="items[0][amount]" placeholder="Amount" required>
                </div>
            </div>
            <button class="btn btn-primary mt-3">Create Personal Cash Advance</button>
        </form>
    </div>

    <div class="eh-card p-3 mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="mb-1">New JO CA Requests</h5>
                <div class="text-muted small">These JO cash advances have not been approved yet.</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>CA No.</th>
                        <th>Entries (J.O No. / Reason)</th>
                        <th>Total Amount</th>
                        <th>Terms</th>
                        <th>Status</th>
                        <th>Payment Proof</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingJoRequests as $request)
                        <tr>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>{{ $request->ca_no ?? '—' }}</td>
                            <td>
                                @if($request->items->isEmpty())
                                    —
                                @else
                                    <div class="small text-muted">
                                        @foreach($request->items as $item)
                                            <div>
                                                {{ $item->jo_number ?: '—' }}
                                                @if($item->reason)
                                                    <span class="text-muted">({{ $item->reason }})</span>
                                                @elseif(!$item->jo_number)
                                                    <span class="badge bg-info text-dark ms-1">Personal</span>
                                                @endif
                                                · PHP {{ number_format($item->amount, 2) }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>PHP {{ number_format($request->amount, 2) }}</td>
                            <td>{{ $request->is_personal ? ($request->salary_deduction_terms ?: 1) : '—' }}</td>
                            <td>
                                @php
                                    $displayStatus = $request->paid_at ? 'Paid' : $request->status;
                                    $statusClass = match ($displayStatus) {
                                        'Paid' => 'bg-success',
                                        'Approved' => 'bg-primary',
                                        'Rejected' => 'bg-danger',
                                        default => 'bg-warning text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $displayStatus }}</span>
                            </td>
                            <td>
                                @if($request->paid_proof_path)
                                    <a href="{{ Storage::url($request->paid_proof_path) }}" target="_blank">View</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">
                                <form class="d-inline" method="POST" action="{{ route('accounting.cash-advances.update', $request) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Approved">
                                    <button class="btn btn-sm btn-outline-success">Approve</button>
                                </form>
                                <form class="d-inline" method="POST" action="{{ route('accounting.cash-advances.update', $request) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Rejected">
                                    <button class="btn btn-sm btn-outline-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No new cash advance requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
                    {{ $pendingJoRequests->links() }}
        </div>
    </div>

    <div class="eh-card p-3 mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="mb-1">New Personal CA Requests</h5>
                <div class="text-muted small">These personal cash advances have not been approved yet.</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>CA No.</th>
                        <th>Reason</th>
                        <th>Total Amount</th>
                        <th>Terms</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPersonalRequests as $request)
                        <tr>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>{{ $request->ca_no ?? '—' }}</td>
                            <td>
                                @foreach($request->items as $item)
                                    <div class="small text-muted">{{ $item->reason ?: '—' }} · PHP {{ number_format($item->amount, 2) }}</div>
                                @endforeach
                            </td>
                            <td>PHP {{ number_format($request->amount, 2) }}</td>
                            <td>{{ $request->salary_deduction_terms ?: 1 }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $request->status }}</span></td>
                            <td class="text-end">
                                <form class="d-inline" method="POST" action="{{ route('accounting.cash-advances.update', $request) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Approved">
                                    <button class="btn btn-sm btn-outline-success">Approve</button>
                                </form>
                                <form class="d-inline" method="POST" action="{{ route('accounting.cash-advances.update', $request) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Rejected">
                                    <button class="btn btn-sm btn-outline-danger">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No new personal cash advance requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $pendingPersonalRequests->links() }}
        </div>
    </div>

    <div class="eh-card p-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="mb-1">Approved JO Cash Advances</h5>
                <div class="text-muted small">These JO cash advances were already approved by an admin.</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>CA No.</th>
                        <th>Entries (J.O No. / Reason)</th>
                        <th>Total Amount</th>
                        <th>Terms</th>
                        <th>Status</th>
                        <th>Payment Proof</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvedJoRequests as $request)
                        <tr>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>{{ $request->ca_no ?? '—' }}</td>
                            <td>
                                @if($request->items->isEmpty())
                                    —
                                @else
                                    <div class="small text-muted">
                                        @foreach($request->items as $item)
                                            <div>
                                                {{ $item->jo_number ?: '—' }}
                                                @if($item->reason)
                                                    <span class="text-muted">({{ $item->reason }})</span>
                                                @elseif(!$item->jo_number)
                                                    <span class="badge bg-info text-dark ms-1">Personal</span>
                                                @endif
                                                · PHP {{ number_format($item->amount, 2) }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>PHP {{ number_format($request->amount, 2) }}</td>
                            <td>{{ $request->is_personal ? ($request->salary_deduction_terms ?: 1) : '—' }}</td>
                            <td>
                                @php
                                    $displayStatus = $request->paid_at ? 'Paid' : 'Approved';
                                    $statusClass = $request->paid_at ? 'bg-success' : 'bg-primary';
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $displayStatus }}</span>
                            </td>
                            <td>
                                @if($request->paid_proof_path)
                                    <a href="{{ Storage::url($request->paid_proof_path) }}" target="_blank">View</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="text-muted small">Approved</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No approved cash advances yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
                    {{ $approvedJoRequests->links() }}
        </div>
    </div>

    <div class="eh-card p-3 mt-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h5 class="mb-1">Approved Personal Cash Advances</h5>
                <div class="text-muted small">These personal cash advances were already approved by an admin.</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>CA No.</th>
                        <th>Reason</th>
                        <th>Total Amount</th>
                        <th>Terms</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvedPersonalRequests as $request)
                        <tr>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>{{ $request->ca_no ?? '—' }}</td>
                            <td>
                                @foreach($request->items as $item)
                                    <div class="small text-muted">{{ $item->reason ?: '—' }} · PHP {{ number_format($item->amount, 2) }}</div>
                                @endforeach
                            </td>
                            <td>PHP {{ number_format($request->amount, 2) }}</td>
                            <td>{{ $request->salary_deduction_terms ?: 1 }}</td>
                            <td>
                                @php
                                    $displayStatus = $request->paid_at ? 'Paid' : 'Approved';
                                    $statusClass = $request->paid_at ? 'bg-success' : 'bg-primary';
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $displayStatus }}</span>
                            </td>
                            <td class="text-end">
                                <span class="text-muted small">Approved</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No approved personal cash advances yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $approvedPersonalRequests->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        document.querySelectorAll('.use-next-ca-no').forEach((button) => {
            button.addEventListener('click', function () {
                const input = button.closest('.input-group')?.querySelector('.ca-no-input');
                if (!input) {
                    return;
                }

                input.value = button.dataset.nextNumber || '';
                input.focus();
                input.select();
            });
        });
    })();

    (function () {
        const itemsWrap = document.getElementById('admin-ca-items');
        const addBtn = document.getElementById('admin-add-ca-item');
        const jobOrderOptions = @json($jobOrders ?? []);
        const escapeHtml = (value) => String(value || '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[character]));
        const jobOrderOptionsHtml = ['<option value="">Select J.O No.</option>']
            .concat(jobOrderOptions.map((jobOrder) => `<option value="${escapeHtml(jobOrder.value)}">${escapeHtml(jobOrder.label)}</option>`))
            .join('');

        if (!itemsWrap || !addBtn) {
            return;
        }

        function updateIndexes() {
            const items = itemsWrap.querySelectorAll('.ca-item');
            items.forEach((item, index) => {
                const joInput = item.querySelector('[name*="[jo_number]"]');
                const reasonInput = item.querySelector('input[name*="[reason]"]');
                const amountInput = item.querySelector('input[name*="[amount]"]');
                if (joInput) {
                    joInput.name = `items[${index}][jo_number]`;
                }
                if (reasonInput) {
                    reasonInput.name = `items[${index}][reason]`;
                }
                if (amountInput) {
                    amountInput.name = `items[${index}][amount]`;
                }
            });
        }

        function addItem() {
            const item = document.createElement('div');
            item.className = 'row g-2 mb-2 ca-item';
            item.innerHTML = `
                <div class="col-md-8">
                    <select class="form-select" name="items[][jo_number]" required>${jobOrderOptionsHtml}</select>
                    <input type="hidden" name="items[][reason]" value="">
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" min="1" class="form-control" name="items[][amount]" placeholder="Amount" required>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-ca-item">&times;</button>
                </div>
            `;
            itemsWrap.appendChild(item);
            updateIndexes();
        }

        function removeItem(target) {
            const item = target.closest('.ca-item');
            if (item) {
                item.remove();
                updateIndexes();
            }
        }

        addBtn.addEventListener('click', addItem);
        itemsWrap.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-ca-item')) {
                removeItem(event.target);
            }
        });

        updateIndexes();
    })();
</script>
@endpush
