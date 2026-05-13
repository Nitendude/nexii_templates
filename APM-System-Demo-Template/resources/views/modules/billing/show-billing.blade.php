@extends('layouts.employeehub')

@section('content')
<style>
    .billing-paper-wrap {
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        max-width: 8in;
        margin: 0 auto 1.25rem;
    }
    .billing-paper {
        font-family: "Trebuchet MS", Tahoma, sans-serif;
        color: #1f2937;
        letter-spacing: 0.01em;
        padding: 0.15in;
        --billing-meta-offset-x-print: -0.1in;
        --billing-meta-offset-y-print: 0.3in;
        --billing-meta-line-gap-print: 0.18in;
        --billing-desc-offset-y-print: calc(0.45in + 2rem);
        --billing-expenses-offset-y-print: 0.68in;
        --billing-total-offset-y-print: 0.5in;
        --billing-sign-offset-y-print: 0.05in;
        --billing-overlay-shift-x-print: 0.26in;
        --billing-overlay-shift-y-print: 0.12in;
        --billing-top-left-offset-x-print: 1.02in;
        --billing-top-left-offset-y-print: calc(1.97in + 1.15rem);
        --billing-top-right-offset-x-print: 6.26in;
        --billing-top-right-offset-y-print: calc(1.97in + 1rem);
        --billing-vessel-left-offset-x-print: 1.04in;
        --billing-vessel-left-offset-y-print: calc(2.86in + 1.15rem);
        --billing-vessel-right-offset-x-print: 6.26in;
        --billing-vessel-right-offset-y-print: calc(2.86in + 1rem);
        --billing-left-line-1-top-print: 0in;
        --billing-left-line-2-top-print: 0.38in;
        --billing-right-line-1-top-print: 0in;
        --billing-right-line-2-top-print: 0.22in;
        --billing-right-line-3-top-print: 0.56in;
        --billing-vessel-line-1-top-print: 0in;
        --billing-vessel-line-2-top-print: 0.26in;
    }
    .apm-brand-font {
        font-family: "Blippo", "Cooper Black", "Arial Black", Impact, "Trebuchet MS", sans-serif;
        letter-spacing: 0.03em;
    }
    .billing-title {
        font-weight: 800;
        letter-spacing: 0.06em;
        font-size: 1.95rem;
        line-height: 1.05;
    }
    .meta-table td {
        padding: 2px 4px;
        vertical-align: top;
    }
    .meta-table-top td {
        padding: 1px 4px;
        vertical-align: top;
    }
    .meta-table,
    .meta-table-top {
        table-layout: auto;
    }
    .meta-label {
        width: 130px;
        font-weight: 700;
        white-space: nowrap;
    }
    .meta-label-right {
        padding-left: 24px;
        width: 138px;
    }
    .meta-colon {
        width: 10px;
        font-weight: 700;
    }
    .meta-value {
        font-weight: 700;
        text-transform: uppercase;
        word-break: normal;
        overflow-wrap: normal;
        white-space: normal;
    }
    .meta-right-value {
        min-width: 260px;
    }
    .meta-nowrap {
        white-space: nowrap;
    }
    .hr-line {
        border-top: 2px solid #2d2d2d;
        margin: 6px 0;
    }
    .hr-tight {
        border-top: 2px solid #2d2d2d;
        margin: 4px 0 6px;
    }
    .billing-section-title {
        font-weight: 800;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .expense-table {
        width: 100%;
        border-collapse: collapse;
    }
    .expense-table td {
        padding: 2px 0;
        vertical-align: top;
        font-weight: 400;
    }
    .expense-amount {
        width: 170px;
        text-align: right;
        padding-left: 12px;
    }
    .billing-foot td {
        padding: 2px 0;
        font-weight: 700;
    }
    .si-top-check .box {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 1.5px solid #2d2d2d;
        margin-right: 6px;
        vertical-align: middle;
        text-align: center;
        line-height: 12px;
        font-size: 11px;
        font-weight: 800;
    }
    .si-top-check {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        line-height: 1.1;
    }
    .si-grid {
        width: 100%;
        border-collapse: collapse;
        margin-top: 6px;
    }
    .si-grid th,
    .si-grid td {
        border: 1px solid #2d2d2d;
        padding: 6px 8px;
        vertical-align: top;
    }
    .si-grid th {
        text-transform: uppercase;
        font-weight: 800;
        font-size: 0.95rem;
    }
    .si-bottom-grid {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }
    .si-bottom-grid td {
        padding: 2px 0;
    }
    .si-amount-words {
        border-top: 2px solid #2d2d2d;
        border-bottom: 2px solid #2d2d2d;
        padding: 6px 0;
        margin: 8px 0 10px;
        text-align: center;
        font-weight: 800;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }
    .si-sign td {
        padding-top: 10px;
        vertical-align: top;
    }
    .si-sign .line {
        border-top: 2px solid #2d2d2d;
        margin-top: 16px;
        padding-top: 3px;
        font-weight: 800;
    }
    .billing-static {
        font-weight: 700;
    }
    .billing-value {
        font-weight: 700;
        text-transform: uppercase;
    }
    .billing-print-label {
        font-weight: 700;
    }
    .billing-field-text {
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: top;
    }
    .billing-template-left-stack {
        display: none;
    }
    .billing-template-right-stack,
    .billing-template-vessel-left-stack,
    .billing-template-vessel-right-stack {
        display: none;
    }
    @media print {
        @page {
            size: Letter portrait;
            margin: 0.35in;
        }
        .no-print {
            display: none !important;
        }
        .eh-navbar,
        .eh-sidebar,
        .eh-sidebar-col,
        #sidebarOffcanvas,
        .chatbot-fab,
        .chatbot-window {
            display: none !important;
        }
        .container-fluid,
        .container-fluid .row,
        .eh-main-col {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            flex: 0 0 100% !important;
        }
        body {
            background: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .billing-paper-wrap,
        .billing-paper,
        .billing-paper * {
            box-sizing: border-box !important;
        }
        .billing-paper {
            width: 7.55in !important;
            max-width: 7.55in !important;
            margin: 0 auto !important;
            padding: 0 !important;
            font-size: 12.5px !important;
            line-height: 1.3 !important;
            letter-spacing: 0 !important;
        }
        .billing-paper-wrap {
            background: #ffffff !important;
            box-shadow: none;
            border: 0 !important;
            border-radius: 0;
            max-width: none !important;
            padding: 0 !important;
            margin: 0 auto !important;
        }
        .billing-title {
            font-size: 26px !important;
            letter-spacing: 0.03em;
        }
        .meta-label {
            width: 104px !important;
            font-size: 12px !important;
        }
        .meta-colon {
            width: 8px !important;
        }
        .meta-table td,
        .meta-table-top td {
            padding: 2px 3px !important;
        }
        .meta-label-right {
            padding-left: 24px !important;
            width: 118px !important;
        }
        .meta-right-value {
            min-width: 230px !important;
        }
        .small {
            font-size: 11px !important;
        }
        .hr-line,
        .hr-tight {
            margin: 7px 0 !important;
        }
        .expense-table td {
            padding: 1px 0 !important;
            line-height: 1.25 !important;
            font-size: 14px !important;
        }
        .expense-amount {
            width: 130px !important;
            font-size: 14px !important;
            font-weight: 800 !important;
        }
        .billing-foot td {
            font-size: 12.5px !important;
        }
        .border-top {
            margin-top: 14px !important;
        }
        .billing-paper-wrap,
        .billing-paper {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .meta-nowrap {
            white-space: nowrap !important;
        }
        .si-top-check .box {
            width: 13px !important;
            height: 13px !important;
            line-height: 11px !important;
            font-size: 10px !important;
        }
        .si-grid th,
        .si-grid td {
            padding: 4px 6px !important;
        }
        body.template-print-active .billing-generated-only,
        body.template-print-active .billing-static,
        body.template-print-active .no-print-placeholder {
            display: none !important;
        }
        body.template-print-active .meta-label,
        body.template-print-active .meta-colon {
            display: table-cell !important;
            visibility: hidden !important;
        }
        body.template-print-active .hr-line,
        body.template-print-active .hr-tight,
        body.template-print-active .si-sign .line,
        body.template-print-active .border-top {
            border: 0 !important;
        }
        body.template-print-active .meta-table-top,
        body.template-print-active .meta-table {
            width: 100% !important;
            table-layout: fixed !important;
        }
        body.template-print-active .billing-top-meta-table,
        body.template-print-active .billing-vessel-meta-table {
            visibility: hidden !important;
            table-layout: fixed !important;
            overflow: hidden !important;
        }
        body.template-print-active .billing-top-meta-table {
            height: 0.78in !important;
            margin-bottom: 0 !important;
        }
        body.template-print-active .billing-vessel-meta-table {
            height: 0.52in !important;
            margin-bottom: 0 !important;
        }
        body.template-print-active .billing-top-meta-table tr {
            height: 0.24in !important;
        }
        body.template-print-active .billing-vessel-meta-table tr {
            height: 0.26in !important;
        }
        body.template-print-active .billing-top-meta-table td,
        body.template-print-active .billing-vessel-meta-table td {
            padding: 0 !important;
            overflow: hidden !important;
        }
        body.template-print-active .billing-top-meta-table .billing-value,
        body.template-print-active .billing-vessel-meta-table .billing-value {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: clip !important;
        }
        body.template-print-active .meta-table-top td,
        body.template-print-active .meta-table td {
            padding: 0 !important;
            line-height: 1 !important;
        }
        body.template-print-active .meta-table-top {
            margin-top: 0 !important;
            margin-bottom: 0.02in !important;
        }
        body.template-print-active .billing-meta-left-move {
            transform: translate(var(--billing-meta-offset-x-print), var(--billing-meta-offset-y-print)) !important;
        }
        body.template-print-active .meta-table-top tr:nth-child(2) .billing-meta-left-move {
            transform: translate(
                var(--billing-meta-offset-x-print),
                calc(var(--billing-meta-offset-y-print) + var(--billing-meta-line-gap-print))
            ) !important;
        }
        body.template-print-active .meta-table-top tr:nth-child(3) .billing-meta-left-move {
            transform: translate(
                var(--billing-meta-offset-x-print),
                calc(var(--billing-meta-offset-y-print) + (var(--billing-meta-line-gap-print) * 2))
            ) !important;
        }
        body.template-print-active .meta-table-top tr > td:nth-child(1) { width: 11% !important; }
        body.template-print-active .meta-table-top tr > td:nth-child(2) { width: 2% !important; }
        body.template-print-active .meta-table-top tr > td:nth-child(3) { width: 50% !important; }
        body.template-print-active .meta-table-top tr > td:nth-child(4) { width: 11% !important; }
        body.template-print-active .meta-table-top tr > td:nth-child(5) { width: 2% !important; }
        body.template-print-active .meta-table-top tr > td:nth-child(6) { width: 24% !important; }
        body.template-print-active .billing-value {
            font-size: 10px !important;
            line-height: 1.18 !important;
            letter-spacing: 0 !important;
        }
        body.template-print-active .billing-date-value {
            text-align: right !important;
            padding-right: 0.04in !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            line-height: 1.05 !important;
            display: table-cell !important;
            vertical-align: top !important;
            transform: none !important;
        }
        body.template-print-active .billing-long-field {
            white-space: nowrap !important;
        }
        body.template-print-active .billing-field-text {
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }
        body.template-print-active .billing-top-left-field {
            max-width: 3.35in !important;
        }
        body.template-print-active .billing-address-value {
            font-size: 9px !important;
        }
        body.template-print-active .billing-hide-left-template {
            visibility: hidden !important;
        }
        body.template-print-active .billing-hide-right-template {
            visibility: hidden !important;
        }
        body.template-print-active .billing-hide-vessel-template {
            visibility: hidden !important;
        }
        body.template-print-active .billing-template-left-stack {
            display: block !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            transform: translate(var(--billing-top-left-offset-x-print), var(--billing-top-left-offset-y-print)) !important;
            width: 3.8in !important;
            z-index: 3 !important;
        }
        body.template-print-active .billing-template-left-stack .billing-left-line {
            display: block !important;
            position: absolute !important;
            left: 0 !important;
            font-size: 12px !important;
            line-height: 1.08 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0 !important;
            width: 100% !important;
        }
        body.template-print-active .billing-template-left-stack .billing-left-line:first-child {
            top: var(--billing-left-line-1-top-print) !important;
            display: -webkit-box !important;
            -webkit-box-orient: vertical !important;
            -webkit-line-clamp: 2 !important;
            white-space: normal !important;
            overflow: hidden !important;
            text-overflow: clip !important;
            font-size: 11px !important;
            line-height: 0.98 !important;
            height: 1.96em !important;
            padding-right: 0.08in !important;
            overflow-wrap: break-word !important;
            word-break: normal !important;
        }
        body.template-print-active .billing-template-left-stack .billing-left-line.billing-address-line {
            top: var(--billing-left-line-2-top-print) !important;
            font-size: 10px !important;
            display: -webkit-box !important;
            -webkit-box-orient: vertical !important;
            -webkit-line-clamp: 2 !important;
            white-space: normal !important;
            overflow: hidden !important;
            text-overflow: clip !important;
            line-height: 1.02 !important;
            height: 2.04em !important;
            padding-right: 0.08in !important;
            overflow-wrap: break-word !important;
            word-break: normal !important;
        }
        body.template-print-active .billing-template-right-stack,
        body.template-print-active .billing-template-vessel-left-stack,
        body.template-print-active .billing-template-vessel-right-stack {
            display: block !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            z-index: 3 !important;
        }
        body.template-print-active .billing-template-right-stack {
            transform: translate(var(--billing-top-right-offset-x-print), var(--billing-top-right-offset-y-print)) !important;
            width: 2.35in !important;
        }
        body.template-print-active .billing-template-vessel-left-stack {
            transform: translate(var(--billing-vessel-left-offset-x-print), var(--billing-vessel-left-offset-y-print)) !important;
            width: 3.8in !important;
        }
        body.template-print-active .billing-template-vessel-right-stack {
            transform: translate(var(--billing-vessel-right-offset-x-print), var(--billing-vessel-right-offset-y-print)) !important;
            width: 2.1in !important;
        }
        body.template-print-active .billing-template-right-stack .billing-left-line,
        body.template-print-active .billing-template-vessel-left-stack .billing-left-line,
        body.template-print-active .billing-template-vessel-right-stack .billing-left-line {
            display: block !important;
            position: absolute !important;
            left: 0 !important;
            font-size: 12px !important;
            line-height: 1.08 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0 !important;
            width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        body.template-print-active .billing-template-right-stack .billing-left-line:nth-child(1) {
            top: var(--billing-right-line-1-top-print) !important;
        }
        body.template-print-active .billing-template-right-stack .billing-left-line:nth-child(2) {
            top: var(--billing-right-line-2-top-print) !important;
        }
        body.template-print-active .billing-template-right-stack .billing-left-line:nth-child(3) {
            top: var(--billing-right-line-3-top-print) !important;
        }
        body.template-print-active .billing-template-right-stack .billing-left-line:last-child {
            top: var(--billing-right-line-3-top-print) !important;
            font-size: 9px !important;
            line-height: 0.98 !important;
            display: -webkit-box !important;
            -webkit-box-orient: vertical !important;
            -webkit-line-clamp: 2 !important;
            white-space: normal !important;
            overflow: hidden !important;
            text-overflow: clip !important;
            height: 1.96em !important;
            padding-right: 0.04in !important;
            overflow-wrap: break-word !important;
            word-break: normal !important;
        }
        body.template-print-active .billing-template-vessel-left-stack .billing-left-line:nth-child(1),
        body.template-print-active .billing-template-vessel-right-stack .billing-left-line:nth-child(1) {
            top: var(--billing-vessel-line-1-top-print) !important;
        }
        body.template-print-active .billing-template-vessel-left-stack .billing-left-line:nth-child(2),
        body.template-print-active .billing-template-vessel-right-stack .billing-left-line:nth-child(2) {
            top: var(--billing-vessel-line-2-top-print) !important;
        }
        body.template-print-active .meta-table-top tr:first-child td:nth-child(3) .billing-value {
            display: inline-block !important;
            transform: translateY(0) !important;
        }
        body.template-print-active .meta-table-top tr:nth-child(2) td:nth-child(3) .billing-value,
        body.template-print-active .meta-table-top tr:nth-child(3) td:nth-child(3) .billing-value {
            display: inline-block !important;
            transform: translateY(0) !important;
        }
        body.template-print-active .billing-desc-block {
            margin-top: 0.12in !important;
            margin-bottom: 0.02in !important;
            transform: translateY(var(--billing-desc-offset-y-print)) !important;
        }
        body.template-print-active .billing-desc-block .small {
            font-size: 10px !important;
            line-height: 1.1 !important;
        }
        body.template-print-active .billing-desc-block .col-md-5 .small {
            margin-bottom: 0 !important;
        }
        body.template-print-active .billing-desc-block .col-md-5 .small:last-child {
            margin-bottom: 0.38in !important;
        }
        body.template-print-active .billing-desc-block .row {
            --bs-gutter-x: 0 !important;
            --bs-gutter-y: 0 !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            gap: 0.18in !important;
            align-items: flex-start !important;
        }
        body.template-print-active .billing-desc-block .col-md-5 {
            flex: 0 0 55% !important;
            max-width: 55% !important;
            width: 55% !important;
            padding: 0 !important;
        }
        body.template-print-active .billing-desc-block .col-md-4 {
            flex: 0 0 35% !important;
            max-width: 35% !important;
            width: 35% !important;
            padding: 0 !important;
            transform: translateY(-0.12in) !important;
        }
        body.template-print-active .billing-print-label {
            display: inline !important;
            visibility: visible !important;
            font-weight: 700 !important;
        }
        body.template-print-active .billing-expenses-block {
            transform: translateY(var(--billing-expenses-offset-y-print)) !important;
        }
        body.template-print-active .billing-expenses-block .billing-section-title {
            display: block !important;
            font-size: 14px !important;
            line-height: 1.18 !important;
            margin: 0 0 3px !important;
            font-weight: 900 !important;
            letter-spacing: 0 !important;
            text-transform: uppercase !important;
            color: #000 !important;
            -webkit-text-stroke: 0.18px #000 !important;
            text-shadow: 0 0 0 #000 !important;
        }
        body.template-print-active .billing-expenses-block {
            margin-top: 0.08in !important;
            height: 4.25in !important;
            overflow: visible !important;
            margin-bottom: 0 !important;
        }
        body.template-print-active .expense-table td {
            padding: 1.5px 0 !important;
            line-height: 1.18 !important;
            font-size: 14px !important;
            font-weight: 700 !important;
        }
        body.template-print-active .expense-amount {
            width: 1.45in !important;
            font-size: 14px !important;
            font-weight: 800 !important;
        }
        body.template-print-active .billing-total-block {
            position: absolute !important;
            left: 0.06in !important;
            right: 0.06in !important;
            bottom: 0.34in !important;
            margin: 0 !important;
            transform: none !important;
        }
        body.template-print-active .billing-total-block.billing-total-has-advance {
            bottom: 0.68in !important;
        }
        body.template-print-active .billing-total-block .billing-static {
            display: inline !important;
            visibility: visible !important;
            font-weight: 800 !important;
        }
        body.template-print-active .billing-total-block.billing-total-has-advance .billing-final-total-label,
        body.template-print-active .billing-total-block.billing-total-has-advance .billing-final-total-value,
        body.template-print-active .billing-total-block.billing-total-has-advance .billing-words-label {
            display: none !important;
            visibility: hidden !important;
        }
        body.template-print-active .billing-total-block .billing-foot td:first-child {
            width: 1.85in !important;
            text-align: left !important;
            white-space: nowrap !important;
        }
        body.template-print-active .billing-total-block.billing-total-has-advance .billing-totals-table td:first-child {
            width: 5.75in !important;
            text-align: right !important;
            padding-right: 0.12in !important;
        }
        body.template-print-active .billing-total-block.billing-total-has-advance .billing-totals-table td:last-child {
            width: 1.35in !important;
            text-align: right !important;
        }
        body.template-print-active .billing-total-block .billing-totals-table td.billing-advance-label-cell:first-child {
            width: 5.75in !important;
        }
        body.template-print-active .billing-total-block.billing-total-has-advance .billing-words-table {
            transform: translateY(0.13in) !important;
        }
        body.template-print-active .billing-foot td {
            padding: 0 !important;
            line-height: 1.1 !important;
            font-size: 13px !important;
        }
        body.template-print-active .billing-amount-words {
            text-align: center !important;
            font-size: 9px !important;
            font-weight: 800 !important;
            letter-spacing: 0 !important;
            white-space: nowrap !important;
        }
        body.template-print-active .billing-sign {
            position: absolute !important;
            left: 0.06in !important;
            right: 0.06in !important;
            bottom: var(--billing-sign-offset-y-print) !important;
            margin-top: 0 !important;
            transform: none !important;
        }
        body.template-print-active .billing-sign .fw-semibold {
            font-size: 12px !important;
            font-weight: 800 !important;
        }
        body.template-print-active .billing-paper {
            position: relative !important;
            width: 7.8in !important;
            max-width: 7.8in !important;
            min-height: auto !important;
            height: 10.3in !important;
            padding: 1.45in 0.06in 0.06in !important;
            font-size: 10.8px !important;
            line-height: 1.2 !important;
            letter-spacing: 0 !important;
            transform: translate(var(--billing-overlay-shift-x-print), var(--billing-overlay-shift-y-print)) !important;
            transform-origin: top left !important;
        }
    }
</style>

@php
    $data = $statement->data ?? [];
    $isService = false;
    $isDraft = (bool) ($isDraft ?? false);
    $nonDesc = $data['non_receipted_desc'] ?? [];
    $nonAmt = $data['non_receipted_amount'] ?? [];
    $recDesc = $data['receipted_desc'] ?? [];
    $recAmt = $data['receipted_amount'] ?? [];

    $fmt = function ($amount) {
        $num = is_numeric($amount) ? (float) $amount : 0;
        return number_format($num, 2);
    };
    $serviceVatAmount = is_numeric($data['si_less_vat'] ?? null)
        ? (float) $data['si_less_vat']
        : (is_numeric($data['si_vat'] ?? null) ? (float) $data['si_vat'] : 0);

    $nonTotal = 0;
    foreach ($nonAmt as $amt) {
        $nonTotal += is_numeric($amt) ? (float) $amt : 0;
    }

    $recTotal = 0;
    foreach ($recAmt as $amt) {
        $recTotal += is_numeric($amt) ? (float) $amt : 0;
    }

    $grandTotal = isset($adjustedGrandTotal) && is_numeric($adjustedGrandTotal)
        ? (float) $adjustedGrandTotal
        : (is_numeric($data['grand_total'] ?? null) ? (float) $data['grand_total'] : ($nonTotal + $recTotal));
    $advanceBalanceAfterBilling = isset($advanceBalanceAfterBilling) && is_numeric($advanceBalanceAfterBilling)
        ? (float) $advanceBalanceAfterBilling
        : 0.0;
    $advanceTotal = isset($advanceTotal) && is_numeric($advanceTotal)
        ? (float) $advanceTotal
        : (float) (($advanceAvailableForBilling ?? 0) ?: (($advanceDeduction ?? 0) + ($advanceOverpayment ?? 0)));
    $advanceAvailableForBilling = isset($advanceAvailableForBilling) && is_numeric($advanceAvailableForBilling)
        ? (float) $advanceAvailableForBilling
        : (float) (($advanceDeduction ?? 0) + ($advanceOverpayment ?? 0));
    $statementDate = !empty($data['statement_date'])
        ? \Carbon\Carbon::parse($data['statement_date'])->format('F d, Y')
        : optional($statement->created_at)->format('F d, Y');
    $isConverge = str_contains(strtoupper(($data['bill_to'] ?? '') . ' ' . ($data['bill_business_style'] ?? '')), 'CONVERGE');
    $pdfUrl = !$isDraft ? route('billing.pdf', $statement) : null;
    $gmailSubject = rawurlencode('Billing Statement #' . ($statement->statement_no ?? ''));
    $gmailBody = rawurlencode(
        "Good day,\n\nPlease see the billing PDF package for JO " . ($data['job_ref_no'] ?? '-') . ".\n\nDownload PDF with attachments: " . ($pdfUrl ?? '') . "\n\nNote: Gmail cannot auto-attach files from a website link. Please download the PDF package and attach it before sending.\n\nThank you."
    );
    $gmailUrl = !$isDraft ? "https://mail.google.com/mail/?view=cm&fs=1&su={$gmailSubject}&body={$gmailBody}" : null;
@endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 no-print">
        <div>
            <h2 class="mb-1">{{ $isDraft ? 'Draft ' : '' }}{{ $isService ? 'Service Invoice' : 'Billing Statement' }} #{{ $statement->statement_no }}</h2>
            <p class="text-muted mb-0">
                {{ $isDraft ? 'Draft preview only. This document has not been saved to the database.' : 'Formatted output based on your document template.' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($isDraft)
                <button class="btn btn-outline-secondary" type="button" onclick="window.close()">Close Draft</button>
            @else
                <a class="btn btn-outline-secondary" href="{{ $isService ? route('billing.service-invoices.documents') : route('billing.documents') }}">Back to Documents</a>
                <a class="btn btn-outline-primary" href="{{ $isService ? route('billing.service-invoices') : route('billing') }}">Back</a>
                <a class="btn btn-outline-warning" href="{{ route('billing.edit', $statement) }}">Edit</a>
                <a class="btn btn-outline-success" href="{{ $pdfUrl }}">Download PDF + Attachments</a>
                <a class="btn btn-outline-danger" href="{{ $gmailUrl }}" target="_blank" rel="noopener">Email via Gmail</a>
            @endif
            <button class="btn btn-primary" type="button" data-print-mode="plain">Print Plain Paper</button>
            <button class="btn btn-outline-primary" type="button" data-print-mode="template">Print With Template</button>
        </div>
    </div>

    @if(session('status') === 'billing-attachments-uploaded')
        <div class="alert alert-success no-print">Scanned document uploaded successfully.</div>
    @endif

    @if(!$isDraft)
        @include('partials.scanner-upload', [
            'scannerId' => 'billingStatementScanner' . $statement->id,
            'modalTitle' => 'Scan Document to Billing Statement #' . $statement->statement_no,
            'description' => 'Choose a connected scanner, scan the document, and APM will save it directly to this Billing Statement.',
            'uploadUrl' => route('billing.attachments.store', $statement),
            'documentLabel' => 'billing-statement-' . ($statement->statement_no ?? $statement->id),
        ])
    @endif

    @if(!$isDraft && $statement->attachments->count())
        <div class="alert alert-light border no-print">
            <div class="fw-semibold mb-1">Attachments included in downloadable PDF</div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($statement->attachments as $attachment)
                    <a class="badge text-bg-light border text-decoration-none" href="{{ \Illuminate\Support\Facades\Storage::url($attachment->path) }}" target="_blank" rel="noopener">
                        {{ $attachment->filename }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="billing-paper-wrap p-4 p-md-5">
        <div class="billing-paper">
            <div class="text-center mb-2 billing-generated-only">
                <div class="fw-bold text-uppercase fs-4 apm-brand-font">APM Customs Brokerage</div>
                <div class="small fw-semibold">Lot 7F 2&3 Rodriguez Compound, Aurenina Village, San Dionisio, 1700 City of Paranaque</div>
                <div class="small fw-semibold">NCR, Fourth District, Philippines</div>
                <div class="small fw-semibold">Tel. Nos.: (02) 8682-6845, 8696-7798</div>
                <div class="small fw-semibold">VAT Reg. TIN: 120-291-938-00000</div>
            </div>

            <div class="d-flex justify-content-center align-items-center mb-3 billing-generated-only">
                <div class="billing-title fs-3 apm-brand-font">{{ $isDraft ? 'DRAFT ' : '' }}{{ $isService ? 'SERVICE INVOICE' : 'BILLING STATEMENT' }}</div>
            </div>

            @if(!$isService)
                <div class="billing-template-left-stack">
                    <span class="billing-left-line">{{ $data['bill_to'] ?? '-' }}</span>
                    <span class="billing-left-line billing-address-line">{{ $data['bill_address'] ?? '-' }}</span>
                </div>
                <div class="billing-template-right-stack">
                    <span class="billing-left-line">{{ $statementDate }}</span>
                    <span class="billing-left-line">{{ $data['bill_tin'] ?? '-' }}</span>
                    <span class="billing-left-line">{{ $data['bill_business_style'] ?? '-' }}</span>
                </div>
                <div class="billing-template-vessel-left-stack">
                    <span class="billing-left-line">{{ $data['vessel_voyage'] ?? '-' }}</span>
                    <span class="billing-left-line">{{ $data['bl_no'] ?? '-' }}</span>
                </div>
                <div class="billing-template-vessel-right-stack">
                    <span class="billing-left-line">{{ ($data['vol_meas'] ?? '-') . (!empty($data['vol_meas_unit']) ? ' ' . strtoupper($data['vol_meas_unit']) : '') }}</span>
                    <span class="billing-left-line">{{ $data['job_ref_no'] ?? '-' }}</span>
                </div>
            @endif

            <table class="meta-table-top w-100 mb-2 billing-top-meta-table">
                @if($isService)
                    <tr>
                        <td class="meta-label">Sold To</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value billing-value" style="width:48%;">{{ $data['bill_to'] ?? '-' }}</td>
                        <td class="meta-label meta-label-right">Date</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value meta-nowrap billing-value">{{ $statementDate }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Registered Name</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $data['si_registered_name'] ?? '-' }}</td>
                        <td class="meta-label meta-label-right">Sales Type</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value">
                            @php $salesType = strtolower($data['si_sales_type'] ?? ''); @endphp
                            <div class="si-top-check">
                                <div class="meta-nowrap"><span class="box">{{ $salesType === 'cash' ? '✓' : '' }}</span> CASH SALES</div>
                                <div class="meta-nowrap"><span class="box">{{ $salesType === 'charge' ? '✓' : '' }}</span> CHARGE SALES</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label">TIN</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $data['bill_tin'] ?? '-' }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="meta-label">Business Address</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value billing-value">{{ $data['bill_address'] ?? '-' }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @else
                    <tr>
                        <td class="meta-label">Bill To</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value billing-value billing-long-field billing-meta-left-move billing-hide-left-template" style="width:48%;"><span class="billing-field-text billing-top-left-field">{{ $data['bill_to'] ?? '-' }}</span></td>
                        <td class="meta-label meta-label-right">Date</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value meta-nowrap billing-value billing-date-value billing-hide-right-template">{{ $statementDate }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Address</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value billing-value billing-long-field billing-address-value billing-meta-left-move billing-hide-left-template"><span class="billing-field-text billing-top-left-field">{{ $data['bill_address'] ?? '-' }}</span></td>
                        <td class="meta-label meta-label-right">TIN</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value meta-nowrap billing-value billing-hide-right-template">{{ $data['bill_tin'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="meta-label meta-label-right">Bus. Style</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value billing-value billing-long-field billing-hide-right-template">{{ $data['bill_business_style'] ?? '-' }}</td>
                    </tr>
                @endif
            </table>

            @if(!$isService)
                <div class="hr-tight"></div>
                <table class="meta-table-top w-100 billing-vessel-meta-table">
                    <tr>
                        <td class="meta-label">Vessel/Voy.</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value billing-value billing-hide-vessel-template" style="width:48%;">{{ $data['vessel_voyage'] ?? '-' }}</td>
                                <td class="meta-label meta-label-right">Vol./Meas.</td>
                                <td class="meta-colon">:</td>
                                <td class="meta-value meta-right-value meta-nowrap billing-value billing-hide-vessel-template">
                                    {{ $data['vol_meas'] ?? '-' }}{{ !empty($data['vol_meas_unit']) ? ' ' . strtoupper($data['vol_meas_unit']) : '' }}
                                </td>
                            </tr>
                    <tr>
                        <td class="meta-label">B/L No.</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value billing-value billing-hide-vessel-template">{{ $data['bl_no'] ?? '-' }}</td>
                        <td class="meta-label meta-label-right meta-nowrap">Job Ref. No.</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value meta-right-value meta-nowrap billing-value billing-hide-vessel-template">{{ $data['job_ref_no'] ?? '-' }}</td>
                    </tr>
                </table>
                <div class="hr-tight mb-3"></div>
            @endif

            @if($isService)
                <table class="si-grid">
                    <thead>
                        <tr>
                            <th style="width:48%;">Item Description / Nature of Service</th>
                            <th style="width:16%;" class="text-center">Quantity</th>
                            <th style="width:18%;" class="text-center">Unit Cost</th>
                            <th style="width:18%;" class="text-center">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height:110px;">
                            <td>{{ strtoupper($data['si_item_description'] ?? 'BROKERAGE FEE AS PER CAO 1-2001') }}</td>
                            <td class="text-center">
                                {{ $fmt($data['si_quantity'] ?? 0) }}{{ !empty($data['vol_meas_unit']) ? ' ' . strtoupper($data['vol_meas_unit']) : '' }}
                            </td>
                            <td class="text-end">{{ $fmt($data['si_unit_cost'] ?? 0) }}</td>
                            <td class="text-end">{{ $fmt($data['si_amount'] ?? 0) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mb-3 mt-2">
                    <div class="small fw-bold">DESCRIPTION : <span class="fw-semibold">{{ $data['description'] ?? '-' }}</span></div>
                    <div class="small fw-bold">SHIPPER'S NAME : <span class="fw-semibold">{{ $data['shipper_name'] ?? '-' }}</span></div>
                    <div class="row g-2">
                        <div class="col-md-5 small fw-bold">INVOICE NO. : <span class="fw-semibold">{{ $data['invoice_no'] ?? '-' }}</span></div>
                        <div class="col-md-4">
                            <div class="small fw-bold">PORT : <span class="fw-semibold">{{ $data['port'] ?? '-' }}</span></div>
                            <div class="small fw-bold mt-1">CTNR. NO : <span class="fw-semibold">{{ $data['container_no'] ?? '-' }}</span></div>
                        </div>
                    </div>
                </div>

                <table class="si-bottom-grid mb-2">
                    <tr>
                        <td style="width:50%;">
                            <div>VATable Sales</div>
                            <div>VAT</div>
                            <div>Zero Rated Sales</div>
                            <div>VAT-Exempt Sales</div>
                        </td>
                        <td style="width:50%;">
                            <div class="d-flex justify-content-between"><span>Total Sales (VAT Inclusive)</span><strong>{{ $fmt($data['si_total_sales'] ?? 0) }}</strong></div>
                            <div class="d-flex justify-content-between"><span>Less: VAT</span><strong>{{ $fmt($serviceVatAmount) }}</strong></div>
                            <div class="d-flex justify-content-between"><span>Amount: Net of VAT</span><strong>{{ $fmt($data['si_amount_net_vat'] ?? 0) }}</strong></div>
                            <div class="d-flex justify-content-between"><span>Less: Withholding Tax</span><strong>({{ $fmt($data['si_less_withholding_tax'] ?? 0) }})</strong></div>
                            <div class="d-flex justify-content-between"><span>Add: VAT</span><strong>{{ $fmt($serviceVatAmount) }}</strong></div>
                            <div class="d-flex justify-content-between border-top border-dark mt-1 pt-1"><span class="fw-bold">TOTAL AMOUNT DUE</span><strong>{{ $fmt($data['si_total_amount_due'] ?? $grandTotal) }}</strong></div>
                        </td>
                    </tr>
                </table>

                <div class="si-amount-words">{{ strtoupper($data['amount_in_words'] ?? '-') }}</div>

                <table class="w-100 si-sign">
                    <tr>
                        <td style="width:33%;">
                            <div>Prepared By:</div>
                            <div class="line">{{ strtoupper($data['prepared_by'] ?? '-') }}</div>
                        </td>
                        <td style="width:33%;">
                            <div>Checked By:</div>
                            <div class="line">{{ strtoupper($data['approved_by'] ?? '-') }}</div>
                        </td>
                        <td style="width:34%;">
                            <div>Received By:</div>
                            <div class="line">{{ strtoupper($data['received_by'] ?? '-') }}</div>
                            <div class="mt-1 text-center fw-semibold">Cashier / Authorized Representative</div>
                        </td>
                    </tr>
                </table>
            @else
                <div class="mb-3 billing-desc-block">
                    <div class="small fw-bold"><span class="billing-print-label">DESCRIPTION : </span><span class="fw-semibold billing-value">{{ $data['description'] ?? '-' }}</span></div>
                    <div class="small fw-bold"><span class="billing-print-label">SHIPPER'S NAME : </span><span class="fw-semibold billing-value">{{ $data['shipper_name'] ?? '-' }}</span></div>
                    <div class="row g-2">
                        <div class="col-md-5 small fw-bold"><span class="billing-print-label">INVOICE NO. : </span><span class="fw-semibold billing-value">{{ $data['invoice_no'] ?? '-' }}</span></div>
                        <div class="col-md-4">
                            <div class="small fw-bold"><span class="billing-print-label">PORT: </span><span class="fw-semibold billing-value">{{ $data['port'] ?? '-' }}</span></div>
                            <div class="small fw-bold mt-1"><span class="billing-print-label">CONTAINER NO.: </span><span class="fw-semibold billing-value">{{ $data['container_no'] ?? '-' }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="billing-expenses-block">
                    <div class="billing-section-title mb-1">I. Brokerage Reimbursable Expenses:</div>
                    @if(!$isConverge)
                        <div class="billing-section-title mb-1">A. Non-Receipted Charges</div>
                        <table class="expense-table mb-3">
                            <tbody>
                                @forelse($nonDesc as $index => $desc)
                                    @if(!empty(trim((string) $desc)) || !empty($nonAmt[$index]))
                                        <tr>
                                            <td>{{ strtoupper($desc) }}</td>
                                            <td class="expense-amount">{{ $fmt($nonAmt[$index] ?? 0) }}</td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td>-</td>
                                        <td class="expense-amount">0.00</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif

                    @if(count($recDesc) > 0)
                        <div class="billing-section-title mb-1">B. Receipted Charges</div>
                        <table class="expense-table mb-3">
                            <tbody>
                                @foreach($recDesc as $index => $desc)
                                    @if(!empty(trim((string) $desc)) || !empty($recAmt[$index]))
                                        <tr>
                                            <td>{{ strtoupper($desc) }}</td>
                                            <td class="expense-amount">{{ $fmt($recAmt[$index] ?? 0) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endif

            @if(!$isService)
                @php
                    $hasAdvanceAdjustment = (!empty($advanceDeduction) && $advanceDeduction > 0) || (!empty($advanceOverpayment) && $advanceOverpayment > 0);
                @endphp
                <div class="billing-total-block {{ $hasAdvanceAdjustment ? 'billing-total-has-advance' : '' }}">
                <div class="hr-line mt-4"></div>
                <table class="billing-foot billing-totals-table w-100 mb-2">
                    @if((!empty($advanceDeduction) && $advanceDeduction > 0) || (!empty($advanceOverpayment) && $advanceOverpayment > 0))
                    <tr>
                        <td style="width: 170px;"><span class="billing-static">SUBTOTAL</span></td>
                        <td class="text-end billing-value">{{ $fmt($baseGrandTotal ?? $grandTotal) }}</td>
                    </tr>
                    @endif
                    @if(!empty($advanceDeduction) && $advanceDeduction > 0)
                    <tr>
                        <td class="billing-advance-label-cell"><span class="billing-static">LESS: ADVANCES OF PHP {{ $fmt($advanceTotal) }}</span></td>
                        <td class="text-end billing-value">({{ $fmt($advanceAvailableForBilling) }})</td>
                    </tr>
                    @endif
                    @if(!empty($advanceOverpayment) && $advanceOverpayment > 0)
                    <tr>
                        <td style="width: 170px;"><span class="billing-static">OVERPAYMENT</span></td>
                        <td class="text-end billing-value">({{ $fmt($advanceOverpayment) }})</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="width: 170px;"><span class="billing-static billing-final-total-label">TOTAL AMOUNT</span></td>
                        <td class="text-end billing-value billing-final-total-value">{{ $hasAdvanceAdjustment ? '' : 'PHP ' . $fmt($grandTotal) }}</td>
                    </tr>
                </table>
                <div class="hr-line my-2"></div>
                <table class="billing-foot billing-words-table w-100 mb-2">
                    <tr>
                        <td style="width: 170px;"><span class="billing-static billing-words-label">AMOUNT IN WORDS:</span></td>
                        <td class="text-center billing-amount-words billing-value">{{ strtoupper($adjustedAmountInWords ?? $data['amount_in_words'] ?? '-') }}</td>
                    </tr>
                </table>
                <div class="hr-line mb-3"></div>
                </div>

                <div class="row g-3 mt-4 billing-sign">
                    <div class="col-md-4">
                        <div class="fw-bold billing-static">Prepared by:</div>
                        <div class="mt-4 border-top border-dark pt-1 fw-semibold billing-value">{{ strtoupper($data['prepared_by'] ?? '-') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-bold billing-static">Approved by:</div>
                        <div class="mt-4 border-top border-dark pt-1 fw-semibold billing-value">{{ strtoupper($data['approved_by'] ?? '-') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="fw-bold billing-static">Received by:</div>
                        <div class="mt-4 border-top border-dark pt-1 fw-semibold billing-value">{{ strtoupper($data['received_by'] ?? '-') }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const plainButton = document.querySelector('[data-print-mode="plain"]');
        const templateButton = document.querySelector('[data-print-mode="template"]');
        const body = document.body;

        const resetTemplateMode = function () {
            body.classList.remove('template-print-active');
        };

        window.addEventListener('afterprint', function () {
            resetTemplateMode();
        });

        if (plainButton) {
            plainButton.addEventListener('click', function () {
                resetTemplateMode();
                window.print();
            });
        }

        if (templateButton) {
            templateButton.addEventListener('click', function () {
                body.classList.add('template-print-active');
                window.print();
            });
        }
    })();
</script>
@endpush
