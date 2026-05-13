@extends('layouts.employeehub')

@section('content')
    <style>
        @media (max-width: 767.98px) {
            .ca-mobile-card {
                border: 1px solid #e3e7ee;
                border-radius: 0.75rem;
                background: #fff;
                padding: 0.75rem;
            }
            .ca-mobile-kv {
                display: flex;
                justify-content: space-between;
                gap: 0.5rem;
                font-size: 0.9rem;
                padding: 0.18rem 0;
            }
            .ca-mobile-kv .label {
                color: #6b7280;
                font-weight: 600;
            }
            .ca-mobile-kv .value {
                text-align: right;
                word-break: break-word;
            }
        }
    </style>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <h2 class="mb-0">Cash Advance</h2>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-12">
            <div class="eh-card p-3 mb-3">
                <h5 class="mb-3">JO Cash Advances</h5>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th colspan="4" style="background:#d9ead3;">Cash Advance</th>
                                <th colspan="4" style="background:#ead1dc;">Liquidation / Petty Cash</th>
                                <th colspan="2" style="background:#cfe2f3;">For Return</th>
                                <th rowspan="2">Remarks</th>
                            </tr>
                            <tr>
                                <th style="background:#d9ead3;">Date</th>
                                <th style="background:#d9ead3;">C.A No.</th>
                                <th style="background:#d9ead3;">J.O No.</th>
                                <th style="background:#d9ead3;">Amount</th>
                                <th style="background:#ead1dc;">Date</th>
                                <th style="background:#ead1dc;">Ref No.</th>
                                <th style="background:#ead1dc;">J.O No.</th>
                                <th style="background:#ead1dc;">Amount</th>
                                <th style="background:#cfe2f3;">Difference</th>
                                <th style="background:#cfe2f3;">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($existingJoGroups as $group)
                                @foreach($group->rows as $row)
                                    <tr>
                                        <td style="background:#d9ead3;">{{ $row->ca_date?->format('M d, Y') ?? '' }}</td>
                                        <td style="background:#d9ead3;">{{ $row->ca_no ?? '' }}</td>
                                        <td style="background:#d9ead3;">{{ $row->ca_jo_or_reason ?? '' }}</td>
                                        <td style="background:#d9ead3;" class="text-end">{{ $row->ca_amount !== null ? number_format($row->ca_amount, 2) : '' }}</td>

                                        <td style="background:#ead1dc;">{{ $row->liq_date?->format('d/m/Y') ?? '' }}</td>
                                        <td style="background:#ead1dc;">{{ $row->liq_ref_no ?? '' }}</td>
                                        <td style="background:#ead1dc;">{{ $row->liq_jo_no ?? '' }}</td>
                                        <td style="background:#ead1dc;" class="text-end">{{ $row->liq_amount !== null ? number_format($row->liq_amount, 2) : '' }}</td>

                                        <td style="background:#cfe2f3;" class="text-end">{{ number_format($row->difference, 2) }}</td>
                                        <td style="background:#cfe2f3;" class="text-end fw-semibold">{{ number_format($row->balance, 2) }}</td>
                                        <td class="text-start">{{ $row->remarks ?? '' }}</td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">No approved JO cash advances yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="eh-card p-3 mb-3">
                <h5 class="mb-3">Personal Cash Advances</h5>
                <div class="d-none d-md-block table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>CA No.</th>
                                <th>Reason</th>
                                <th>Total Amount</th>
                                <th>Terms</th>
                                <th>Per Payslip</th>
                                <th>Paid Amount</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Payment Proof</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($personalCashAdvances as $request)
                                @php
                                    $personalPaidAmount = (float) ($request->personal_paid_amount ?? 0);
                                    $personalTerms = max((int) ($request->salary_deduction_terms ?: 1), 1);
                                    $personalInstallment = min(max((float) $request->amount - $personalPaidAmount, 0), round((float) $request->amount / $personalTerms, 2));
                                    $personalBalance = max((float) $request->amount - $personalPaidAmount, 0);
                                    $displayStatus = $request->paid_at ? 'Paid' : $request->status;
                                    $statusClass = match ($displayStatus) {
                                        'Paid' => 'bg-success',
                                        'Approved' => 'bg-primary',
                                        'Rejected' => 'bg-danger',
                                        default => 'bg-warning text-dark',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                                    <td>{{ $request->ca_no ?? '—' }}</td>
                                    <td>
                                        @if($request->items->isEmpty())
                                            {{ $request->purpose ?? '—' }}
                                        @else
                                            <div class="small text-muted">
                                                @foreach($request->items as $item)
                                                    <div>
                                                        {{ $item->reason ?: ($request->purpose ?? '—') }}
                                                        · PHP {{ number_format($item->amount, 2) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td>PHP {{ number_format($request->amount, 2) }}</td>
                                    <td>{{ $personalTerms }}</td>
                                    <td>PHP {{ number_format($personalInstallment, 2) }}</td>
                                    <td>PHP {{ number_format($personalPaidAmount, 2) }}</td>
                                    <td class="fw-semibold">PHP {{ number_format($personalBalance, 2) }}</td>
                                    <td><span class="badge {{ $statusClass }}">{{ $displayStatus }}</span></td>
                                    <td>
                                        @if($request->paid_proof_path)
                                            <a href="{{ Storage::url($request->paid_proof_path) }}" target="_blank">View</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No personal cash advances yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-md-none d-flex flex-column gap-2">
                    @forelse($personalCashAdvances as $request)
                        @php
                            $personalPaidAmount = (float) ($request->personal_paid_amount ?? 0);
                            $personalTerms = max((int) ($request->salary_deduction_terms ?: 1), 1);
                            $personalInstallment = min(max((float) $request->amount - $personalPaidAmount, 0), round((float) $request->amount / $personalTerms, 2));
                            $personalBalance = max((float) $request->amount - $personalPaidAmount, 0);
                            $displayStatus = $request->paid_at ? 'Paid' : $request->status;
                            $statusClass = match ($displayStatus) {
                                'Paid' => 'bg-success',
                                'Approved' => 'bg-primary',
                                'Rejected' => 'bg-danger',
                                default => 'bg-warning text-dark',
                            };
                        @endphp
                        <div class="ca-mobile-card">
                            <div class="ca-mobile-kv"><span class="label">Date</span><span class="value">{{ $request->created_at->format('M d, Y') }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">CA No.</span><span class="value">{{ $request->ca_no ?? '—' }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">Total</span><span class="value">PHP {{ number_format($request->amount, 2) }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">Terms</span><span class="value">{{ $personalTerms }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">Per Payslip</span><span class="value">PHP {{ number_format($personalInstallment, 2) }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">Paid</span><span class="value">PHP {{ number_format($personalPaidAmount, 2) }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">Balance</span><span class="value fw-semibold">PHP {{ number_format($personalBalance, 2) }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">Status</span><span class="value"><span class="badge {{ $statusClass }}">{{ $displayStatus }}</span></span></div>
                            <div class="mt-2 small text-muted">
                                @if($request->items->isEmpty())
                                    {{ $request->purpose ?? '—' }}
                                @else
                                    @foreach($request->items as $item)
                                        <div>
                                            {{ $item->reason ?: ($request->purpose ?? '—') }}
                                            · PHP {{ number_format($item->amount, 2) }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @if($request->paid_proof_path)
                                <div class="mt-2">
                                    <a href="{{ Storage::url($request->paid_proof_path) }}" target="_blank" class="small">View Payment Proof</a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">No personal cash advances yet.</div>
                    @endforelse
                </div>
            </div>
            <div class="eh-card p-3">
                <h5 class="mb-3">JO Cash Advance Requests</h5>
                <div class="d-none d-md-block table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>CA No.</th>
                                <th>Entries</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Payment Proof</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                                <tr>
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
                                                        @endif
                                                        · PHP {{ number_format($item->amount, 2) }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td>PHP {{ number_format($request->amount, 2) }}</td>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No JO cash advance requests yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-md-none d-flex flex-column gap-2">
                    @forelse($requests as $request)
                        <div class="ca-mobile-card">
                            <div class="ca-mobile-kv"><span class="label">Date</span><span class="value">{{ $request->created_at->format('M d, Y') }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">CA No.</span><span class="value">{{ $request->ca_no ?? '—' }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">Total</span><span class="value">PHP {{ number_format($request->amount, 2) }}</span></div>
                            <div class="ca-mobile-kv">
                                <span class="label">Status</span>
                                <span class="value">
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
                                </span>
                            </div>
                            <div class="mt-2 small text-muted">
                                @if($request->items->isEmpty())
                                    —
                                @else
                                    @foreach($request->items as $item)
                                        <div>
                                            {{ $item->jo_number ?: '—' }}
                                            @if($item->reason)
                                                <span class="text-muted">({{ $item->reason }})</span>
                                            @endif
                                            · PHP {{ number_format($item->amount, 2) }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            @if($request->paid_proof_path)
                                <div class="mt-2">
                                    <a href="{{ Storage::url($request->paid_proof_path) }}" target="_blank" class="small">View Payment Proof</a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">No JO cash advance requests yet.</div>
                    @endforelse
                </div>
            </div>
            <div class="eh-card p-3 mt-3">
                <h5 class="mb-3">My Liquidation / Petty Cash</h5>
                <div class="d-none d-md-block table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>CA No.</th>
                                <th>Ref No.</th>
                                <th>J.O No. / Purpose</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Receipt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $liquidations = $requests->flatMap->liquidations->sortByDesc('date')->values();
                            @endphp
                            @forelse($liquidations as $liquidation)
                                <tr>
                                    <td>{{ $liquidation->date->format('M d, Y') }}</td>
                                    <td>{{ $liquidation->cashAdvanceRequest?->ca_no ?? '—' }}</td>
                                    <td>{{ $liquidation->ref_no ?? '—' }}</td>
                                    <td>{{ $liquidation->jo_number ?? '—' }}</td>
                                    <td>PHP {{ number_format($liquidation->amount, 2) }}</td>
                                    <td>
                                        @php
                                            $liqStatus = $liquidation->status ?? 'Pending';
                                            $liqStatusClass = match ($liqStatus) {
                                                'Approved' => 'bg-success',
                                                'Rejected' => 'bg-danger',
                                                default => 'bg-warning text-dark',
                                            };
                                        @endphp
                                        <span class="badge {{ $liqStatusClass }}">{{ $liqStatus }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $receiptList = $liquidation->receipt_paths ?? [];
                                            if (empty($receiptList) && !empty($liquidation->receipt_path)) {
                                                $receiptList = [$liquidation->receipt_path];
                                            }
                                        @endphp
                                        @if(!empty($receiptList))
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#employeeReceiptsModal"
                                                data-receipts='@json($receiptList)'>
                                                View Receipts ({{ count($receiptList) }})
                                            </button>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No liquidation/petty cash records yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-md-none d-flex flex-column gap-2">
                    @php
                        $liquidations = $requests->flatMap->liquidations->sortByDesc('date')->values();
                    @endphp
                    @forelse($liquidations as $liquidation)
                        <div class="ca-mobile-card">
                            <div class="ca-mobile-kv"><span class="label">Date</span><span class="value">{{ $liquidation->date->format('M d, Y') }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">CA No.</span><span class="value">{{ $liquidation->cashAdvanceRequest?->ca_no ?? '—' }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">Ref No.</span><span class="value">{{ $liquidation->ref_no ?? '—' }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">J.O No. / Purpose</span><span class="value">{{ $liquidation->jo_number ?? '—' }}</span></div>
                            <div class="ca-mobile-kv"><span class="label">Amount</span><span class="value">PHP {{ number_format($liquidation->amount, 2) }}</span></div>
                            <div class="ca-mobile-kv">
                                <span class="label">Status</span>
                                <span class="value">
                                    @php
                                        $liqStatus = $liquidation->status ?? 'Pending';
                                        $liqStatusClass = match ($liqStatus) {
                                            'Approved' => 'bg-success',
                                            'Rejected' => 'bg-danger',
                                            default => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $liqStatusClass }}">{{ $liqStatus }}</span>
                                </span>
                            </div>
                            <div class="mt-2">
                                @php
                                    $receiptList = $liquidation->receipt_paths ?? [];
                                    if (empty($receiptList) && !empty($liquidation->receipt_path)) {
                                        $receiptList = [$liquidation->receipt_path];
                                    }
                                @endphp
                                @if(!empty($receiptList))
                                    <button type="button" class="btn btn-sm btn-outline-secondary w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#employeeReceiptsModal"
                                        data-receipts='@json($receiptList)'>
                                        View Receipts ({{ count($receiptList) }})
                                    </button>
                                @else
                                    <span class="small text-muted">No receipt uploaded.</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">No liquidation/petty cash records yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const modal = document.getElementById('employeeReceiptsModal');
        if (!modal) {
            return;
        }
        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const raw = trigger?.getAttribute('data-receipts') || '[]';
            let receipts = [];
            try {
                receipts = JSON.parse(raw);
            } catch (err) {
                receipts = [];
            }
            const list = modal.querySelector('.receipt-list');
            list.innerHTML = '';
            if (!receipts.length) {
                list.innerHTML = '<div class="text-muted">No receipts found.</div>';
                return;
            }
            const baseUrl = "{{ rtrim(Storage::url(''), '/') }}/";
            receipts.forEach((path) => {
                const item = document.createElement('div');
                const link = document.createElement('a');
                link.href = baseUrl + path;
                link.target = '_blank';
                link.textContent = path.split('/').pop();
                item.appendChild(link);
                list.appendChild(item);
            });
        });
    })();
</script>
@endpush

<div class="modal fade" id="employeeReceiptsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Receipts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="receipt-list d-flex flex-column gap-2"></div>
            </div>
        </div>
    </div>
</div>
