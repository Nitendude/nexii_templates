@extends('layouts.employeehub')

@section('content')
    @php
        $note = $note ?? null;
        $noteData = is_array($note?->data) ? $note->data : [];
        $noteRows = collect($noteData['rows'] ?? []);
        $isEditing = filled($note?->id);
        $oldParticular = old('dcn_particular', $noteRows->pluck('particular')->all());
        $oldSide = old('dcn_side', $noteRows->pluck('side')->all());
        $oldAmount = old('dcn_amount', $noteRows->pluck('amount')->all());
        if (!is_array($oldParticular)) { $oldParticular = ['']; }
        if (!is_array($oldSide)) { $oldSide = ['debit']; }
        if (!is_array($oldAmount)) { $oldAmount = ['']; }
        $deductDefault = $isEditing && array_key_exists('deduct_advances', $noteData) ? $noteData['deduct_advances'] : '1';
        $deductAdvancesChecked = filter_var(old('deduct_advances', $deductDefault), FILTER_VALIDATE_BOOLEAN);
        $rowCount = max(count($oldParticular), count($oldSide), count($oldAmount), 1);
        $fixedJo = $jobOrders->first();
        $linkedClient = $client ?? ($clientsByName ?? collect())->get($fixedJo?->consignee);
        $year = $fixedJo?->jo_date ? \Illuminate\Support\Carbon::parse($fixedJo->jo_date)->format('y') : now()->format('y');
        $computedJobRefNo = $fixedJo
            ? trim(($fixedJo->code ?? '') . '-' . ($fixedJo->mo ?? '') . '-' . ($fixedJo->number ?? '') . '-' . $year, '-')
            : '';
        $fixedJoLabel = $fixedJo
            ? (($fixedJo->code ?? '-') . '-' . ($fixedJo->number ?? '-') . ' | ' . ($fixedJo->consignee ?? '-'))
            : 'No Job Order selected';
        $formAction = $isEditing ? route('billing.notes.update', $note) : route('billing.notes.store');
        $noteDateDefault = $note?->note_date?->format('Y-m-d') ?? ($noteData['note_date'] ?? now()->format('Y-m-d'));
    @endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h2 class="mb-1">{{ $isEditing ? 'Edit Debit / Credit Note' : 'Create Debit / Credit Note' }}</h2>
            <p class="text-muted mb-0">{{ $isEditing ? 'Update the saved Debit/Credit Note without changing its document number.' : 'Billing-style optional document for an existing Job Order.' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ $isEditing ? route('billing.notes.show', $note) : route('billing.notes') }}">Back</a>
            <a class="btn btn-outline-primary" href="{{ route('billing.notes.documents') }}">Debit/Credit Note Documents</a>
        </div>
    </div>

    @if(session('status') === 'debit-credit-note-saved')
        <div class="alert alert-success">Debit/Credit note saved successfully.</div>
    @endif
    @if(session('status') === 'debit-credit-note-updated')
        <div class="alert alert-success">Debit/Credit note updated successfully.</div>
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

    <div class="eh-card p-4 mb-4">
        <div class="text-center mb-4">
            <div class="fw-bold text-uppercase">APM Customs Brokerage</div>
            <div class="text-muted small">Lot 7F 2&3 Rodriguez Compound, Aurenina Village, San Dionisio, 1700 City of Paranaque</div>
            <div class="text-muted small">Tel Nos: (02) 8682-6845, 8696-7798</div>
            <div class="text-muted small">VAT Reg. TIN: 120-291-938-00000</div>
            <div class="fw-semibold mt-2">DEBIT / CREDIT NOTE</div>
        </div>

        <form method="POST" action="{{ $formAction }}">
            @csrf
            @if($isEditing)
                @method('PUT')
            @endif
            <input type="hidden" name="deduct_advances" value="0">
            <div class="alert alert-warning d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <div class="fw-semibold">Advance Deduction</div>
                    <div class="small mb-0">Keep this checked if this Debit/Credit Note should deduct the JO advances. Uncheck it if the client does not want advances deducted.</div>
                </div>
                <label class="form-check form-switch d-flex align-items-center gap-2 mb-0">
                    <input class="form-check-input" type="checkbox" name="deduct_advances" value="1" @checked($deductAdvancesChecked)>
                    <span class="form-check-label fw-semibold">Deduct Advances</span>
                </label>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Date</label>
                    <input type="date" class="form-control" name="note_date" value="{{ old('note_date', $noteDateDefault) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Job Order</label>
                    <input type="hidden" name="job_order_id" value="{{ old('job_order_id', $fixedJo?->id) }}">
                    <input type="text" class="form-control" value="{{ $fixedJoLabel }}" readonly>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Bill To</label>
                    <input class="form-control" id="dcnBillTo" name="bill_to" value="{{ old('bill_to', $noteData['bill_to'] ?? ($fixedJo?->consignee ?? '')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input class="form-control" id="dcnBillAddress" name="bill_address" value="{{ old('bill_address', $noteData['bill_address'] ?? ($linkedClient?->address ?? '')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">TIN</label>
                    <input class="form-control" id="dcnBillTin" name="bill_tin" value="{{ old('bill_tin', $noteData['bill_tin'] ?? ($linkedClient?->tin_number ?? '')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Bus. Style</label>
                    <input class="form-control" id="dcnBusinessStyle" name="bill_business_style" value="{{ old('bill_business_style', $noteData['bill_business_style'] ?? ($linkedClient?->business_style ?? '')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Vol./Meas.</label>
                    <div class="input-group">
                        <input class="form-control" id="dcnVolMeas" name="vol_meas" value="{{ old('vol_meas', $noteData['vol_meas'] ?? ($fixedJo?->no_of_cbm ?? '')) }}">
                        <select class="form-select" name="vol_meas_unit">
                            <option value="KGS" @selected(old('vol_meas_unit', $noteData['vol_meas_unit'] ?? '') === 'KGS')>KGS</option>
                            <option value="CBM" @selected(old('vol_meas_unit', $noteData['vol_meas_unit'] ?? '') === 'CBM')>CBM</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Vessel/Voy.</label>
                    <input class="form-control" id="dcnVessel" name="vessel_voyage" value="{{ old('vessel_voyage', $noteData['vessel_voyage'] ?? ($fixedJo?->vessel_voyage_no ?? '')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">B/L No.</label>
                    <input class="form-control" id="dcnBlNo" name="bl_no" value="{{ old('bl_no', $noteData['bl_no'] ?? ($fixedJo?->bl_awb_no ?? '')) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Job Ref. No.</label>
                    <input class="form-control" id="dcnJobRefNo" name="job_ref_no" value="{{ old('job_ref_no', $noteData['job_ref_no'] ?? $computedJobRefNo) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <input class="form-control" id="dcnDescription" name="description" value="{{ old('description', $noteData['description'] ?? ($fixedJo?->description ?? '')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Shipper's Name</label>
                    <input class="form-control" id="dcnShipperName" name="shipper_name" value="{{ old('shipper_name', $noteData['shipper_name'] ?? ($fixedJo?->shipper ?? '')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Invoice No.</label>
                    <input class="form-control" id="dcnInvoiceNo" name="invoice_no" value="{{ old('invoice_no', $noteData['invoice_no'] ?? ($fixedJo?->invoice_no ?? '')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Port</label>
                    <input class="form-control" id="dcnPort" name="port" value="{{ old('port', $noteData['port'] ?? ($fixedJo?->port ?? '')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">CTNR. No.</label>
                    <input class="form-control" id="dcnContainerNo" name="container_no" value="{{ old('container_no', $noteData['container_no'] ?? ($fixedJo?->no_of_container ?? '')) }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Amount in Words (optional)</label>
                    <input class="form-control" id="dcnAmountInWords" name="amount_in_words" value="{{ old('amount_in_words', $noteData['amount_in_words'] ?? '') }}" placeholder="e.g. Forty Six Thousand Nine Hundred Thirteen and 22/100 Pesos" readonly>
                </div>
            </div>

            <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <div class="fw-semibold">Debit/Credit Note Paper Setup</div>
                    <div class="small mb-0">Choose a ready-made template when needed, or keep it as clear paper and type your own lines.</div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-outline-primary" type="button" id="useDcnTemplate">Use Template</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="clearDcnPaper">Clear Paper</button>
                </div>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Particulars</th>
                            <th style="width: 170px;">Side</th>
                            <th style="width: 180px;" class="text-end">Amount</th>
                            <th style="width: 56px;"></th>
                        </tr>
                    </thead>
                    <tbody id="dcnRows">
                        @for($i = 0; $i < $rowCount; $i++)
                            @php
                                $particularValue = (string) ($oldParticular[$i] ?? '');
                                $isReimbursableHeader = preg_match('/^a\\.?\\s*(?:receipted\\s*\\/\\s*)?reimburse?able(?:\\s+(?:voucher|charges))?$/i', $particularValue) === 1;
                                $isNonReceiptedHeader = preg_match('/^b\\.?\\s*non\\s*-?\\s*receipted\\s*charges$/i', $particularValue) === 1;
                                $isSectionHeader = $isReimbursableHeader || $isNonReceiptedHeader;
                            @endphp
                            <tr>
                                <td><input class="form-control" name="dcn_particular[]" value="{{ $oldParticular[$i] ?? '' }}" placeholder="Charge/adjustment detail"></td>
                                @if($isSectionHeader)
                                    <td>
                                        <input type="hidden" name="dcn_side[]" value="debit">
                                        <span class="text-muted small">Section header</span>
                                    </td>
                                    <td class="text-end">
                                        <input type="hidden" name="dcn_amount[]" value="0">
                                        <span class="text-muted small">No amount</span>
                                    </td>
                                @else
                                    <td>
                                        <select class="form-select" name="dcn_side[]">
                                            <option value="debit" @selected(($oldSide[$i] ?? 'debit') === 'debit')>Debit</option>
                                            <option value="credit" @selected(($oldSide[$i] ?? '') === 'credit')>Credit</option>
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" min="0" class="form-control text-end" name="dcn_amount[]" value="{{ $oldAmount[$i] ?? '' }}" placeholder="0.00"></td>
                                @endif
                                <td class="text-end"><button class="btn btn-sm btn-outline-danger remove-dcn-row" type="button">&times;</button></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" type="button" id="addDcnRow">Add line</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="addReimbursableDcnRow">+ A. Reimbursable Voucher</button>
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="addNonReceiptedDcnRow">+ Non Receipted Charges</button>
                </div>
                <div class="text-muted small">Choose debit or credit per line to control which print column it goes to.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Remarks (optional)</label>
                <textarea class="form-control" name="remarks" rows="2">{{ old('remarks', $noteData['remarks'] ?? ($note?->remarks ?? '')) }}</textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button class="btn btn-primary" type="submit">{{ $isEditing ? 'Update Debit/Credit Note' : 'Save Debit/Credit Note' }}</button>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    (function () {
        const rowsWrap = document.getElementById('dcnRows');
        const addBtn = document.getElementById('addDcnRow');
        const addReimbursableBtn = document.getElementById('addReimbursableDcnRow');
        const addNonReceiptedBtn = document.getElementById('addNonReceiptedDcnRow');
        const useTemplateBtn = document.getElementById('useDcnTemplate');
        const clearPaperBtn = document.getElementById('clearDcnPaper');
        const linkEnergiesDcnTemplateRows = [
            { particular: 'A. RECEIPTED/ REIMBURSEABLE CHARGES' },
            { particular: 'CONTAINER DEPOSIT - ONE AR#', side: 'debit', amount: '' },
        ];

        const isReimbursableHeader = (value) => /^a\.?\s*(?:receipted\s*\/\s*)?reimburse?able(?:\s+(?:voucher|charges))?$/i.test(String(value || '').trim());
        const isNonReceiptedHeader = (value) => /^b\.?\s*non\s*-?\s*receipted\s*charges$/i.test(String(value || '').trim());
        const isSectionHeader = (value) => isReimbursableHeader(value) || isNonReceiptedHeader(value);

        const rowHtml = ({ particular = '', side = 'debit', amount = '' } = {}) => {
            const safeParticular = String(particular).replace(/"/g, '&quot;');

            if (isSectionHeader(particular)) {
                return `
                    <td><input class="form-control" name="dcn_particular[]" value="${safeParticular}" placeholder="Charge/adjustment detail"></td>
                    <td>
                        <input type="hidden" name="dcn_side[]" value="debit">
                        <span class="text-muted small">Section header</span>
                    </td>
                    <td class="text-end">
                        <input type="hidden" name="dcn_amount[]" value="0">
                        <span class="text-muted small">No amount</span>
                    </td>
                    <td class="text-end"><button class="btn btn-sm btn-outline-danger remove-dcn-row" type="button">&times;</button></td>
                `;
            }

            return `
                <td><input class="form-control" name="dcn_particular[]" value="${safeParticular}" placeholder="Charge/adjustment detail"></td>
                <td>
                    <select class="form-select" name="dcn_side[]">
                        <option value="debit" ${side === 'debit' ? 'selected' : ''}>Debit</option>
                        <option value="credit" ${side === 'credit' ? 'selected' : ''}>Credit</option>
                    </select>
                </td>
                <td><input type="number" step="0.01" min="0" class="form-control text-end" name="dcn_amount[]" value="${amount}" placeholder="0.00"></td>
                <td class="text-end"><button class="btn btn-sm btn-outline-danger remove-dcn-row" type="button">&times;</button></td>
            `;
        };

        const appendDcnRow = (data = {}) => {
            if (!rowsWrap) return null;

            const row = document.createElement('tr');
            row.innerHTML = rowHtml(data);
            rowsWrap.appendChild(row);
            return row;
        };

        const resetDcnRows = (rows) => {
            if (!rowsWrap) return;

            rowsWrap.innerHTML = '';
            rows.forEach((row) => appendDcnRow(row));
            refreshAmountInWords();
        };

        if (rowsWrap && addBtn) {
            addBtn.addEventListener('click', () => {
                appendDcnRow();
            });

            addReimbursableBtn?.addEventListener('click', () => {
                appendDcnRow({ particular: 'A. REIMBURSABLE VOUCHER' });
            });

            addNonReceiptedBtn?.addEventListener('click', () => {
                appendDcnRow({ particular: 'B. NON-RECEIPTED CHARGES' });
            });

            useTemplateBtn?.addEventListener('click', () => {
                if (isLinkEnergies()) {
                    resetDcnRows(linkEnergiesDcnTemplateRows);
                    return;
                }

                resetDcnRows([
                    { particular: 'A. RECEIPTED/ REIMBURSEABLE CHARGES' },
                ]);
            });

            clearPaperBtn?.addEventListener('click', () => {
                resetDcnRows([{}]);
            });

            rowsWrap.addEventListener('click', (event) => {
                if (event.target.classList.contains('remove-dcn-row')) {
                    const row = event.target.closest('tr');
                    if (row && rowsWrap.querySelectorAll('tr').length > 1) {
                        row.remove();
                    }
                }
            });
        }

        const setValue = (id, value) => {
            const el = document.getElementById(id);
            if (el) {
                el.value = value || '';
            }
        };

        const isLinkEnergies = () => {
            const billTo = document.getElementById('dcnBillTo')?.value || '';
            const busStyle = document.getElementById('dcnBusinessStyle')?.value || '';
            const text = `${billTo} ${busStyle}`.toUpperCase();
            return text.includes('LINK ENERGIE') || text.includes('LINK ENERGIES');
        };

        const numberToWords = (num) => {
            if (!Number.isFinite(num)) return '';
            const ones = ['Zero','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
            const tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
            const scales = ['','Thousand','Million','Billion'];

            const chunkToWords = (n) => {
                const parts = [];
                const hundred = Math.floor(n / 100);
                const rest = n % 100;
                if (hundred) parts.push(`${ones[hundred]} Hundred`);
                if (rest) {
                    if (rest < 20) {
                        parts.push(ones[rest]);
                    } else {
                        const ten = Math.floor(rest / 10);
                        const one = rest % 10;
                        parts.push(tens[ten] + (one ? ` ${ones[one]}` : ''));
                    }
                }
                return parts.join(' ');
            };

            const isNegative = num < 0;
            const abs = Math.abs(num);
            const whole = Math.floor(abs);
            const cents = Math.round((abs - whole) * 100);

            if (whole === 0 && cents === 0) return 'Zero Pesos Only';

            let value = whole;
            let scaleIndex = 0;
            const words = [];
            while (value > 0) {
                const chunk = value % 1000;
                if (chunk) {
                    const chunkWords = chunkToWords(chunk);
                    const scale = scales[scaleIndex] ? ` ${scales[scaleIndex]}` : '';
                    words.unshift(`${chunkWords}${scale}`.trim());
                }
                value = Math.floor(value / 1000);
                scaleIndex += 1;
            }

            let result = words.join(' ');
            if (cents > 0) {
                result += ` and ${cents}/100`;
            }
            result += ' Pesos Only';
            return isNegative ? `Negative ${result}` : result;
        };

        const refreshAmountInWords = () => {
            const rows = rowsWrap ? rowsWrap.querySelectorAll('tr') : [];
            let net = 0;
            rows.forEach((row) => {
                const amountInput = row.querySelector('input[name="dcn_amount[]"]');
                const sideInput = row.querySelector('[name="dcn_side[]"]');
                const amount = Number.parseFloat(amountInput?.value || '0') || 0;
                const side = (sideInput?.value || 'debit').toLowerCase();
                net += side === 'credit' ? -amount : amount;
            });
            setValue('dcnAmountInWords', numberToWords(net));
        };

        rowsWrap?.addEventListener('input', refreshAmountInWords);
        rowsWrap?.addEventListener('change', refreshAmountInWords);
        refreshAmountInWords();
    })();
</script>
@endpush
