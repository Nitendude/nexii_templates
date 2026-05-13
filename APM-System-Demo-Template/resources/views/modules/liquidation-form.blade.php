@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <h2 class="mb-0">Liquidation Form</h2>
    </div>

    @if(session('status') === 'liquidation-form-submitted')
        <div class="alert alert-success">Liquidation form submitted for admin approval.</div>
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

    <div class="eh-card p-4 mb-3">
        <h5 class="mb-3">Submit Liquidation Form</h5>
        <form method="POST" action="{{ route('cash-advances.liquidation-form.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">J.O to Liquidate</label>
                    <select class="form-select" name="cash_advance_item_id" required>
                        <option value="">Select J.O</option>
                        @foreach($caItems as $item)
                            @php
                                $joDigits = preg_match('/(\d{3,})$/', $item->jo_number ?? '', $m) ? $m[1] : null;
                                $consignee = $joDigits ? ($jobOrderNames[$joDigits] ?? null) : null;
                            @endphp
                            <option value="{{ $item->id }}" @selected((string) old('cash_advance_item_id') === (string) $item->id)>
                                {{ $item->jo_number }} - {{ $consignee ?: 'Client N/A' }} (CA {{ $item->request?->ca_no ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">LIQ. No.</label>
                    <input class="form-control" value="{{ $nextLiquidationNo }}" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Top Remarks (Optional)</label>
                    <input class="form-control" name="remarks" value="{{ old('remarks') }}">
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50%;">Description</th>
                            <th style="width: 20%;">Amount</th>
                            <th style="width: 30%;">Remarks / Reference No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($defaultRows as $i => $label)
                            <tr>
                                <td>
                                    <input type="hidden" name="line_items[{{ $i }}][description]" value="{{ $label }}">
                                    <strong>{{ $label }}</strong>
                                </td>
                                <td>
                                    <input class="form-control text-end liq-amount" type="number" step="0.01" min="0" name="line_items[{{ $i }}][amount]" value="{{ old("line_items.$i.amount") }}">
                                </td>
                                <td>
                                    <input class="form-control" name="line_items[{{ $i }}][reference]" value="{{ old("line_items.$i.reference") }}">
                                </td>
                            </tr>
                        @endforeach
                        @for($i = count($defaultRows); $i < count($defaultRows) + 10; $i++)
                            <tr>
                                <td>
                                    <input class="form-control" name="line_items[{{ $i }}][description]" value="{{ old("line_items.$i.description") }}" placeholder="Additional description">
                                </td>
                                <td>
                                    <input class="form-control text-end liq-amount" type="number" step="0.01" min="0" name="line_items[{{ $i }}][amount]" value="{{ old("line_items.$i.amount") }}">
                                </td>
                                <td>
                                    <input class="form-control" name="line_items[{{ $i }}][reference]" value="{{ old("line_items.$i.reference") }}">
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th class="text-end">TOTAL</th>
                            <th class="text-end" id="liqFormTotal">0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mb-3">
                <label class="form-label">Receipts <span class="text-danger">*</span></label>
                <input type="file" class="form-control" name="receipts[]" multiple accept=".jpg,.jpeg,.png,.pdf" required>
                <div class="text-muted small mt-1">You can upload multiple files.</div>
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-primary">Submit Liquidation Form</button>
            </div>
        </form>
    </div>

    <div class="eh-card p-3">
        <h5 class="mb-3">My Liquidation Forms</h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Form No.</th>
                        <th>Date</th>
                        <th>J.O No.</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($forms as $form)
                        @php
                            $statusClass = match($form->status) {
                                'Approved' => 'bg-success',
                                'Rejected' => 'bg-danger',
                                default => 'bg-warning text-dark',
                            };
                        @endphp
                        <tr>
                            <td>{{ $form->liq_no ?? $form->form_no ?? ('LF-' . $form->id) }}</td>
                            <td>{{ optional($form->date)->format('M d, Y') }}</td>
                            <td>{{ $form->jo_number ?: '-' }}</td>
                            <td>{{ $form->client_name ?: '-' }}</td>
                            <td>PHP {{ number_format((float) $form->amount, 2) }}</td>
                            <td><span class="badge {{ $statusClass }}">{{ $form->status }}</span></td>
                            <td>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('cash-advances.liquid-form.show', $form) }}">View / Print</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No liquidation form submissions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-2">{{ $forms->links() }}</div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const amountInputs = document.querySelectorAll('.liq-amount');
        const totalEl = document.getElementById('liqFormTotal');

        function refreshTotal() {
            let total = 0;
            amountInputs.forEach((input) => {
                total += parseFloat(input.value || '0');
            });
            totalEl.textContent = total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        amountInputs.forEach((input) => input.addEventListener('input', refreshTotal));
        refreshTotal();
    })();
</script>
@endpush
