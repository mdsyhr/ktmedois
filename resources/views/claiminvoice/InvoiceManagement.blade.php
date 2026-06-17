<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KTM eDOIS - Submit Invoice Claim</title>
    <link rel="stylesheet" href="{{ asset('css/ktmb.css') }}">
</head>

{{--
    ROUTE EXPECTS:
        $do           — DeliveryOrder model (with Cust_Name, Staff_Name eager-loaded via joins)
        $items        — Collection of ItemDetail for this DO
        $taxRate      — float, e.g. 0.06 (fetched from tax_settings table or config)
        $taxLabel     — string, e.g. "SST (6%)"
--}}

<style>
    :root {
        --primary-blue: #003399;
        --accent-yellow: #FFCC00;
        --white: #ffffff;
        --light-gray: #f4f7f6;
        --text-dark: #333;
        --border-color: #ddd;
        --error-red: #d32f2f;
        --success-green: #2e7d32;
        --warning-orange: #ff9800;
    }

    * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }

    body { background-color: var(--light-gray); color: var(--text-dark); min-height: 100vh; display: flex; flex-direction: column; }

   /* --- NEW NAV --- */
    .ktmb-nav {
        background-color: #002266;
        display: flex;
        align-items: stretch;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .ktmb-nav a {
        display: flex; align-items: center; gap: 8px;
        padding: 14px 28px; color: rgba(255,255,255,0.85);
        text-decoration: none; font-weight: 500; font-size: 0.95rem;
        border-bottom: 3px solid transparent; transition: all 0.3s;
    }
    .ktmb-nav a:hover {
        background-color: rgba(255,255,255,0.1); color: #ffffff;
    }
    .ktmb-nav a.active {
        color: #FFCC00; border-bottom-color: #FFCC00;
        background-color: rgba(255,204,0,0.08);
    }

    /* --- MAIN --- */
    main { flex: 1; padding: 40px; display: flex; justify-content: center; align-items: flex-start; }

    .container { background-color: var(--white); width: 100%; max-width: 860px; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 5px solid var(--accent-yellow); }

    /* --- BREADCRUMB --- */
    .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 0.88rem; color: #666; margin-bottom: 20px; }
    .breadcrumb a { color: var(--primary-blue); text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .breadcrumb .sep { color: #bbb; }

    h2 { color: var(--primary-blue); margin-bottom: 6px; }
    h3 { color: var(--primary-blue); margin-bottom: 14px; font-size: 1.05rem; }
    .section-divider { border: none; border-top: 1px solid #eee; margin: 24px 0; }

    /* --- DO DETAILS PANEL --- */
    .do-details-panel {
        background: #f0f4ff; border: 1px solid #c0d0ff;
        border-left: 4px solid var(--primary-blue);
        border-radius: 6px; padding: 18px 20px; margin-bottom: 24px;
    }
    .do-details-panel h4 { color: var(--primary-blue); margin-bottom: 14px; font-size: 1rem; border-bottom: 1px solid #c0d0ff; padding-bottom: 8px; }
    .do-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 20px; margin-bottom: 14px; }
    .do-meta-item label { font-size: 0.78rem; color: #666; margin-bottom: 3px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; display: block; }
    .do-meta-item span { font-size: 0.95rem; font-weight: 500; color: #222; }

    /* Item table */
    .item-details-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .item-details-table th { background-color: #1a4499; color: white; padding: 10px 12px; text-align: left; font-size: 0.85rem; }
    .item-details-table td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
    .item-details-table tbody tr:last-child td { border-bottom: none; }
    .item-subtotal { font-weight: 600; color: var(--primary-blue); }

    /* Dynamic / Editable table styles */
    .editable-item-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 14px; }
    .editable-item-table th { background-color: #333333; color: white; padding: 10px 12px; text-align: left; font-size: 0.85rem; }
    .editable-item-table td { padding: 8px 10px; border: 1px solid var(--border-color); vertical-align: middle; }
    .editable-item-table input[type="text"], .editable-item-table input[type="number"] { padding: 8px; font-size: 0.9rem; border: 1px solid var(--border-color); border-radius: 4px; width: 100%; }

    /* DO document row */
    .do-doc-row {
        display: flex; align-items: center; gap: 10px;
        margin-top: 10px; padding: 10px 12px;
        background: white; border-radius: 4px; border: 1px solid #ddd;
    }
    .do-doc-row .doc-icon { font-size: 1.4rem; }
    .do-doc-row .doc-name { flex: 1; font-weight: 500; font-size: 0.9rem; }
    .do-doc-row .doc-actions { display: flex; gap: 6px; }

    /* --- FORM ELEMENTS --- */
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.95rem; }
    input[type="text"], input[type="number"], select, textarea {
        width: 100%; padding: 12px; border: 1px solid var(--border-color);
        border-radius: 4px; font-size: 1rem;
    }
    input:focus, textarea:focus { border-color: var(--primary-blue); outline: none; box-shadow: 0 0 5px rgba(0,51,153,0.2); }
    textarea { resize: vertical; font-family: inherit; }
    .input-hint { font-size: 0.82rem; color: #888; margin-top: 5px; display: block; }

    .checkbox-row { display: flex; align-items: center; gap: 12px; }
    .checkbox-row input[type="checkbox"] { width: 18px; height: 18px; margin: 0; cursor: pointer; }
    .checkbox-row label { margin: 0; cursor: pointer; font-weight: 500; }

    /* --- INVOICE SUMMARY PANEL --- */
    .invoice-summary {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        border: 1px solid #c0d0ff;
        border-radius: 8px; padding: 22px 24px; margin: 24px 0;
    }
    .invoice-summary h4 { color: var(--primary-blue); margin-bottom: 16px; font-size: 1.05rem; }

    .summary-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 9px 0; border-bottom: 1px solid #dde6ff; font-size: 0.95rem;
    }
    .summary-row:last-child { border-bottom: none; }
    .summary-row .label { color: #444; }
    .summary-row .value { font-weight: 600; }

    .summary-row.total-row {
        margin-top: 10px; padding-top: 14px; border-top: 2px solid var(--primary-blue);
        border-bottom: none; font-size: 1.1rem;
    }
    .summary-row.total-row .label { color: var(--primary-blue); font-weight: 700; }
    .summary-row.total-row .value { color: var(--primary-blue); font-size: 1.25rem; font-weight: 700; }

    .tax-note {
        display: inline-block; font-size: 0.78rem; color: #7c8bc4;
        background: #dce6ff; border-radius: 10px; padding: 2px 8px; margin-left: 8px; font-weight: 400;
    }

    /* --- BUTTONS --- */
    .btn {
        padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer;
        font-size: 1rem; font-weight: bold; transition: background 0.2s;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-primary { background-color: var(--primary-blue); color: var(--white); }
    .btn-primary:hover { background-color: #002266; }
    .btn-secondary { background-color: #666; color: white; }
    .btn-secondary:hover { background-color: #444; }
    .btn-danger { background-color: var(--error-red); color: white; padding: 8px 14px; font-size: 0.88rem; }
    .btn-danger:hover { background-color: #b71c1c; }
    .btn-outline { background: transparent; border: 2px solid var(--primary-blue); color: var(--primary-blue); }
    .btn-outline:hover { background: var(--primary-blue); color: white; }
    .btn-sm { padding: 6px 12px; font-size: 0.85rem; }

    .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; }

    /* --- MODALS --- */
    .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.55); display: none; justify-content: center; align-items: center; z-index: 1000; }
    .modal-overlay.active { display: flex; }
    .modal-box { background: var(--white); border-radius: 8px; padding: 30px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.2); max-width: 480px; width: 90%; }
    .modal-icon { font-size: 3rem; margin-bottom: 10px; }

    .doc-modal-box { background: white; border-radius: 8px; padding: 0; box-shadow: 0 8px 32px rgba(0,0,0,0.3); max-width: 900px; width: 95%; max-height: 90vh; display: flex; flex-direction: column; }
    .doc-modal-header { background: var(--primary-blue); color: white; padding: 14px 20px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .doc-modal-header h4 { margin: 0; font-size: 1rem; }
    .doc-modal-body { flex: 1; overflow: auto; }
    .doc-modal-body iframe { width: 100%; height: 70vh; border: none; }
    .doc-modal-body img { max-width: 100%; display: block; margin: 0 auto; }
    .doc-modal-footer { padding: 12px 20px; border-top: 1px solid #ddd; display: flex; justify-content: flex-end; gap: 8px; }

    /* Loading */
    .loading-spinner { border: 4px solid #f3f3f3; border-top: 4px solid var(--primary-blue); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 20px auto; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 0.92rem; }
    .alert-success { background: #e6f4ea; border-left: 4px solid var(--success-green); color: #2e7d32; }
    .alert-error   { background: #fdecea; border-left: 4px solid var(--error-red);   color: var(--error-red); }

    @media (max-width: 640px) {
        main { padding: 16px; }
        .do-meta-grid { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column; }
        .form-actions .btn { width: 100%; justify-content: center; }
    }

    /* --- FOOTER --- */
    .ktmb-footer {
        background-color: #001133;
        color: rgba(255, 255, 255, 0.6);
        text-align: center;
        padding: 15px 20px;
        font-size: 0.85rem;
        border-top: 3px solid #FFCC00;
        width: 100%;
    }
</style>

<body>

<header class="ktmb-header">
    <div class="ktmb-logo-container">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6d/KTMB_Official_Logo.jpg"
             alt="KTMB Logo" class="ktmb-logo-img" style="width: 80px; height: 50px; background-color: white; border-radius: 5px; object-fit: contain; border: 2px solid #FFCC00; padding: 2px; margin-right: 15px;">
        <div class="system-title" style="font-size: 1.5rem; font-weight: bold; color: white; display: inline-block; vertical-align: middle;">KTM <span style="color: #FFCC00;">eDOIS</span></div>
    </div>
    <div class="ktmb-user-info" style="color: white; display: flex; gap: 15px; align-items: center;">
        <span>{{ auth()->user()->Username }}</span>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); this.closest('form').submit();"
               style="color: #FFCC00; text-decoration: none; font-weight: bold; font-size: 0.9rem;">
                Logout
            </a>
        </form>
    </div>
</header>

<div class="ktmb-subheader" style="background-color: #001133; color: #ccc; padding: 8px 30px; font-size: 0.85rem;">
    Electronic Delivery Order &amp; Invoice System
</div>

<nav class="ktmb-nav">
    <a href="{{ route('vendor.dashboard') }}">
        🏠 Home
    </a>
    <a href="{{ route('delivery.list') }}">
        📋 Manage Delivery Order
    </a>
    <a href="{{ route('invoice.list') }}" class="active">
        🧾 Manage Invoice
    </a>
</nav>

<main>
<div class="container">



    <h2>Submit Invoice Claim</h2>

    {{-- ============================================================
         1. DO DETAILS PANEL (pre-populated, read-only)
    ============================================================ --}}
    <div class="do-details-panel">
        <h4>📋 Delivery Order Details</h4>

        <div class="do-meta-grid">
            <div class="do-meta-item">
                <label>DO Number</label>
                <span>{{ $do->DO_Number }}</span>
            </div>
            <div class="do-meta-item">
                <label>PO Number</label>
                <span>{{ $do->PO_Number ?? '—' }}</span>
            </div>
            <div class="do-meta-item">
                <label>Customer</label>
                <span>{{ $do->Cust_Name ?? '—' }}</span>
            </div>
            <div class="do-meta-item">
                <label>Assigned Staff</label>
                <span>{{ $do->Staff_Name ?? '—' }}</span>
            </div>
            <div class="do-meta-item">
                <label>Created Date</label>
                <span>{{ \Carbon\Carbon::parse($do->Created_Date)->format('d M Y') }}</span>
            </div>
            <div class="do-meta-item">
                <label>Status</label>
                <span style="color: var(--success-green); font-weight: 700;">✔ Approved</span>
            </div>
        </div>

        {{-- Item Details Table --}}
        <h4 style="margin-bottom: 10px;">📦 Item Details Baseline</h4>
        @if($items->count())
            <table class="item-details-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Description</th>
                        <th>Qty</th>
                        <th>Unit Price (RM)</th>
                        <th>Line Total (RM)</th>
                    </tr>
                </thead>
                <tbody id="invoice-items-tbody"> 
                    @foreach($items as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->Item_Desc }}</td>
                            <td>{{ $item->Quantity }}</td>
                            <td>{{ number_format($item->Unit_Price, 2) }}</td>
                            <td class="item-subtotal">{{ number_format($item->Quantity * $item->Unit_Price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color:#888; font-size:0.9rem; padding: 8px 0;">No item details linked to this Delivery Order.</p>
        @endif

        {{-- DO Document --}}
        @if($do->DO_Link)
            <div style="margin-top: 14px;">
                <h4 style="margin-bottom: 8px;">📄 DO Document</h4>
                @php
                    $doExt = pathinfo($do->DO_Link, PATHINFO_EXTENSION);
                    $doUrl = Storage::url($do->DO_Link);
                    $doIcon = $doExt === 'pdf' ? '📄' : '🖼️';
                @endphp
                <div class="do-doc-row">
                    <span class="doc-icon">{{ $doIcon }}</span>
                    <span class="doc-name">DO Document — {{ $do->DO_Number }}</span>
                    <div class="doc-actions">
                        <button class="btn btn-secondary btn-sm"
                            onclick="viewDocument('{{ $doUrl }}', 'DO Document — {{ $do->DO_Number }}', '{{ $doExt }}')">
                            👁 View
                        </button>
                        <a class="btn btn-primary btn-sm" href="{{ $doUrl }}" download target="_blank">
                            ⬇ Download
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <hr class="section-divider">

    {{-- ============================================================
         2. INVOICE SUBMISSION FORM
    ============================================================ --}}
    <div id="invoice-form-container">
        <h3>Invoice Details</h3>

        <div class="form-group" style="background: #f0f4ff; border: 2px dashed var(--primary-blue); padding: 20px; border-radius: 6px; text-align: center; margin-bottom: 25px;">
            <label for="invoice_pdf" style="color: var(--primary-blue); display: block; font-weight: bold; margin-bottom: 5px; cursor: pointer;">
                ⚡ Fast Auto-Fill via Invoice PDF Upload
            </label>
            <input type="file" id="invoice_pdf" accept=".pdf,.png,.jpg,.jpeg" onchange="extractInvoiceData(this)" style="margin-top: 5px;">
            <span class="input-hint">Upload your invoice file to automatically upload items, descriptions, quantities, and prices.</span>
        </div>

        <form id="invoiceForm" onsubmit="handleInvoiceSubmission(event)">
            @csrf
            {{-- Hidden DO ID --}}
            <input type="hidden" id="do_id" name="do_id" value="{{ $do->DO_ID }}">
            {{-- Hidden tax rate (from DB / config) --}}
            <input type="hidden" id="tax_rate" value="{{ $taxRate }}">
            <input type="hidden" id="hidden_items_json" value="{{ json_encode($items) }}">

            {{-- Invoice Number --}}
            <div class="form-group">
                <label for="invoice_num">Invoice Number <span style="color:var(--error-red);">*</span></label>
                <input type="text" id="invoice_num" name="invoice_num"
                       placeholder="e.g. INV-2026-001" required
                       oninput="logAudit('INPUT','Typing invoice number')">
            </div>

            {{-- Custom Invoice Line Items Work Space (Always Displayed) --}}
            <div id="manual-items-container" style="display: block; border: 1px dashed var(--primary-blue); padding: 20px; border-radius: 6px; background: #fafafa; margin-bottom: 24px; margin-top: 25px;">
                <h4 style="font-size: 0.98rem; margin-bottom: 12px; color: var(--primary-blue);">Custom Invoice Line Items</h4>
                <table class="editable-item-table">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Item Description</th>
                            <th style="width: 15%;">Qty</th>
                            <th style="width: 20%;">Unit Price (RM)</th>
                            <th style="width: 15%;">Line Total</th>
                            <th style="width: 5%; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="editable-items-tbody">
                        {{-- Handled dynamically via JavaScript layout handlers --}}
                    </tbody>
                </table>
                <button type="button" class="btn btn-outline btn-sm" onclick="addManualRow()">➕ Add Line Item Row</button>
            </div>

            {{-- Late Penalty Toggle --}}
            <div class="form-group">
                <div class="checkbox-row">
                    <input type="checkbox" id="is_late" name="is_late" value="true"
                           onchange="recalculate()">
                    <label for="is_late">Late Delivery Penalty</label>
                </div>
            </div>

            {{-- Credit Note --}}
            <div class="form-group">
                <label for="credit_note">Credit Note / Discount (RM)</label>
                <input type="number" id="credit_note" name="credit_note"
                       value="0.00" min="0" step="0.01"
                       oninput="recalculate(); toggleDiscountReason(this);">
            </div>

            {{-- Discount Reason (shown when credit note > 0) --}}
            <div class="form-group" id="discount-reason-group" style="display:none;">
                <label for="discount_reason">
                    Discount / Credit Note Reason <span style="color:var(--error-red);">*</span>
                </label>
                <textarea id="discount_reason" name="discount_reason" rows="3"
                    placeholder="e.g. Partial delivery on PO-2026-045, agreed deduction per signed credit note CN-012…"></textarea>
                <span class="input-hint">Recorded in the audit log.</span>
            </div>

            {{-- ============================================================
                 3. INVOICE SUMMARY (instant, auto-populated)
            ============================================================ --}}
            <div class="invoice-summary" id="invoiceSummary">
                <h4>📊 Invoice Summary
                    <span class="tax-note" id="tax-badge">{{ $taxLabel }}</span>
                </h4>
                <div class="summary-row">
                    <span class="label">Subtotal</span>
                    <span class="value" id="summary-base">RM 0.00</span>
                </div>
                <div class="summary-row">
                    <span class="label">Tax <span class="tax-note" id="tax-rate-label">{{ $taxLabel }}</span></span>
                    <span class="value" id="summary-tax">RM 0.00</span>
                </div>
                <div class="summary-row" id="penalty-row" style="display:none;">
                    <span class="label">Late Delivery Penalty (1%)</span>
                    <span class="value" id="summary-penalty" style="color:var(--error-red);">− RM 0.00</span>
                </div>
                <div class="summary-row">
                    <span class="label">Credit Note / Discount</span>
                    <span class="value" id="summary-discount" style="color:var(--success-green);">− RM 0.00</span>
                </div>
                <div class="summary-row total-row">
                    <span class="label">TOTAL CLAIM AMOUNT</span>
                    <span class="value" id="summary-total">RM 0.00</span>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <a href="{{ url('/invoice') }}" class="btn btn-secondary">← Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">Submit Invoice →</button>
            </div>
        </form>
    </div>

    {{-- Processing spinner --}}
    <div id="invoice-processing" style="display:none; text-align:center; padding: 40px 0;">
        <div class="loading-spinner"></div>
        <p style="color:#666; margin-top:10px;">Processing your invoice…</p>
    </div>

</div>
</main>

{{-- ============================
     SUCCESS MODAL
============================= --}}
<div id="invoice-success-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-icon">✅</div>
        <h3 style="color: var(--success-green);">Invoice Submitted Successfully!</h3>
        <p style="margin: 15px 0;">Your invoice has been submitted and is now pending review.</p>
        <div style="background:#f0f4ff; border-radius:6px; padding:14px; text-align:left; margin: 10px 0;">
            <div><strong>Invoice Ref:</strong> <span id="modal-invoice-ref" style="color:var(--primary-blue); font-weight:bold;"></span></div>
            <div><strong>DO Reference:</strong> <span id="modal-do-ref">{{ $do->DO_Number }}</span></div>
            <div><strong>Total Amount:</strong> <span id="modal-total"></span></div>
        </div>
        <a href="{{ url('/invoice') }}" class="btn btn-primary" style="margin-top:14px; width:100%; justify-content:center;">
            Return to Invoice List
        </a>
    </div>
</div>

{{-- ============================
     DOCUMENT VIEWER MODAL
============================= --}}
<div id="doc-viewer-modal" class="modal-overlay">
    <div class="doc-modal-box">
        <div class="doc-modal-header">
            <h4 id="doc-viewer-title">Document Viewer</h4>
            <button onclick="closeDocViewer()" style="background:none; border:none; color:white; font-size:1.3rem; cursor:pointer;">✕</button>
        </div>
        <div class="doc-modal-body" id="doc-viewer-body"></div>
        <div class="doc-modal-footer">
            <a id="doc-download-btn" href="#" download class="btn btn-secondary btn-sm">⬇ Download</a>
            <button onclick="closeDocViewer()" class="btn btn-primary btn-sm">Close</button>
        </div>
    </div>
</div>

<script>
    // ─── Global State Variables ──────────────────────────────────────────────
    let ITEMS = [];
    let TAX_RATE = 0.06;

    // ─── Run on page load to safely populate data and summary ───────────────
    document.addEventListener('DOMContentLoaded', () => {
        const hiddenItemsInput = document.getElementById('hidden_items_json');
        const taxRateInput = document.getElementById('tax_rate');

        ITEMS = JSON.parse(hiddenItemsInput ? hiddenItemsInput.value : '[]');
        TAX_RATE = taxRateInput ? parseFloat(taxRateInput.value) : 0.06;

        // Automatically populate editable rows layout instantly on load
        const tbody = document.getElementById('editable-items-tbody');
        if (tbody && tbody.children.length === 0) {
            ITEMS.forEach(item => {
                const desc = item.Item_Desc || item.item_desc || '';
                const qty = item.Quantity || item.quantity || 0;
                const price = item.Unit_Price || item.unit_price || 0;
                addManualRow(desc, qty, price);
            });
        }

        // Trigger execution configuration baseline
        recalculate();
        logAudit('NAVIGATE', `Opened unified invoice form for DO: {{ $do->DO_Number }}`);
    });

    // ─── AJAX Invoice Document Parser ──────────────────────────────────────
    function extractInvoiceData(input) {
        const file = input.files[0];
        if (!file) return;

        const formData = new FormData();
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        formData.append('_token', csrfToken ? csrfToken.content : '');
        formData.append('invoice_file', file);

        const formContainer = document.getElementById('invoice-form-container');
        const processingLoader = document.getElementById('invoice-processing');
        
        if (formContainer) formContainer.style.display = 'none';
        if (processingLoader) processingLoader.style.display = 'block';

        fetch('/invoice/extract-data', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (processingLoader) processingLoader.style.display = 'none';
            if (formContainer) formContainer.style.display = 'block';

            if (data.success) {
                // 1. Auto-fill extracted document configuration metadata fields
                if (data.invoice_num) {
                    document.getElementById('invoice_num').value = data.invoice_num;
                }

                // 2. Overwrite the global tracking array layout
                ITEMS = data.items;

                // 3. Dynamically update baseline layout display table view
                const tbody = document.getElementById('invoice-items-tbody');
                if (tbody) {
                    tbody.innerHTML = '';
                    ITEMS.forEach((item, index) => {
                        const qty = parseFloat(item.Quantity || item.quantity) || 0;
                        const price = parseFloat(item.Unit_Price || item.unit_price) || 0;
                        const lineTotal = (qty * price).toFixed(2);

                        tbody.insertAdjacentHTML('beforeend', `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${escapeHtml(item.Item_Desc || item.item_desc)}</td>
                                <td>${qty}</td>
                                <td>${price.toFixed(2)}</td>
                                <td class="item-subtotal">${lineTotal}</td>
                            </tr>
                        `);
                    });
                }

                // 4. Update row layouts modification workspace grid immediately
                const editableTbody = document.getElementById('editable-items-tbody');
                if (editableTbody) editableTbody.innerHTML = '';
                ITEMS.forEach(item => {
                    addManualRow(
                        item.Item_Desc || item.item_desc || '',
                        item.Quantity || item.quantity || 0,
                        item.Unit_Price || item.unit_price || 0
                    );
                });

                recalculate();
                logAudit('AI_EXTRACTION', `Successfully parsed data layouts out of: ${file.name}`);

                if (data.ocr_warning) {
                    alert('Invoice file processed via image scanning (OCR). Please double-check the Invoice Number and item details below carefully, as scanned text can sometimes be misread.');
                } else {
                    alert('Invoice file processed! System parameters auto-filled successfully.');
                }
            } else {
                alert('Extraction error: ' + data.message);
            }
        })
        .catch(err => {
            if (processingLoader) processingLoader.style.display = 'none';
            if (formContainer) formContainer.style.display = 'block';
            console.error(err);
            alert('An unexpected error occurred during document parsing parameters processing.');
        });
    }

    // ─── Append New Editable Input Row ───────────────────────────────────────
    function addManualRow(desc = '', qty = 1, price = 0.00) {
        const tbody = document.getElementById('editable-items-tbody');
        if (!tbody) return;

        const row = document.createElement('tr');
        row.className = 'item-row';
        
        row.innerHTML = `
            <td>
                <input type="text" class="item-desc" value="${escapeHtml(desc)}" placeholder="Enter item description..." required>
            </td>
            <td>
                <input type="number" class="item-qty" value="${qty}" min="0" step="any" oninput="recalculate()" required style="text-align: center;">
            </td>
            <td>
                <input type="number" class="item-price" value="${parseFloat(price).toFixed(2)}" min="0" step="0.01" oninput="recalculate()" required style="text-align: right;">
            </td>
            <td class="row-total" style="text-align: right; font-weight: 600; color: var(--primary-blue);">
                RM 0.00
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeManualRow(this)">✕</button>
            </td>
        `;
        
        tbody.appendChild(row);
        recalculate();
    }

    // ─── Remove Manual Entry Input Row ───────────────────────────────────────
    function removeManualRow(button) {
        button.closest('tr').remove();
        recalculate();
    }

    // ─── Recalculate summary metrics dynamically ──────────────────────────────
    function recalculate() {
        let dynamicSubtotal = 0;

        // Scrape inputs from custom item rows layout modification grid map
        document.querySelectorAll('.item-row').forEach(row => {
            const qtyInput = row.querySelector('.item-qty');
            const priceInput = row.querySelector('.item-price');
            const lineTotalCell = row.querySelector('.row-total');

            const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
            const price = parseFloat(priceInput ? priceInput.value : 0) || 0;
            const lineTotal = qty * price;

            if (lineTotalCell) {
                lineTotalCell.textContent = 'RM ' + lineTotal.toFixed(2);
            }
            dynamicSubtotal += lineTotal;
        });

        const creditNoteInput = document.getElementById('credit_note');
        const creditNote = creditNoteInput ? parseFloat(creditNoteInput.value) || 0 : 0;
        
        const isLateCheck = document.getElementById('is_late');
        const isLate = isLateCheck ? isLateCheck.checked : false;
        
        const tax = dynamicSubtotal * TAX_RATE;
        const penalty = isLate ? (dynamicSubtotal * 0.01) : 0;
        
        const total = (dynamicSubtotal + tax) - penalty - creditNote;

        if (document.getElementById('summary-base')) document.getElementById('summary-base').textContent = 'RM ' + dynamicSubtotal.toFixed(2);
        if (document.getElementById('summary-tax')) document.getElementById('summary-tax').textContent = 'RM ' + tax.toFixed(2);
        if (document.getElementById('summary-penalty')) document.getElementById('summary-penalty').textContent = '− RM ' + penalty.toFixed(2);
        if (document.getElementById('summary-discount')) document.getElementById('summary-discount').textContent = '− RM ' + creditNote.toFixed(2);
        if (document.getElementById('summary-total')) document.getElementById('summary-total').textContent = 'RM ' + total.toFixed(2);

        if (document.getElementById('penalty-row')) {
            document.getElementById('penalty-row').style.display = isLate ? 'flex' : 'none';
        }
    }

    // ─── Toggle Discount Reason input requirement visibility ──────────────────
    function toggleDiscountReason(input) {
        const group = document.getElementById('discount-reason-group');
        const textarea = document.getElementById('discount_reason');
        
        if (!group || !textarea) return;

        if (parseFloat(input.value) > 0) {
            group.style.display = 'block';
            textarea.required = true;
        } else {
            group.style.display = 'none';
            textarea.required = false;
            textarea.value = '';
        }
    }

    // ─── AJAX Invoice Submission Handler ──────────────────────────────────────
    function handleInvoiceSubmission(event) {
        event.preventDefault();

        const invNumEl = document.getElementById('invoice_num');
        const creditNoteEl = document.getElementById('credit_note');
        const discountReasonEl = document.getElementById('discount_reason');
        const doIdEl = document.getElementById('do_id');

        const invNum = invNumEl ? invNumEl.value.trim() : '';
        const creditNote = creditNoteEl ? parseFloat(creditNoteEl.value) || 0 : 0;
        const discountReason = discountReasonEl ? discountReasonEl.value.trim() : '';

        if (!invNum) { 
            alert('Please enter an Invoice Number.'); 
            return; 
        }

        if (creditNote > 0 && !discountReason) {
            if (discountReasonEl) {
                discountReasonEl.style.borderColor = 'var(--error-red)';
                discountReasonEl.focus();
            }
            alert('Please provide a reason for the discount / credit note.');
            return;
        }
        if (discountReasonEl) discountReasonEl.style.borderColor = '';

        let finalItemsArray = [];
        const rowElements = document.querySelectorAll('.item-row');
        rowElements.forEach(row => {
            const descVal = row.querySelector('.item-desc').value.trim();
            const qtyVal = parseFloat(row.querySelector('.item-qty').value) || 0;
            const priceVal = parseFloat(row.querySelector('.item-price').value) || 0;
            
            finalItemsArray.push({
                Item_Desc: descVal || 'Custom Vendor Line Item',
                Quantity: qtyVal,
                Unit_Price: priceVal
            });
        });

        if (finalItemsArray.length === 0) {
            alert('Please add at least one line item row.');
            return;
        }

        const formContainer = document.getElementById('invoice-form-container');
        const processingLoader = document.getElementById('invoice-processing');
        
        if (formContainer) formContainer.style.display = 'none';
        if (processingLoader) processingLoader.style.display = 'block';

        const formData = new FormData();
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        
        formData.append('_token', csrfToken ? csrfToken.content : '');
        formData.append('do_id', doIdEl ? doIdEl.value : '');
        formData.append('invoice_num', invNum);
        
        const isLateCheck = document.getElementById('is_late');
        formData.append('is_late', isLateCheck && isLateCheck.checked ? 'true' : 'false');
        formData.append('credit_note', creditNote);
        formData.append('discount_reason', discountReason);

        finalItemsArray.forEach((item, i) => {
            formData.append(`items[${i}][desc]`, item.Item_Desc || item.item_desc || '');
            formData.append(`items[${i}][quantity]`, item.Quantity || item.quantity || 0);
            formData.append(`items[${i}][unit_price]`, item.Unit_Price || item.unit_price || 0);
        });

        const proofFileInput = document.getElementById('proofFile') || document.getElementById('proof_file');
        const proofFile = proofFileInput && proofFileInput.files ? proofFileInput.files[0] : null;
        if (proofFile) {
            formData.append('proof_file', proofFile);
        }

        fetch('/invoice/store', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (processingLoader) processingLoader.style.display = 'none';
            if (data.success) {
                const modalRef = document.getElementById('modal-invoice-ref');
                const modalTotal = document.getElementById('modal-total');
                const summaryTotal = document.getElementById('summary-total');
                const successModal = document.getElementById('invoice-success-modal');

                if (modalRef) modalRef.textContent = invNum;
                if (modalTotal && summaryTotal) modalTotal.textContent = summaryTotal.textContent;
                if (successModal) successModal.classList.add('active');
                
                logAudit('SUBMIT', `Invoice submitted: ${invNum} for DO {{ $do->DO_Number }}`);
            } else {
                if (formContainer) formContainer.style.display = 'block';
                alert('Submission failed: ' + (data.message || 'Unknown error.'));
            }
        })
        .catch(err => {
            if (processingLoader) processingLoader.style.display = 'none';
            if (formContainer) formContainer.style.display = 'block';
            console.error(err);
            alert('An error occurred. Please try again.');
        });
    }

    // ─── Document Viewer Overlay Controls ────────────────────────────────────
    function viewDocument(link, title, ext) {
        const titleEl = document.getElementById('doc-viewer-title');
        const downloadBtn = document.getElementById('doc-download-btn');
        const bodyEl = document.getElementById('doc-viewer-body');
        const modalEl = document.getElementById('doc-viewer-modal');

        if (titleEl) titleEl.textContent = title;
        if (downloadBtn) {
            downloadBtn.href = link;
            downloadBtn.download = title;
        }

        if (bodyEl) {
            if (ext === 'pdf') {
                bodyEl.innerHTML = `<iframe src="${link}" title="${title}"></iframe>`;
            } else if (['jpg','jpeg','png','gif','webp'].includes(ext.toLowerCase())) {
                bodyEl.innerHTML = `<img src="${link}" alt="${title}" style="max-width:100%; padding:16px; display:block; margin:auto;">`;
            } else {
                bodyEl.innerHTML = `<p style="padding:30px; text-align:center; color:#666;">Preview not available.<br>
                    <a href="${link}" download class="btn btn-primary btn-sm" style="margin-top:10px; display:inline-block;">⬇ Download</a></p>`;
            }
        }
        if (modalEl) modalEl.classList.add('active');
        logAudit('VIEW_DOCUMENT', `Viewed: ${title}`);
    }

    function closeDocViewer() {
        const modalEl = document.getElementById('doc-viewer-modal');
        const bodyEl = document.getElementById('doc-viewer-body');
        if (modalEl) modalEl.classList.remove('active');
        if (bodyEl) bodyEl.innerHTML = '';
    }

    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', e => { 
            if (e.target === o && o.id === 'doc-viewer-modal') closeDocViewer(); 
        });
    });

    // ─── Vendor Status Toggle Control ────────────────────────────────────────
    function toggleVendorStatus() {
        const toggle = document.getElementById('status-toggle');
        const label = document.getElementById('status-label');
        if (!toggle || !label) return;

        toggle.classList.toggle('active');
        const isActive = toggle.classList.contains('active');
        label.innerText = isActive ? 'ACTIVE' : 'INACTIVE';
        label.style.color = isActive ? '#aaffaa' : '#ffaaaa';
    }

    // ─── Audit Trail Logger Request ──────────────────────────────────────────
    function logAudit(action, description) {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) return;
        fetch('/audit/log', {
            method: 'POST',
            headers: { 
                'Content-Type':'application/json', 
                'X-CSRF-TOKEN':token.content, 
                'X-Requested-With':'XMLHttpRequest' 
            },
            body: JSON.stringify({ action, description, module: 'Invoice' })
        }).catch(() => {});
    }

    // Utility text validation escaping method
    function escapeHtml(string) {
        return String(string).replace(/[&<>"']/g, function (s) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[s];
        });
    }
</script>

<footer class="ktmb-footer">
    &copy; 2026 Keretapi Tanah Melayu Berhad (KTMB). All rights reserved.
</footer>

</body>
</html>