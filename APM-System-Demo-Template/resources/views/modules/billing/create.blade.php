@extends('layouts.employeehub')

@section('content')
    @php
        $isEdit = isset($statement) && $statement;
        $baseData = $isEdit ? ($statement->data ?? []) : [];
        $documentType = old('document_type', $documentType ?? ($baseData['document_type'] ?? 'billing_statement'));
        $existingBillingUrl = !empty($existingBillingStatement) ? route('billing.show', $existingBillingStatement) : null;
        $existingServiceUrl = !empty($existingServiceInvoice) ? route('billing.show', $existingServiceInvoice) : null;
        $getValue = function (string $key, $fallback = '') use ($baseData) {
            return old($key, $baseData[$key] ?? $fallback);
        };
        $nonDescSeed = old('non_receipted_desc', $baseData['non_receipted_desc'] ?? ['']);
        $nonAmtSeed = old('non_receipted_amount', $baseData['non_receipted_amount'] ?? ['']);
        $recDescSeed = old('receipted_desc', $baseData['receipted_desc'] ?? ['']);
        $recAmtSeed = old('receipted_amount', $baseData['receipted_amount'] ?? ['']);
        $siDescSeed = old('si_item_description', $baseData['si_item_description'] ?? ['BROKERAGE FEE AS PER CAO 1-2001']);
        $siQtySeed = old('si_quantity', $baseData['si_quantity'] ?? ['']);
        $siUnitSeed = old('si_unit_cost', $baseData['si_unit_cost'] ?? ['']);
        $siAmtSeed = old('si_amount', $baseData['si_amount'] ?? ['']);

        if (!is_array($siDescSeed)) { $siDescSeed = [$siDescSeed]; }
        if (!is_array($siQtySeed)) { $siQtySeed = [$siQtySeed]; }
        if (!is_array($siUnitSeed)) { $siUnitSeed = [$siUnitSeed]; }
        if (!is_array($siAmtSeed)) { $siAmtSeed = [$siAmtSeed]; }

        $siRows = max(count($siDescSeed), count($siQtySeed), count($siUnitSeed), count($siAmtSeed), 1);
        $joId = old('job_order_id', $jobOrder?->id ?? ($isEdit ? $statement->job_order_id : null));
    @endphp
    <style>
        .apm-brand-font {
            font-family: "Blippo", "Cooper Black", "Arial Black", Impact, "Trebuchet MS", sans-serif;
            letter-spacing: 0.03em;
        }
        .billing-separator {
            border-top: 2px solid #2d2d2d;
            margin: 8px 0 12px;
        }
        .billing-meta .meta-label {
            font-weight: 700;
            white-space: nowrap;
        }
        .billing-meta .meta-colon {
            width: 12px;
            text-align: center;
            font-weight: 700;
        }
        .billing-meta .meta-input {
            min-height: 34px;
        }
        .meta-pair {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .meta-pair .meta-label {
            flex: 0 0 230px;
            margin-bottom: 0;
        }
        .meta-pair .meta-colon {
            flex: 0 0 12px;
        }
        .meta-pair .meta-field {
            flex: 1 1 auto;
            min-width: 0;
        }
        .meta-pair .meta-checks {
            flex: 1 1 auto;
        }
        .meta-checks .form-check {
            line-height: 1.1;
        }
    </style>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="mb-1">{{ $isEdit ? 'Edit Billing' : 'Create Billing' }}</h2>
            <p class="text-muted mb-0">{{ $isEdit ? 'Update the existing billing document.' : 'Fill out the billing statement.' }}</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('billing') }}">Back</a>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <a class="btn btn-primary" href="{{ route('billing.create', ['job_order_id' => $joId]) }}">Create New</a>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" @disabled(empty($joId))>
                View Existing
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('billing.documents', ['job_order_id' => $joId]) }}">Billing Statements</a></li>
                <li><a class="dropdown-item" href="{{ route('billing.service-invoices.documents', ['job_order_id' => $joId]) }}">Service Invoices</a></li>
                <li><a class="dropdown-item" href="{{ route('billing.notes.documents', ['job_order_id' => $joId]) }}">Debit/Credit Notes</a></li>
            </ul>
        </div>
    </div>

    @if(($existingBillingStatement ?? null) || ($existingServiceInvoice ?? null))
        <div class="alert alert-info d-flex flex-wrap align-items-center gap-2 mb-3">
            <span class="fw-semibold">This JO already has:</span>
            @if($existingBillingStatement ?? null)
                <a class="btn btn-sm btn-outline-primary" href="{{ route('billing.show', $existingBillingStatement) }}">
                    Open Billing Statement
                </a>
            @endif
            @if($existingServiceInvoice ?? null)
                <a class="btn btn-sm btn-outline-primary" href="{{ route('billing.show', $existingServiceInvoice) }}">
                    Open Service Invoice
                </a>
            @endif
        </div>
    @endif

    <div class="eh-card p-4">
        <div class="text-center mb-4">
            <div class="fw-bold text-uppercase apm-brand-font">APM Customs Brokerage</div>
            <div class="text-muted small">Lot 7F 2&3 Rodriguez Compound, Aurenina Village, San Dionisio, 1700 City of Paranaque</div>
            <div class="text-muted small">Tel Nos: (02) 8682-6845, 8696-7798</div>
            <div class="text-muted small">VAT Reg. TIN: 120-291-938-00000</div>
            <div class="fw-semibold mt-3 apm-brand-font" id="documentTypeTitle">{{ (($documentType ?? 'billing_statement') === 'service_invoice') ? 'Service Invoice' : 'Billing Statement' }}</div>
            <div class="d-inline-flex gap-2 mt-3">
                <button
                    type="button"
                    class="btn btn-sm {{ (($documentType ?? 'billing_statement') === 'billing_statement') ? 'btn-primary' : 'btn-outline-primary' }}"
                    id="btnBillingStatement"
                >
                    Billing Statement
                </button>
                <button
                    type="button"
                    class="btn btn-sm {{ (($documentType ?? 'billing_statement') === 'service_invoice') ? 'btn-primary' : 'btn-outline-primary' }}"
                    id="btnServiceInvoice"
                >
                    Service Invoice
                </button>
            </div>
        </div>

        <form method="POST" action="{{ $isEdit ? route('billing.update', $statement) : route('billing.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            <input type="hidden" name="job_order_id" value="{{ $joId }}">
            <input type="hidden" name="grand_total" id="grandTotalInput" value="0.00">
            <input type="hidden" name="document_type" id="documentTypeInput" value="{{ $documentType ?? 'billing_statement' }}">
            <div class="billing-meta mb-3">
                <div class="row g-3 align-items-start">
                    <div class="col-md-6">
                        <div class="meta-pair">
                            <div class="meta-label" id="billToLabel">{{ (($documentType ?? 'billing_statement') === 'service_invoice') ? 'Sold To' : 'Bill To' }}</div>
                            <div class="meta-colon">:</div>
                            <div class="meta-field">
                                <input class="form-control meta-input" id="billToInput" name="bill_to" value="{{ $getValue('bill_to', $jobOrder?->consignee ?? $client?->name ?? '') }}">
                            </div>
                        </div>
                        <div class="meta-pair mt-1" id="registeredNameRow" style="display:none;">
                            <div class="meta-label">Registered Name</div>
                            <div class="meta-colon">:</div>
                            <div class="meta-field">
                                <input class="form-control meta-input" name="si_registered_name" value="{{ $getValue('si_registered_name', $jobOrder?->consignee ?? $client?->name ?? '') }}">
                            </div>
                        </div>
                        <div class="meta-pair mt-1" id="serviceTinRow" style="display:none;">
                            <div class="meta-label">TIN</div>
                            <div class="meta-colon">:</div>
                            <div class="meta-field">
                                <input class="form-control meta-input" id="billTinInputService" name="" value="{{ $getValue('bill_tin', $client?->tin_number ?? '') }}">
                            </div>
                        </div>
                        <div class="meta-pair mt-1" id="serviceBusinessAddressRow" style="display:none;">
                            <div class="meta-label">Business Address</div>
                            <div class="meta-colon">:</div>
                            <div class="meta-field">
                                <input class="form-control meta-input" id="billAddressInputService" name="" value="{{ $getValue('bill_address', $client?->address ?? '') }}">
                            </div>
                        </div>
                        <div id="billingAddressTinRow" class="mt-2">
                            <div class="meta-pair">
                                <div class="meta-label">Address</div>
                                <div class="meta-colon">:</div>
                                <div class="meta-field">
                                    <input class="form-control meta-input" id="billAddressInputBilling" name="bill_address" value="{{ $getValue('bill_address', $client?->address ?? '') }}">
                                </div>
                            </div>
                            <div class="meta-pair mt-2">
                                <div class="meta-label">TIN</div>
                                <div class="meta-colon">:</div>
                                <div class="meta-field">
                                    <input class="form-control meta-input" id="billTinInputBilling" name="bill_tin" value="{{ $getValue('bill_tin', $client?->tin_number ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="meta-pair">
                            <div class="meta-label">Date</div>
                            <div class="meta-colon">:</div>
                            <div class="meta-field">
                                <input type="date" class="form-control meta-input" name="statement_date" value="{{ $getValue('statement_date', now()->format('Y-m-d')) }}" readonly>
                            </div>
                        </div>
                        <div class="meta-pair mt-1" id="salesTypeInlineRow" style="display:none;">
                            <div class="meta-label">Sales Type</div>
                            <div class="meta-colon">:</div>
                            <div class="meta-checks">
                                <input type="hidden" name="si_sales_type" id="siSalesType" value="{{ $getValue('si_sales_type', '') }}">
                                <div class="d-flex flex-column gap-1">
                                    <label class="form-check d-flex align-items-center gap-2 mb-0">
                                        <input type="checkbox" class="form-check-input" id="siCashSales">
                                        <span class="form-check-label">Cash Sales</span>
                                    </label>
                                    <label class="form-check d-flex align-items-center gap-2 mb-0">
                                        <input type="checkbox" class="form-check-input" id="siChargeSales">
                                        <span class="form-check-label">Charge Sales</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 align-items-start mt-2" id="busStyleRow">
                    <div class="col-md-6">
                        <div class="meta-pair">
                            <div class="meta-label"></div>
                            <div class="meta-colon"></div>
                            <div class="meta-field"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="meta-pair">
                            <div class="meta-label">Bus. Style</div>
                            <div class="meta-colon">:</div>
                            <div class="meta-field">
                                <input class="form-control meta-input" id="businessStyleInput" name="bill_business_style" value="{{ $getValue('bill_business_style', $client?->business_style ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="billing-separator" id="vesselBlSeparator"></div>

            <div class="billing-meta mb-3" id="vesselBlSection">
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <div class="row g-2 align-items-center">
                            <div class="col-3 meta-label">Vessel/Voy.</div>
                            <div class="col-1 meta-colon">:</div>
                            <div class="col-8">
                                <input class="form-control meta-input" name="vessel_voyage" value="{{ $getValue('vessel_voyage', $jobOrder?->vessel_voyage_no ?? '') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2 align-items-center">
                            <div class="col-3 meta-label">Vol./Meas.</div>
                            <div class="col-1 meta-colon">:</div>
                            <div class="col-5">
                                <input class="form-control meta-input" name="vol_meas" value="{{ $getValue('vol_meas', $jobOrder?->no_of_cbm ?? '') }}">
                            </div>
                            <div class="col-3">
                                <select class="form-select meta-input" name="vol_meas_unit">
                                    <option value="KGS" @selected($getValue('vol_meas_unit', 'KGS') === 'KGS')>KGS</option>
                                    <option value="CBM" @selected($getValue('vol_meas_unit') === 'CBM')>CBM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-2 align-items-center mt-1">
                    <div class="col-md-6">
                        <div class="row g-2 align-items-center">
                            <div class="col-3 meta-label">B/L No.</div>
                            <div class="col-1 meta-colon">:</div>
                            <div class="col-8">
                                <input class="form-control meta-input" name="bl_no" value="{{ $getValue('bl_no', $jobOrder?->bl_awb_no ?? '') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2 align-items-center">
                            <div class="col-3 meta-label">Job Ref. No.</div>
                            <div class="col-1 meta-colon">:</div>
                            <div class="col-8">
                                <input class="form-control meta-input" name="job_ref_no" value="{{ $getValue('job_ref_no', $jobRefNo ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="billing-separator"></div>

            <div id="serviceInvoiceItems" class="mb-4" style="display:none;">
                <div class="fw-semibold mb-2">Item Description / Nature of Service</div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Item Description / Nature of Service</th>
                                <th style="width: 140px;">Quantity</th>
                                <th style="width: 160px;">Unit Cost</th>
                                <th style="width: 170px;">Amount</th>
                                <th style="width: 56px;"></th>
                            </tr>
                        </thead>
                        <tbody id="siItemsBody">
                            @for($i = 0; $i < $siRows; $i++)
                                <tr>
                                    <td><input class="form-control" name="si_item_description[]" value="{{ $siDescSeed[$i] ?? '' }}"></td>
                                    <td><input class="form-control text-end" name="si_quantity[]" type="number" step="0.01" min="0" value="{{ $siQtySeed[$i] ?? '' }}"></td>
                                    <td><input class="form-control text-end amount-input-si" name="si_unit_cost[]" type="number" step="0.01" min="0" value="{{ $siUnitSeed[$i] ?? '' }}"></td>
                                    <td><input class="form-control text-end amount-input-si si-amount-input" name="si_amount[]" type="number" step="0.01" min="0" value="{{ $siAmtSeed[$i] ?? '' }}"></td>
                                    <td class="text-end"><button class="btn btn-sm btn-outline-danger remove-si-row" type="button">&times;</button></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-sm btn-outline-primary" type="button" id="addSiLine">Add service line</button>
                <div class="small text-muted">Tip: set the final amount in the Amount column, then VAT totals are auto-computed below.</div>
            </div>

            <div id="serviceVatSummary" class="mb-4" style="display:none;">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr>
                                <td style="width: 50%;">
                                    <div class="d-flex justify-content-between"><span>VATable Sales</span><span><input class="form-control form-control-sm text-end d-inline-block" style="width: 150px;" name="si_vatable_sales" id="siVatableSales" readonly value="{{ $getValue('si_vatable_sales', '0.00') }}"></span></div>
                                </td>
                                <td style="width: 50%;">
                                    <div class="d-flex justify-content-between"><span>Total Sales (VAT Inclusive)</span><span><input class="form-control form-control-sm text-end d-inline-block" style="width: 150px;" name="si_total_sales" id="siTotalSales" readonly value="{{ $getValue('si_total_sales', '0.00') }}"></span></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between"><span>VAT</span><span><input class="form-control form-control-sm text-end d-inline-block" style="width: 150px;" name="si_vat" id="siVat" readonly value="{{ $getValue('si_vat', '0.00') }}"></span></div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between"><span>Less: VAT</span><span><input class="form-control form-control-sm text-end d-inline-block" style="width: 150px;" name="si_less_vat" id="siLessVat" readonly value="{{ $getValue('si_less_vat', '0.00') }}"></span></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between"><span>Zero Rated Sales</span><span><input class="form-control form-control-sm text-end d-inline-block" style="width: 150px;" name="si_zero_rated_sales" id="siZeroRatedSales" readonly value="{{ $getValue('si_zero_rated_sales', '0.00') }}"></span></div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between"><span>Amount: Net of VAT</span><span><input class="form-control form-control-sm text-end d-inline-block" style="width: 150px;" name="si_amount_net_vat" id="siAmountNetVat" readonly value="{{ $getValue('si_amount_net_vat', '0.00') }}"></span></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between"><span>VAT-Exempt Sales</span><span><input class="form-control form-control-sm text-end d-inline-block" style="width: 150px;" name="si_vat_exempt_sales" id="siVatExemptSales" readonly value="{{ $getValue('si_vat_exempt_sales', '0.00') }}"></span></div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        <span>Less: Withholding Tax</span>
                                        <span class="d-flex align-items-center gap-2">
                                            <label class="form-check-label small mb-0" for="siApplyWithholdingTax">Apply</label>
                                            <input class="form-check-input mt-0" name="si_apply_withholding_tax" id="siApplyWithholdingTax" type="checkbox" value="1" @checked((float) $getValue('si_less_withholding_tax', 0) > 0)>
                                            <input class="form-control form-control-sm text-end d-inline-block" style="width: 150px;" name="si_less_withholding_tax" id="siLessWithholdingTax" type="number" step="0.01" min="0" readonly value="{{ (float) $getValue('si_less_withholding_tax', 0) > 0 ? $getValue('si_less_withholding_tax', '0.00') : '0.00' }}">
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <div class="d-flex justify-content-between"><span>Add: VAT</span><span><input class="form-control form-control-sm text-end d-inline-block" style="width: 150px;" name="si_add_vat" id="siAddVat" readonly value="{{ $getValue('si_add_vat', '0.00') }}"></span></div>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <div class="d-flex justify-content-between fw-bold"><span>TOTAL AMOUNT DUE</span><span><input class="form-control form-control-sm text-end d-inline-block fw-bold" style="width: 150px;" name="si_total_amount_due" id="siTotalAmountDue" readonly value="{{ $getValue('si_total_amount_due', '0.00') }}"></span></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Description</label>
                    <input class="form-control" name="description" value="{{ $getValue('description', $jobOrder?->description ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Shipper's Name</label>
                    <input class="form-control" name="shipper_name" value="{{ $getValue('shipper_name', $jobOrder?->shipper ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Invoice No.</label>
                    <input class="form-control" name="invoice_no" value="{{ $getValue('invoice_no', $jobOrder?->invoice_no ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Port</label>
                    <input class="form-control" name="port" value="{{ $getValue('port', $jobOrder?->port ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">CNTR. No.</label>
                    <input class="form-control" name="container_no" value="{{ $getValue('container_no', $jobOrder?->no_of_container ?? '') }}">
                </div>
            </div>

            <div class="mb-4" id="brokerageExpensesSection">
                <div class="fw-semibold mb-2">Brokerage Reimbursable Expenses</div>
                <div class="mb-2" id="convergeNonReceiptedToggleWrap" style="display:none;">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="toggleConvergeNonReceiptedBtn">
                        Add Non-Receipted Charges
                    </button>
                </div>
                <div class="border rounded p-3 mb-3" id="nonReceiptedSection">
                    <div class="fw-semibold mb-2">A. Non-Receipted Charges</div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end" style="width: 160px;">Amount (PHP)</th>
                                    <th style="width: 56px;"></th>
                                </tr>
                            </thead>
                            <tbody id="nonReceiptedBody">
                                @foreach($nonDescSeed as $i => $desc)
                                    <tr>
                                        <td><input class="form-control" name="non_receipted_desc[]" value="{{ $desc }}"></td>
                                        <td><input class="form-control text-end amount-input" name="non_receipted_amount[]" type="number" step="0.01" min="0" value="{{ $nonAmtSeed[$i] ?? '' }}"></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-danger remove-row" type="button">&times;</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-end fw-semibold">Subtotal</td>
                                    <td class="text-end fw-semibold" id="nonReceiptedTotal">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mt-3" type="button" id="addNonReceipted">Add line</button>
                </div>

                <div class="border rounded p-3" id="receiptedSection">
                    <div class="fw-semibold mb-2">B. Receipted Charges</div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end" style="width: 160px;">Amount (PHP)</th>
                                    <th style="width: 56px;"></th>
                                </tr>
                            </thead>
                            <tbody id="receiptedBody">
                                @foreach($recDescSeed as $i => $desc)
                                    <tr>
                                        <td><input class="form-control" name="receipted_desc[]" value="{{ $desc }}"></td>
                                        <td><input class="form-control text-end amount-input" name="receipted_amount[]" type="number" step="0.01" min="0" value="{{ $recAmtSeed[$i] ?? '' }}"></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-danger remove-row" type="button">&times;</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-end fw-semibold">Subtotal</td>
                                    <td class="text-end fw-semibold" id="receiptedTotal">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mt-3" type="button" id="addReceipted">Add line</button>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Amount in Words:</label>
                    <input class="form-control" name="amount_in_words" id="amountInWords" placeholder="e.g. Fifty Six Thousand Pesos Only" readonly>
                </div>
                <div class="col-md-4 text-end">
                    <div class="fw-semibold">Total Amount (PHP)</div>
                    <div class="fs-4 fw-bold" id="grandTotal">0.00</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Prepared by</label>
                    <input class="form-control" name="prepared_by" value="{{ $getValue('prepared_by', 'A.D.E') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" id="approvedByLabel">Approved by</label>
                    <input class="form-control" name="approved_by" value="{{ $getValue('approved_by', 'A.P.M') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Received by</label>
                    <input class="form-control" name="received_by" value="{{ $getValue('received_by') }}">
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button class="btn btn-primary" type="submit" id="saveDocumentBtn">
                    @if(($documentType ?? 'billing_statement') === 'service_invoice')
                        {{ $isEdit ? 'Update Service Invoice' : 'Save Service Invoice' }}
                    @else
                        {{ $isEdit ? 'Update Billing Statement' : 'Save Billing Statement' }}
                    @endif
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const isEditForm = {{ $isEdit ? 'true' : 'false' }};
        const convergeReceiptedTemplates = [
            'WHARFAGE FEE -',
            'ARRASTRE CHARGES -',
            'CTNR. EQUIPMENT CLEARANCE -',
            'FCL CHARGES -',
            'CO-LOADER CHARGES -',
            'PROCESSING,DOCUMENTATION,CUSTOMS FORMS',
            'TRUCKING/DELIVERY CHARGES -',
            'TABS(TERMINAL APPOINTMENT BOOKING SYSTEM)',
            'RETURN OF EMPTY CONTAINER FEE',
            'MANPOWER',
        ];

        const addRow = (tbodyId) => {
            const body = document.getElementById(tbodyId);
            if (!body) {
                return;
            }
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input class="form-control" name="${tbodyId === 'nonReceiptedBody' ? 'non_receipted_desc[]' : 'receipted_desc[]'}"></td>
                <td><input class="form-control text-end amount-input" name="${tbodyId === 'nonReceiptedBody' ? 'non_receipted_amount[]' : 'receipted_amount[]'}" type="number" step="0.01" min="0"></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-danger remove-row" type="button">&times;</button>
                </td>
            `;
            body.appendChild(row);
        };

        const addSiRow = () => {
            const body = document.getElementById('siItemsBody');
            if (!body) {
                return;
            }
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input class="form-control" name="si_item_description[]"></td>
                <td><input class="form-control text-end" name="si_quantity[]" type="number" step="0.01" min="0"></td>
                <td><input class="form-control text-end amount-input-si" name="si_unit_cost[]" type="number" step="0.01" min="0"></td>
                <td><input class="form-control text-end amount-input-si si-amount-input" name="si_amount[]" type="number" step="0.01" min="0"></td>
                <td class="text-end"><button class="btn btn-sm btn-outline-danger remove-si-row" type="button">&times;</button></td>
            `;
            body.appendChild(row);
        };

        const setDocumentType = (type) => {
            const normalized = type === 'service_invoice' ? 'service_invoice' : 'billing_statement';
            const isService = normalized === 'service_invoice';
            const typeInput = document.getElementById('documentTypeInput');
            const title = document.getElementById('documentTypeTitle');
            const billToLabel = document.getElementById('billToLabel');
            const saveBtn = document.getElementById('saveDocumentBtn');
            const btnBilling = document.getElementById('btnBillingStatement');
            const btnService = document.getElementById('btnServiceInvoice');
            const brokerageSection = document.getElementById('brokerageExpensesSection');
            const serviceItems = document.getElementById('serviceInvoiceItems');
            const serviceVatSummary = document.getElementById('serviceVatSummary');
            const vesselBlSection = document.getElementById('vesselBlSection');
            const vesselBlSeparator = document.getElementById('vesselBlSeparator');
            const salesTypeInlineRow = document.getElementById('salesTypeInlineRow');
            const approvedByLabel = document.getElementById('approvedByLabel');
            const busStyleRow = document.getElementById('busStyleRow');
            const registeredNameRow = document.getElementById('registeredNameRow');
            const serviceTinRow = document.getElementById('serviceTinRow');
            const serviceBusinessAddressRow = document.getElementById('serviceBusinessAddressRow');
            const billingAddressTinRow = document.getElementById('billingAddressTinRow');
            const billTinInputBilling = document.getElementById('billTinInputBilling');
            const billTinInputService = document.getElementById('billTinInputService');
            const billAddressInputBilling = document.getElementById('billAddressInputBilling');
            const billAddressInputService = document.getElementById('billAddressInputService');

            if (typeInput) typeInput.value = normalized;
            if (title) title.textContent = isService ? 'Service Invoice' : 'Billing Statement';
            if (billToLabel) billToLabel.textContent = isService ? 'Sold To' : 'Bill To';
            if (saveBtn) saveBtn.textContent = isService
                ? (isEditForm ? 'Update Service Invoice' : 'Save Service Invoice')
                : (isEditForm ? 'Update Billing Statement' : 'Save Billing Statement');
            if (brokerageSection) brokerageSection.style.display = isService ? 'none' : '';
            if (serviceItems) serviceItems.style.display = isService ? '' : 'none';
            if (serviceVatSummary) serviceVatSummary.style.display = isService ? '' : 'none';
            if (vesselBlSection) vesselBlSection.style.display = isService ? 'none' : '';
            if (vesselBlSeparator) vesselBlSeparator.style.display = isService ? 'none' : '';
            if (salesTypeInlineRow) salesTypeInlineRow.style.display = isService ? '' : 'none';
            if (approvedByLabel) approvedByLabel.textContent = isService ? 'Checked by' : 'Approved by';
            if (busStyleRow) busStyleRow.style.display = isService ? 'none' : '';
            if (registeredNameRow) registeredNameRow.style.display = isService ? '' : 'none';
            if (serviceTinRow) serviceTinRow.style.display = isService ? '' : 'none';
            if (serviceBusinessAddressRow) serviceBusinessAddressRow.style.display = isService ? '' : 'none';
            if (billingAddressTinRow) billingAddressTinRow.style.display = isService ? 'none' : '';

            if (isService) {
                if (billTinInputService && billTinInputBilling) {
                    billTinInputService.value = billTinInputService.value || billTinInputBilling.value || '';
                    billTinInputService.name = 'bill_tin';
                    billTinInputBilling.name = '';
                }
                if (billAddressInputService && billAddressInputBilling) {
                    billAddressInputService.value = billAddressInputService.value || billAddressInputBilling.value || '';
                    billAddressInputService.name = 'bill_address';
                    billAddressInputBilling.name = '';
                }
            } else {
                if (billTinInputService && billTinInputBilling) {
                    billTinInputBilling.value = billTinInputBilling.value || billTinInputService.value || '';
                    billTinInputBilling.name = 'bill_tin';
                    billTinInputService.name = '';
                }
                if (billAddressInputService && billAddressInputBilling) {
                    billAddressInputBilling.value = billAddressInputBilling.value || billAddressInputService.value || '';
                    billAddressInputBilling.name = 'bill_address';
                    billAddressInputService.name = '';
                }
            }

            if (btnBilling && btnService) {
                btnBilling.classList.toggle('btn-primary', !isService);
                btnBilling.classList.toggle('btn-outline-primary', isService);
                btnService.classList.toggle('btn-primary', isService);
                btnService.classList.toggle('btn-outline-primary', !isService);
            }
        };

        const isConverge = () => {
            const billTo = document.getElementById('billToInput')?.value || '';
            const busStyle = document.getElementById('businessStyleInput')?.value || '';
            return `${billTo} ${busStyle}`.toUpperCase().includes('CONVERGE');
        };

        const setNonReceiptedEnabled = (enabled) => {
            const section = document.getElementById('nonReceiptedSection');
            if (!section) {
                return;
            }
            section.style.display = enabled ? '' : 'none';
            section.querySelectorAll('input, button, select, textarea').forEach((el) => {
                el.disabled = !enabled;
            });
        };

        const hasSeedNonReceipted = (() => {
            const rows = document.querySelectorAll('#nonReceiptedBody tr');
            for (const row of rows) {
                const desc = row.querySelector('input[name="non_receipted_desc[]"]')?.value || '';
                const amt = row.querySelector('input[name="non_receipted_amount[]"]')?.value || '';
                if (desc.trim() !== '' || amt.trim() !== '') {
                    return true;
                }
            }
            return false;
        })();
        let forceShowConvergeNonReceipted = hasSeedNonReceipted;
        const convergeToggleWrap = document.getElementById('convergeNonReceiptedToggleWrap');
        const convergeToggleBtn = document.getElementById('toggleConvergeNonReceiptedBtn');

        const refreshConvergeToggleLabel = () => {
            if (!convergeToggleBtn) {
                return;
            }
            convergeToggleBtn.textContent = forceShowConvergeNonReceipted
                ? 'Hide Non-Receipted Charges'
                : 'Add Non-Receipted Charges';
        };

        const applyConvergeReceiptedTemplate = () => {
            const body = document.getElementById('receiptedBody');
            if (!body) {
                return;
            }

            const existingDescs = [...body.querySelectorAll('input[name="receipted_desc[]"]')];
            const hasNonEmpty = existingDescs.some((input) => (input.value || '').trim() !== '');
            if (hasNonEmpty) {
                return;
            }

            body.innerHTML = '';
            convergeReceiptedTemplates.forEach((desc) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><input class="form-control" name="receipted_desc[]" value="${desc.replace(/"/g, '&quot;')}"></td>
                    <td><input class="form-control text-end amount-input" name="receipted_amount[]" type="number" step="0.01" min="0"></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-danger remove-row" type="button">&times;</button>
                    </td>
                `;
                body.appendChild(row);
            });
        };

        const applyClientMode = () => {
            if (isConverge()) {
                if (convergeToggleWrap) {
                    convergeToggleWrap.style.display = '';
                }
                setNonReceiptedEnabled(forceShowConvergeNonReceipted);
                applyConvergeReceiptedTemplate();
            } else {
                forceShowConvergeNonReceipted = false;
                refreshConvergeToggleLabel();
                if (convergeToggleWrap) {
                    convergeToggleWrap.style.display = 'none';
                }
                setNonReceiptedEnabled(true);
            }
            refreshTotals();
        };

        const sumTable = (tbodyId, totalId) => {
            const body = document.getElementById(tbodyId);
            const totalEl = document.getElementById(totalId);
            if (!body || !totalEl) {
                return 0;
            }
            let total = 0;
            body.querySelectorAll('.amount-input').forEach((input) => {
                total += parseFloat(input.value || '0');
            });
            totalEl.textContent = total.toFixed(2);
            return total;
        };

        const numberToWords = (num) => {
            if (!Number.isFinite(num)) {
                return '';
            }
            const ones = ['Zero','One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
            const tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
            const scales = ['','Thousand','Million','Billion'];

            const chunkToWords = (n) => {
                let words = [];
                const hundred = Math.floor(n / 100);
                const rest = n % 100;
                if (hundred) {
                    words.push(`${ones[hundred]} Hundred`);
                }
                if (rest) {
                    if (rest < 20) {
                        words.push(ones[rest]);
                    } else {
                        const ten = Math.floor(rest / 10);
                        const one = rest % 10;
                        words.push(tens[ten] + (one ? ` ${ones[one]}` : ''));
                    }
                }
                return words.join(' ');
            };

            if (num === 0) {
                return 'Zero Pesos Only';
            }

            let words = [];
            let scaleIndex = 0;
            let value = Math.floor(num);
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

            const cents = Math.round((num - Math.floor(num)) * 100);
            if (cents > 0) {
                words.push(`and ${cents}/100`);
            }

            return `${words.join(' ')} Pesos Only`;
        };

        const refreshTotals = () => {
            const isService = (document.getElementById('documentTypeInput')?.value || 'billing_statement') === 'service_invoice';
            const a = isService ? 0 : sumTable('nonReceiptedBody', 'nonReceiptedTotal');
            const b = isService ? 0 : sumTable('receiptedBody', 'receiptedTotal');
            let siAmount = 0;
            document.querySelectorAll('.si-amount-input').forEach((input) => {
                siAmount += parseFloat(input.value || '0') || 0;
            });
            const grand = document.getElementById('grandTotal');
            const grandInput = document.getElementById('grandTotalInput');
            const wordsInput = document.getElementById('amountInWords');

            if (isService) {
                const netVat = siAmount;
                const vat = netVat * 0.12;
                const totalSales = netVat + vat;
                const applyWithholding = document.getElementById('siApplyWithholdingTax')?.checked;
                const withholding = applyWithholding ? (netVat * 0.10) : 0;
                const totalDue = totalSales - withholding;

                const setField = (id, value) => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.value = Number.isFinite(value) ? value.toFixed(2) : '0.00';
                    }
                };

                setField('siTotalSales', totalSales);
                setField('siLessVat', vat);
                setField('siAmountNetVat', netVat);
                setField('siLessWithholdingTax', withholding);
                setField('siAddVat', vat);
                setField('siTotalAmountDue', totalDue);
                setField('siVatableSales', netVat);
                setField('siVat', vat);
                setField('siZeroRatedSales', 0);
                setField('siVatExemptSales', 0);
            }

            if (grand) {
                const total = (isService
                    ? parseFloat(document.getElementById('siTotalAmountDue')?.value || '0')
                    : (a + b)
                ).toFixed(2);
                grand.textContent = total;
                if (grandInput) {
                    grandInput.value = total;
                }
                if (wordsInput) {
                    wordsInput.value = numberToWords(parseFloat(total));
                }
            }
        };

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter') {
                return;
            }

            const target = event.target;
            if (!(target instanceof HTMLElement) || !target.closest('form')) {
                return;
            }

            if (target.matches('textarea, button, input[type="submit"], input[type="button"]')) {
                return;
            }

            event.preventDefault();
        });

        document.addEventListener('input', (event) => {
            if (
                event.target.classList.contains('amount-input') ||
                event.target.classList.contains('amount-input-si') ||
                event.target.id === 'siApplyWithholdingTax'
            ) {
                refreshTotals();
            }
        });

        document.addEventListener('click', (event) => {
            if (event.target.id === 'addNonReceipted') {
                addRow('nonReceiptedBody');
            }
            if (event.target.id === 'addReceipted') {
                addRow('receiptedBody');
            }
            if (event.target.id === 'addSiLine') {
                addSiRow();
                refreshTotals();
            }
            if (event.target.classList.contains('remove-si-row')) {
                const row = event.target.closest('tr');
                const body = document.getElementById('siItemsBody');
                if (row && body) {
                    if (body.querySelectorAll('tr').length <= 1) {
                        row.querySelectorAll('input').forEach((input) => {
                            input.value = '';
                        });
                    } else {
                        row.remove();
                    }
                    refreshTotals();
                }
            }
            if (event.target.classList.contains('remove-row')) {
                const row = event.target.closest('tr');
                if (row) {
                    row.remove();
                    refreshTotals();
                }
            }
        });

        document.getElementById('billToInput')?.addEventListener('input', applyClientMode);
        document.getElementById('businessStyleInput')?.addEventListener('input', applyClientMode);
        convergeToggleBtn?.addEventListener('click', () => {
            forceShowConvergeNonReceipted = !forceShowConvergeNonReceipted;
            refreshConvergeToggleLabel();
            applyClientMode();
        });
        document.getElementById('btnBillingStatement')?.addEventListener('click', (event) => {
            setDocumentType('billing_statement');
        });
        document.getElementById('btnServiceInvoice')?.addEventListener('click', (event) => {
            setDocumentType('service_invoice');
        });
        const siTypeInput = document.getElementById('siSalesType');
        const siCash = document.getElementById('siCashSales');
        const siCharge = document.getElementById('siChargeSales');

        const syncSalesChecks = () => {
            const value = (siTypeInput?.value || '').toLowerCase();
            if (siCash) siCash.checked = value === 'cash';
            if (siCharge) siCharge.checked = value === 'charge';
        };

        const setSalesType = (value) => {
            if (siTypeInput) {
                siTypeInput.value = (value === 'cash' || value === 'charge') ? value : '';
            }
            syncSalesChecks();
        };

        siCash?.addEventListener('change', () => {
            if (siCash.checked) {
                setSalesType('cash');
                return;
            }
            setSalesType(siCharge?.checked ? 'charge' : '');
        });
        siCharge?.addEventListener('change', () => {
            if (siCharge.checked) {
                setSalesType('charge');
                return;
            }
            setSalesType(siCash?.checked ? 'cash' : '');
        });

        refreshTotals();
        applyClientMode();
        refreshConvergeToggleLabel();
        setDocumentType(document.getElementById('documentTypeInput')?.value || 'billing_statement');
        syncSalesChecks();
    })();
</script>
@endpush
