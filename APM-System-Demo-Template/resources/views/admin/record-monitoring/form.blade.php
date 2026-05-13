@extends('layouts.employeehub')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">{{ $title }}</h2>
            <p class="text-muted mb-0">Track billing, advances, payments, balances, and receiving details without going back to Excel.</p>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('accounting.record-monitoring.index') }}">Back</a>
    </div>

    <div class="eh-card p-4">
        <form method="POST" action="{{ $formAction }}" class="row g-3">
            @csrf
            @if($formMethod !== 'POST')
                @method($formMethod)
            @endif

            @php
                $sourceTypeLabels = [
                    'billing_statement' => 'Billing Statement',
                    'service_invoice' => 'Service Invoice',
                    'debit_credit_note' => 'Debit / Credit Note',
                    'workbook' => 'Excel Workbook',
                    'manual' => 'Manual Entry',
                ];

                $sourceLabel = $sourceTypeLabels[$entry->source_type ?? 'manual'] ?? ucwords(str_replace('_', ' ', (string) $entry->source_type));
                $isSystemGenerated = in_array($entry->source_type, ['billing_statement', 'service_invoice', 'debit_credit_note'], true);
            @endphp

            @if($isSystemGenerated)
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        This row is system-generated from an existing document. Update follow-up fields here, and update the source document if you need to change the document details.
                    </div>
                </div>
            @endif

            <div class="col-md-3">
                <label class="form-label">Source</label>
                <input class="form-control" value="{{ $sourceLabel }}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">In-Charge</label>
                <input class="form-control" name="in_charge" value="{{ old('in_charge', $entry->in_charge) }}" placeholder="Assigned collector / owner">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status As Of</label>
                <input class="form-control" name="status_as_of" list="record-monitoring-statuses" value="{{ old('status_as_of', $entry->status_as_of) }}" placeholder="Select or type a status">
                <datalist id="record-monitoring-statuses">
                    @foreach(\App\Services\RecordMonitoringSyncService::STATUS_PRESETS as $preset)
                        <option value="{{ $preset }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="col-md-3">
                <label class="form-label">Balance</label>
                <input class="form-control" type="number" step="0.01" value="{{ old('balance_amount', $entry->balance_amount) }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Client Name</label>
                <input class="form-control" name="client_name" value="{{ old('client_name', $entry->client_name) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Sheet Name</label>
                <input class="form-control" name="sheet_name" value="{{ old('sheet_name', $entry->sheet_name) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Section Name</label>
                <input class="form-control" name="section_name" value="{{ old('section_name', $entry->section_name) }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">Group</label>
                <select class="form-select" name="entry_group">
                    <option value="active" @selected(old('entry_group', $entry->entry_group) === 'active')>Active</option>
                    <option value="paid" @selected(old('entry_group', $entry->entry_group) === 'paid')>Paid</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date</label>
                <input class="form-control" name="date_text" value="{{ old('date_text', $entry->date_text) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">J.O. No.</label>
                <input class="form-control" name="jo_number" value="{{ old('jo_number', $entry->jo_number) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Ref No.</label>
                <input class="form-control" name="reference_no" value="{{ old('reference_no', $entry->reference_no) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Billing Amt.</label>
                <input class="form-control" name="billing_amount" type="number" step="0.01" value="{{ old('billing_amount', $entry->billing_amount) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Advances</label>
                <input class="form-control" name="advances_amount" type="number" step="0.01" value="{{ old('advances_amount', $entry->advances_amount) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Adv. Paid</label>
                <input class="form-control" name="advances_paid_amount" type="number" step="0.01" value="{{ old('advances_paid_amount', $entry->advances_paid_amount) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Payment</label>
                <input class="form-control" name="payment_amount" type="number" step="0.01" value="{{ old('payment_amount', $entry->payment_amount) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">WHT</label>
                <input class="form-control" name="wht_amount" type="number" step="0.01" value="{{ old('wht_amount', $entry->wht_amount) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Discount</label>
                <input class="form-control" name="discount_amount" type="number" step="0.01" value="{{ old('discount_amount', $entry->discount_amount) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">VAT</label>
                <input class="form-control" name="vat_amount" type="number" step="0.01" value="{{ old('vat_amount', $entry->vat_amount) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Rebate</label>
                <input class="form-control" name="rebate_amount" type="number" step="0.01" value="{{ old('rebate_amount', $entry->rebate_amount) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Deducted</label>
                <input class="form-control" name="deducted_amount" type="number" step="0.01" value="{{ old('deducted_amount', $entry->deducted_amount) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">CR No.</label>
                <input class="form-control" name="cr_no" value="{{ old('cr_no', $entry->cr_no) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">AR No.</label>
                <input class="form-control" name="ar_no" value="{{ old('ar_no', $entry->ar_no) }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">BL No.</label>
                <input class="form-control" name="bl_no" value="{{ old('bl_no', $entry->bl_no) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Email Sent On</label>
                <input class="form-control" name="email_sent_on" value="{{ old('email_sent_on', $entry->email_sent_on) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Acknowledged</label>
                <input class="form-control" name="email_acknowledged" value="{{ old('email_acknowledged', $entry->email_acknowledged) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Billing Received On</label>
                <input class="form-control" name="billing_received_on" value="{{ old('billing_received_on', $entry->billing_received_on) }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Received By</label>
                <input class="form-control" name="received_by" value="{{ old('received_by', $entry->received_by) }}">
            </div>

            <div class="col-md-8">
                <label class="form-label">Remarks</label>
                <input class="form-control" name="remarks" value="{{ old('remarks', $entry->remarks) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Sort Order</label>
                <input class="form-control" name="sort_order" type="number" min="0" step="1" value="{{ old('sort_order', $entry->sort_order ?? 0) }}">
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('accounting.record-monitoring.index') }}">Cancel</a>
                <button class="btn btn-primary" type="submit">Save Entry</button>
            </div>
        </form>
    </div>
@endsection
