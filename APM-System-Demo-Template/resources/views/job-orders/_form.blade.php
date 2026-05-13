@php
    $isNew = !$jobOrder->exists;
    $codeOptions = ['FCL', 'LCL', 'AMO', 'AIR', 'PKL', 'BKK', 'NTC', 'OMB', 'ESB'];
    $defaultMo = $isNew ? now()->format('m') : $jobOrder->mo;
    $defaultJoDate = $isNew ? now()->format('Y-m-d') : optional($jobOrder->jo_date)->format('Y-m-d');
    $defaultNumber = $isNew ? ($nextJoNumber ?? '') : $jobOrder->number;
    $selectedClientId = old('client_id', $jobOrder->client_id);
    $selectedClient = collect($clients ?? [])->firstWhere('id', (int) $selectedClientId);
    $consigneeDisplay = $selectedClient
        ? trim($selectedClient->name . ($selectedClient->address ? ' - ' . $selectedClient->address : ''))
        : old('consignee', $jobOrder->consignee);
    $clientOptions = collect($clients ?? [])->map(function ($client) {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'address' => $client->address,
            'label' => trim($client->name . ($client->address ? ' - ' . $client->address : '')),
        ];
    })->values();
@endphp

@push('styles')
<style>
    .jo-form {
        --jo-text: #16324f;
        --jo-muted: #4f6781;
        --jo-border: #b7cce0;
        --jo-focus: #0f5ea8;
        --jo-bg: #f4f8fc;
        --jo-basic: #e8f1ff;
        --jo-parties: #edf9f1;
        --jo-dates: #fff5e6;
        --jo-cargo: #f7eefc;
        --jo-docs: #eef7fb;
        --jo-notes: #fff0f0;
    }

    .jo-form .jo-section {
        border: 1px solid var(--jo-border);
        border-radius: 1rem;
        padding: 1rem 1rem 1.15rem;
        box-shadow: 0 8px 24px rgba(15, 48, 84, 0.06);
    }

    .jo-form .jo-section-header {
        padding: 0.85rem 1rem;
        border-radius: 0.8rem;
        margin-bottom: 1rem;
    }

    .jo-form .jo-section-header h6 {
        color: var(--jo-text);
        font-size: 1rem;
        letter-spacing: 0.01em;
    }

    .jo-form .jo-section-header span {
        color: var(--jo-muted) !important;
        font-weight: 600;
    }

    .jo-form .jo-section-basic {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .jo-form .jo-section-basic .jo-section-header {
        background: var(--jo-basic);
    }

    .jo-form .jo-section-parties {
        background: linear-gradient(180deg, #ffffff 0%, #f7fcf8 100%);
    }

    .jo-form .jo-section-parties .jo-section-header {
        background: var(--jo-parties);
    }

    .jo-form .jo-section-dates {
        background: linear-gradient(180deg, #ffffff 0%, #fffaf1 100%);
    }

    .jo-form .jo-section-dates .jo-section-header {
        background: var(--jo-dates);
    }

    .jo-form .jo-section-cargo {
        background: linear-gradient(180deg, #ffffff 0%, #fbf8ff 100%);
    }

    .jo-form .jo-section-cargo .jo-section-header {
        background: var(--jo-cargo);
    }

    .jo-form .jo-section-docs {
        background: linear-gradient(180deg, #ffffff 0%, #f7fbfe 100%);
    }

    .jo-form .jo-section-docs .jo-section-header {
        background: var(--jo-docs);
    }

    .jo-form .jo-section-notes {
        background: linear-gradient(180deg, #ffffff 0%, #fff8f8 100%);
    }

    .jo-form .jo-section-notes .jo-section-header {
        background: var(--jo-notes);
    }

    .jo-form .form-label {
        color: var(--jo-text);
        font-size: 0.95rem;
        margin-bottom: 0.45rem;
    }

    .jo-form .form-control,
    .jo-form .input-group .btn {
        min-height: 48px;
        border-width: 2px;
        border-color: var(--jo-border);
        border-radius: 0.8rem;
        font-size: 1rem;
    }

    .jo-form .form-control {
        background: var(--jo-bg);
        color: #0f2740;
    }

    .jo-form .input-group > .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .jo-form .input-group > .btn {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        font-weight: 700;
    }

    .jo-form .form-control::placeholder {
        color: #5f7791;
        opacity: 1;
    }

    .jo-form .form-control:focus {
        border-color: var(--jo-focus);
        box-shadow: 0 0 0 0.25rem rgba(15, 94, 168, 0.18);
        background: #ffffff;
    }

    .jo-form .form-text {
        color: var(--jo-muted);
        font-size: 0.92rem;
        font-weight: 500;
    }

    .jo-form #consigneeOptions .list-group-item {
        border-left: 4px solid #5a8ec9;
        font-size: 0.98rem;
        padding: 0.85rem 1rem;
    }
</style>
@endpush

<div class="row g-4 jo-form">
    <div class="col-12">
        <div class="jo-section jo-section-basic">
            <div class="jo-section-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold">JO Basics</h6>
                <span class="text-muted small">Core identifiers</span>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">CODE</label>
                    <select class="form-select" name="code">
                        <option value="">Select code</option>
                        @foreach($codeOptions as $codeOption)
                            <option value="{{ $codeOption }}" @selected(old('code', $jobOrder->code) === $codeOption)>{{ $codeOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">MO.</label>
                    <input class="form-control" name="mo" value="{{ old('mo', $defaultMo) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">NO.</label>
                    <div class="input-group">
                        <input
                            class="form-control"
                            id="jobOrderNumberInput"
                            name="number"
                            value="{{ old('number', $defaultNumber) }}"
                            placeholder="{{ $nextJoNumber ?? '' }}"
                            data-check-url="{{ route('operations.job-orders.check-number') }}"
                            data-exclude-id="{{ $jobOrder->id ?? '' }}">
                        @if($isNew)
                            <button
                                class="btn btn-outline-secondary"
                                type="button"
                                id="useNextJoNumber"
                                data-next-number="{{ $nextJoNumber ?? '' }}">
                                Use Next
                            </button>
                        @endif
                    </div>
                    @if($isNew)
                        <div class="form-text">You can type an existing JO number if it has not been recorded yet, or keep the suggested next number for automatic progression.</div>
                    @endif
                    <div class="invalid-feedback d-none" id="jobOrderNumberFeedback"></div>
                    @error('number')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">JO DATE</label>
                    <input type="date" class="form-control" name="jo_date" value="{{ old('jo_date', $defaultJoDate) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">COSTING</label>
                    <input class="form-control" name="costing" value="{{ old('costing', $jobOrder->costing) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">STATUS</label>
                    <input class="form-control" name="status" value="{{ old('status', $jobOrder->status) }}" placeholder="e.g. In progress, Delivered">
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="jo-section jo-section-parties">
            <div class="jo-section-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold">Parties & Shipment</h6>
                <span class="text-muted small">Client details</span>
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">CONSIGNEE</label>
                    <div class="position-relative">
                        <input
                            class="form-control"
                            id="consigneeSearch"
                            type="text"
                            placeholder="Search consignee or branch"
                            autocomplete="off"
                            value="{{ $consigneeDisplay }}"
                        >
                        <input type="hidden" name="client_id" id="consigneeClientId" value="{{ $selectedClientId }}">
                        <input type="hidden" name="consignee" id="consigneeValue" value="{{ old('consignee', $jobOrder->consignee) }}" required>
                        <div class="list-group position-absolute w-100 shadow-sm d-none" id="consigneeOptions" style="max-height: 220px; overflow-y: auto; z-index: 10;"></div>
                    </div>
                    <div class="form-text">Type to filter, then select the exact client branch from the list.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">SHIPPER</label>
                    <input class="form-control" name="shipper" value="{{ old('shipper', $jobOrder->shipper) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">ORIGIN</label>
                    <input class="form-control" name="origin" value="{{ old('origin', $jobOrder->origin) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">PORT</label>
                    <input class="form-control" name="port" value="{{ old('port', $jobOrder->port) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">SHIPPING LINES</label>
                    <input class="form-control" name="shipping_lines" value="{{ old('shipping_lines', $jobOrder->shipping_lines) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">CO-LOADER / FORWARDER</label>
                    <input class="form-control" name="co_loader_forwarder" value="{{ old('co_loader_forwarder', $jobOrder->co_loader_forwarder) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="jo-section jo-section-dates">
        <div class="jo-section-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold">Key Dates</h6>
            <span class="text-muted small">Timeline</span>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">ETA</label>
                <input type="date" class="form-control" name="eta" value="{{ old('eta', optional($jobOrder->eta)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">DEMURRAGE DATE</label>
                <input type="date" class="form-control" name="demurrage_date" value="{{ old('demurrage_date', optional($jobOrder->demurrage_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">DETENTION DATE</label>
                <input type="date" class="form-control" name="detention_date" value="{{ old('detention_date', optional($jobOrder->detention_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">PORT STORAGE DATE</label>
                <input type="date" class="form-control" name="port_storage_date" value="{{ old('port_storage_date', optional($jobOrder->port_storage_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">DISCHARGE DATE</label>
                <input type="date" class="form-control" name="discharge_date" value="{{ old('discharge_date', optional($jobOrder->discharge_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">DATE DELIVERED</label>
                <input type="date" class="form-control" name="date_delivered" value="{{ old('date_delivered', optional($jobOrder->date_delivered)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">DATE OF ARRIVAL</label>
                <input type="date" class="form-control" name="date_of_arrival" value="{{ old('date_of_arrival', optional($jobOrder->date_of_arrival)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">DATE REFUNDED</label>
                <input type="date" class="form-control" name="date_refunded" value="{{ old('date_refunded', optional($jobOrder->date_refunded)->format('Y-m-d')) }}">
            </div>
        </div>
        </div>
    </div>

    <div class="col-12">
        <div class="jo-section jo-section-cargo">
        <div class="jo-section-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold">Cargo & Container</h6>
            <span class="text-muted small">Cargo details</span>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">DESCRIPTION</label>
                <input class="form-control" name="description" value="{{ old('description', $jobOrder->description) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">NO. OF CONTAINER</label>
                <input class="form-control" name="no_of_container" value="{{ old('no_of_container', $jobOrder->no_of_container) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">NO. OF PACKAGES</label>
                <input class="form-control" name="no_of_packages" value="{{ old('no_of_packages', $jobOrder->no_of_packages) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">KIND OF PACKAGES</label>
                <input class="form-control" name="kind_of_packages" value="{{ old('kind_of_packages', $jobOrder->kind_of_packages) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">GROSS WEIGHT</label>
                <input type="number" step="0.01" min="0" class="form-control" name="gross_weight" value="{{ old('gross_weight', $jobOrder->gross_weight) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">NO. OF CBM</label>
                <input type="number" step="0.01" min="0" class="form-control" name="no_of_cbm" value="{{ old('no_of_cbm', $jobOrder->no_of_cbm) }}">
            </div>
        </div>
        </div>
    </div>

    <div class="col-12">
        <div class="jo-section jo-section-docs">
        <div class="jo-section-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold">Documents & References</h6>
            <span class="text-muted small">Tracking and reference numbers</span>
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">P.O. #</label>
                <input class="form-control" name="po_number" value="{{ old('po_number', $jobOrder->po_number) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">INVOICE NO.</label>
                <input class="form-control" name="invoice_no" value="{{ old('invoice_no', $jobOrder->invoice_no) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">ENTRY NO.</label>
                <input class="form-control" name="entry_no" value="{{ old('entry_no', $jobOrder->entry_no) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">VESSEL / VOYAGE NO.</label>
                <input class="form-control" name="vessel_voyage_no" value="{{ old('vessel_voyage_no', $jobOrder->vessel_voyage_no) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">BL / AWB NO.</label>
                <input class="form-control" name="bl_awb_no" value="{{ old('bl_awb_no', $jobOrder->bl_awb_no) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">CTNR DEPOSIT</label>
                <input type="number" step="0.01" min="0" class="form-control" name="ctnr_deposit" value="{{ old('ctnr_deposit', $jobOrder->ctnr_deposit) }}">
            </div>
        </div>
        </div>
    </div>

    <div class="col-12">
        <div class="jo-section jo-section-notes">
        <div class="jo-section-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold">Notes & Attachments</h6>
            <span class="text-muted small">Optional references</span>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">REMARKS / LOCATION</label>
                <input class="form-control" name="remarks_location" value="{{ old('remarks_location', $jobOrder->remarks_location) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Scanned Documents / Attachments</label>
                <input class="form-control" type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.bmp,.tif,.tiff,image/*">
                <div class="form-text">Scan the document first, then upload the saved file here. A copy is stored in APM after saving.</div>
            </div>
        </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const numberInput = document.getElementById('jobOrderNumberInput');
        const useNextButton = document.getElementById('useNextJoNumber');
        const feedback = document.getElementById('jobOrderNumberFeedback');
        const form = numberInput ? numberInput.closest('form') : null;
        const submitButtons = form ? [...form.querySelectorAll('button[type="submit"]')] : [];
        const existingJoNumbers = new Set(
            @json(collect($existingJoNumbers ?? [])->map(fn ($x) => trim((string) $x))->filter()->values())
                .map((value) => String(value || '').trim())
                .filter((value) => value !== '')
        );

        const setNumberErrorState = (message = '') => {
            if (!numberInput) return;
            const hasError = String(message).trim() !== '';
            numberInput.classList.toggle('is-invalid', hasError);
            if (feedback) {
                feedback.textContent = message || '';
                feedback.classList.toggle('d-none', !hasError);
            }
            submitButtons.forEach((button) => {
                button.disabled = hasError;
            });
        };

        let checkTimer = null;
        let latestToken = 0;
        const localDuplicateCheck = () => {
            if (!numberInput) return false;
            const number = String(numberInput.value || '').trim();
            if (number === '') {
                setNumberErrorState('');
                return false;
            }
            if (existingJoNumbers.has(number)) {
                setNumberErrorState(`JO number ${number} already exists.`);
                return true;
            }
            setNumberErrorState('');
            return false;
        };

        const checkDuplicateNumber = () => {
            if (!numberInput) return;
            const number = (numberInput.value || '').trim();
            if (number === '') {
                setNumberErrorState('');
                return;
            }

            if (localDuplicateCheck()) {
                return;
            }

            const url = numberInput.dataset.checkUrl;
            if (!url) return;

            const params = new URLSearchParams();
            params.set('number', number);
            const excludeId = (numberInput.dataset.excludeId || '').trim();
            if (excludeId !== '') {
                params.set('exclude_id', excludeId);
            }

            const token = ++latestToken;
            fetch(`${url}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            })
                .then((response) => response.ok ? response.json() : Promise.reject())
                .then((data) => {
                    if (token !== latestToken) return;
                    if (data?.exists) {
                        setNumberErrorState(data?.message || 'JO number already exists.');
                        return;
                    }
                    setNumberErrorState('');
                })
                .catch(() => {
                    if (token !== latestToken) return;
                    // Do not hard-block submit on network errors.
                    setNumberErrorState('');
                });
        };

        if (numberInput && useNextButton) {
            useNextButton.addEventListener('click', function () {
                numberInput.value = useNextButton.dataset.nextNumber || '';
                numberInput.focus();
                checkDuplicateNumber();
            });
        }

        if (numberInput) {
            numberInput.addEventListener('input', () => {
                localDuplicateCheck();
                if (checkTimer) clearTimeout(checkTimer);
                checkTimer = setTimeout(checkDuplicateNumber, 350);
            });
            numberInput.addEventListener('blur', checkDuplicateNumber);
            if (numberInput.value && numberInput.value.trim() !== '') {
                checkDuplicateNumber();
            }
        }
    })();

    (function () {
        const input = document.getElementById('consigneeSearch');
        const clientId = document.getElementById('consigneeClientId');
        const hidden = document.getElementById('consigneeValue');
        const list = document.getElementById('consigneeOptions');
        if (!input || !clientId || !hidden || !list) {
            return;
        }
        const clients = @json($clientOptions);

        const renderList = (items) => {
            list.innerHTML = '';
            if (!items.length) {
                list.classList.add('d-none');
                return;
            }
            items.forEach((client) => {
                const item = document.createElement('div');
                item.className = 'list-group-item list-group-item-action';
                item.innerHTML = `<div class="fw-semibold">${client.name}</div>${client.address ? `<div class="small text-muted">${client.address}</div>` : ''}`;
                item.dataset.value = client.name;
                item.dataset.clientId = client.id;
                item.dataset.label = client.label;
                list.appendChild(item);
            });
            list.classList.remove('d-none');
        };

        const filterClients = (value) => {
            const term = value.trim().toLowerCase();
            if (!term) {
                renderList(clients);
                return;
            }
            const matches = clients.filter((client) =>
                client.name.toLowerCase().includes(term)
                || (client.address || '').toLowerCase().includes(term)
                || client.label.toLowerCase().includes(term)
            );
            renderList(matches);
        };

        const syncToSelection = () => {
            const term = input.value.trim().toLowerCase();
            const exact = clients.find((client) => client.label.toLowerCase() === term || client.name.toLowerCase() === term);
            if (exact) {
                clientId.value = exact.id;
                hidden.value = exact.name;
                input.value = exact.label;
                return;
            }

            if (clientId.value && hidden.value) {
                const selected = clients.find((client) => String(client.id) === String(clientId.value));
                input.value = selected ? selected.label : hidden.value;
                return;
            }

            clientId.value = '';
            hidden.value = '';
            input.value = '';
        };

        input.addEventListener('focus', () => {
            filterClients(input.value);
        });
        input.addEventListener('input', () => {
            clientId.value = '';
            hidden.value = '';
            filterClients(input.value);
        });
        list.addEventListener('mousedown', (event) => {
            event.preventDefault();
        });
        list.addEventListener('click', (event) => {
            const item = event.target.closest('[data-value]');
            if (!item) {
                return;
            }
            clientId.value = item.dataset.clientId || '';
            hidden.value = item.dataset.value || '';
            input.value = item.dataset.label || item.dataset.value || '';
            list.classList.add('d-none');
        });
        input.addEventListener('blur', () => {
            setTimeout(() => {
                syncToSelection();
                list.classList.add('d-none');
            }, 100);
        });
        document.addEventListener('click', (event) => {
            if (!list.contains(event.target) && event.target !== input) {
                list.classList.add('d-none');
            }
        });
    })();
</script>
@endpush
