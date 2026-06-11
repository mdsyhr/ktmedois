<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KTM eDOIS - Approved Delivery Orders</title>
</head>

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

    body {
        background-color: var(--light-gray);
        color: var(--text-dark);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* --- HEADER --- */
    header {
        background-color: var(--primary-blue);
        color: var(--white);
        padding: 15px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .logo-container { display: flex; align-items: center; gap: 15px; }

    .logo-img {
        width: 80px; height: 50px;
        background-color: var(--white);
        border-radius: 5px;
        object-fit: contain;
        border: 2px solid var(--accent-yellow);
        padding: 2px;
    }

    .system-title { font-size: 1.5rem; font-weight: bold; color: var(--white); }
    .system-title span { color: var(--accent-yellow); }

    .simulation-controls {
        background: rgba(255,255,255,0.2);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--white);
    }

    .toggle-switch {
        cursor: pointer; background: #333; border-radius: 10px; padding: 2px;
        width: 40px; height: 20px; position: relative; transition: 0.3s;
    }
    .toggle-switch.active { background: var(--accent-yellow); }
    .toggle-knob {
        width: 16px; height: 16px; background: white; border-radius: 50%;
        position: absolute; left: 2px; transition: 0.3s;
    }
    .toggle-switch.active .toggle-knob { left: 22px; }

    /* --- TASKBAR --- */
    .taskbar {
        background-color: #002266;
        padding: 0;
        display: flex;
        align-items: stretch;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .taskbar-nav { display: flex; list-style: none; margin: 0; padding: 0; width: 100%; }
    .taskbar-item { position: relative; flex: 1; text-align: center; }
    .taskbar-link {
        display: flex; align-items: center; justify-content: center;
        padding: 15px 20px; color: rgba(255,255,255,0.9); text-decoration: none;
        font-weight: 500; transition: all 0.3s; border-bottom: 3px solid transparent;
        height: 100%; width: 100%;
    }
    .taskbar-link:hover { background-color: rgba(255,255,255,0.1); color: var(--white); }
    .taskbar-link.active {
        color: var(--accent-yellow); border-bottom-color: var(--accent-yellow);
        background-color: rgba(255,204,0,0.1);
    }
    .taskbar-link .icon { margin-right: 8px; font-size: 1.1rem; }

    /* --- MAIN --- */
    main {
        flex: 1; padding: 40px;
        display: flex; justify-content: center; align-items: flex-start;
    }

    .container {
        background-color: var(--white);
        width: 100%; max-width: 1000px;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-top: 5px solid var(--accent-yellow);
    }

    h2 { color: var(--primary-blue); margin-bottom: 8px; }
    .page-subtitle { color: #666; font-size: 0.95rem; margin-bottom: 24px; }

    /* --- SEARCH & FILTER BAR --- */
    .filter-bar {
        display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;
    }
    .filter-bar input[type="text"] {
        flex: 1; min-width: 200px;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 0.95rem;
    }
    .filter-bar input[type="text"]:focus { border-color: var(--primary-blue); outline: none; }

    /* --- DO TABLE --- */
    .do-table-wrap { overflow-x: auto; }

    table { width: 100%; border-collapse: collapse; margin-top: 4px; }
    thead tr { background-color: var(--primary-blue); }
    th {
        padding: 13px 14px; text-align: left;
        color: white; font-size: 0.88rem; font-weight: 600;
        white-space: nowrap;
    }
    td {
        padding: 12px 14px; border-bottom: 1px solid #eee;
        font-size: 0.92rem; vertical-align: middle;
    }
    tbody tr:hover { background-color: #f5f8ff; }
    tbody tr:last-child td { border-bottom: none; }

    .do-num { font-weight: 700; color: var(--primary-blue); }
    .po-num { color: #555; font-size: 0.88rem; }

    /* Badge */
    .badge {
        padding: 3px 10px; border-radius: 12px;
        font-size: 0.78rem; font-weight: bold; white-space: nowrap;
    }
    .badge-approved { background: #e6f4ea; color: #2e7d32; }

    /* Action buttons */
    .action-cell { display: flex; gap: 8px; flex-wrap: nowrap; }

    .btn {
        padding: 8px 16px;
        border: none; border-radius: 4px; cursor: pointer;
        font-size: 0.85rem; font-weight: 600;
        transition: background 0.2s, transform 0.1s;
        text-decoration: none; display: inline-flex;
        align-items: center; gap: 5px; white-space: nowrap;
    }
    .btn:active { transform: scale(0.97); }
    .btn-outline {
        background: transparent;
        border: 2px solid var(--primary-blue);
        color: var(--primary-blue);
    }
    .btn-outline:hover { background: var(--primary-blue); color: white; }
    .btn-primary { background-color: var(--primary-blue); color: var(--white); }
    .btn-primary:hover { background-color: #002266; color: var(--white); }
    .btn-success { background-color: var(--success-green); color: white; }
    .btn-success:hover { background-color: #1b5e20; }

    /* Empty state */
    .empty-state {
        text-align: center; padding: 60px 30px; color: #999;
    }
    .empty-state .empty-icon { font-size: 3rem; margin-bottom: 12px; }
    .empty-state p { font-size: 1rem; }

    /* Stats strip */
    .stats-strip {
        display: flex; gap: 20px; margin-bottom: 24px; flex-wrap: wrap;
    }
    .stat-card {
        flex: 1; min-width: 120px;
        background: #f0f4ff;
        border: 1px solid #c8d8ff;
        border-radius: 6px;
        padding: 14px 18px;
        text-align: center;
    }
    .stat-card .num { font-size: 1.8rem; font-weight: 700; color: var(--primary-blue); }
    .stat-card .lbl { font-size: 0.8rem; color: #666; margin-top: 3px; }

    /* Date column */
    .date-col { color: #555; font-size: 0.88rem; white-space: nowrap; }

    /* Modal */
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.55); display: none;
        justify-content: center; align-items: center; z-index: 1000;
    }
    .modal-overlay.active { display: flex; }
    .doc-modal-box {
        background: white; border-radius: 8px; padding: 0;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        max-width: 900px; width: 95%; max-height: 90vh;
        display: flex; flex-direction: column;
    }
    .doc-modal-header {
        background: var(--primary-blue); color: white;
        padding: 14px 20px; border-radius: 8px 8px 0 0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .doc-modal-header h4 { margin: 0; font-size: 1rem; }
    .doc-modal-body { flex: 1; overflow: auto; padding: 0; }
    .doc-modal-body iframe { width: 100%; height: 70vh; border: none; }
    .doc-modal-body img { max-width: 100%; display: block; margin: 0 auto; }
    .doc-modal-footer {
        padding: 12px 20px; border-top: 1px solid #ddd;
        display: flex; justify-content: flex-end; gap: 8px;
    }

    .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 0.92rem; }
    .alert-success { background: #e6f4ea; border-left: 4px solid var(--success-green); color: #2e7d32; }
    .alert-error   { background: #fdecea; border-left: 4px solid var(--error-red);   color: var(--error-red); }

    @media (max-width: 640px) {
        main { padding: 16px; }
        .action-cell { flex-direction: column; }
        .stats-strip { gap: 10px; }
    }
</style>

<body>

<!-- HEADER -->
<header>
    <div class="logo-container">
        <img src="https://www.bernama.com/storage/photos/b2b32fe44095c63a213df09421007c3f64a8454176890"
             alt="KTMB Logo" class="logo-img">
        <div class="system-title">KTM <span>eDOIS</span></div>
    </div>
    <div class="simulation-controls">
        <span>Vendor Status:</span>
        <span id="status-label" style="font-weight: bold; color: #aaffaa;">ACTIVE</span>
        <div class="toggle-switch active" id="status-toggle" onclick="toggleVendorStatus()">
            <div class="toggle-knob"></div>
        </div>
    </div>
</header>

<!-- TASKBAR -->
<nav class="taskbar">
    <ul class="taskbar-nav">
        <li class="taskbar-item">
            <a href="{{ url('/delivery-order') }}" class="taskbar-link">
                <span class="icon">📋</span> Delivery Order (DO)
            </a>
        </li>
        <li class="taskbar-item">
            <a href="{{ url('/invoice') }}" class="taskbar-link active">
                <span class="icon">🧾</span> Invoice
            </a>
        </li>
    </ul>
</nav>

<main>
<div class="container">

    @if(session('success'))
        <div class="alert alert-success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">❌ {{ session('error') }}</div>
    @endif

    <h2>🧾 Invoice Claims</h2>
   

    {{-- Stats Strip --}}
    <div class="stats-strip">
        <div class="stat-card">
            <div class="num">{{ $deliveryOrders->count() }}</div>
            <div class="lbl">Approved DOs</div>
        </div>
        <div class="stat-card">
            <div class="num">{{ $deliveryOrders->whereNotNull('Proof_Link')->count() }}</div>
            <div class="lbl">With Proof Uploaded</div>
        </div>
        <div class="stat-card">
            <div class="num">{{ $invoices->whereIn('Status', ['Submitted','Pending'])->count() }}</div>
            <div class="lbl">Pending Claims</div>
        </div>
        <div class="stat-card">
            <div class="num" style="color:var(--success-green);">{{ $invoices->where('Status','Approved')->count() }}</div>
            <div class="lbl">Approved Claims</div>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="filter-bar">
        <input type="text" id="searchInput" placeholder="🔍  Search by DO number, PO number or customer…"
               oninput="filterTable()">
    </div>

    {{-- DO Table --}}
    <div class="do-table-wrap">
        <table id="doTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>DO Number</th>
                    <th>PO Number</th>
                    <th>Customer</th>
                    <th>Assigned Staff</th>
                    <th>Date Created</th>
                    <th>Status</th>
                    <th>Claim Status</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody id="doTableBody">
                @forelse($deliveryOrders as $index => $do)
                    @php
                        $existingInvoice = $invoices->firstWhere('DO_ID', $do->DO_ID);
                        $claimStatus = $existingInvoice ? $existingInvoice->Status : null;
                        $claimBadge  = match($claimStatus) {
                            'Submitted' => 'badge-submitted',
                            'Approved'  => 'badge-approved',
                            'Rejected'  => 'badge-rejected',
                            'Pending'   => 'badge-pending',
                            default     => null,
                        };
                    @endphp
                    <tr class="do-row"
                        data-search="{{ strtolower($do->DO_Number . ' ' . $do->PO_Number . ' ' . ($do->Cust_Name ?? '')) }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="do-num">{{ $do->DO_Number }}</span>
                        </td>
                        <td><span class="po-num">{{ $do->PO_Number }}</span></td>
                        <td>{{ $do->Cust_Name ?? '—' }}</td>
                        <td>{{ $do->Staff_Name ?? '—' }}</td>
                        <td class="date-col">
                            {{ \Carbon\Carbon::parse($do->Created_Date)->format('d M Y') }}
                        </td>
                        <td>
                            <span class="badge badge-approved">✔ Approved</span>
                        </td>
                        <td>
                            @if($claimStatus)
                                <span class="badge {{ $claimBadge }}">{{ $claimStatus }}</span>
                            @else
                                <span style="color:#aaa; font-size:0.85rem;">No claim yet</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-cell" style="justify-content:center;">
                                {{-- View DO Document button --}}
                                @if($do->DO_Link)
                                    <button class="btn btn-outline"
                                        onclick="viewDocument('{{ Storage::url($do->DO_Link) }}', 'DO Document — {{ $do->DO_Number }}', '{{ pathinfo($do->DO_Link, PATHINFO_EXTENSION) }}')">
                                        👁 View DO
                                    </button>
                                @else
                                    <button class="btn btn-outline" disabled style="opacity:0.4; cursor:not-allowed;" title="No DO document uploaded">
                                        👁 View DO
                                    </button>
                                @endif

                                {{-- Make Claim button — disabled if claim already exists --}}
                                @if(!$claimStatus)
                                    <a href="{{ url('/invoice/create/' . $do->DO_ID) }}" class="btn btn-success">
                                        🧾 Make Claim
                                    </a>
                                @elseif($claimStatus === 'Rejected')
                                    <a href="{{ url('/invoice/create/' . $do->DO_ID) }}" class="btn btn-primary">
                                        🔄 Resubmit
                                    </a>
                                @else
                                    <button class="btn btn-primary" disabled style="opacity:0.4; cursor:not-allowed;"
                                            title="Claim already {{ $claimStatus }}">
                                        🧾 Claimed
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon">📭</div>
                                <p>No approved Delivery Orders available for invoicing.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
</main>

{{-- ============================
     DOCUMENT VIEWER MODAL
============================= --}}
<div id="doc-viewer-modal" class="modal-overlay">
    <div class="doc-modal-box">
        <div class="doc-modal-header">
            <h4 id="doc-viewer-title">Document Viewer</h4>
            <button onclick="closeDocViewer()"
                style="background:none; border:none; color:white; font-size:1.3rem; cursor:pointer;">✕</button>
        </div>
        <div class="doc-modal-body" id="doc-viewer-body"></div>
        <div class="doc-modal-footer">
            <a id="doc-download-btn" href="#" download class="btn btn-outline" style="border-color:#ccc;color:#333;">⬇ Download</a>
            <button onclick="closeDocViewer()" class="btn btn-primary">Close</button>
        </div>
    </div>
</div>

<script>
    // ─── Search / Filter ─────────────────────────────────────────────────────
    function filterTable() {
        const q = document.getElementById('searchInput').value.toLowerCase().trim();
        document.querySelectorAll('.do-row').forEach(row => {
            row.style.display = row.dataset.search.includes(q) ? '' : 'none';
        });
    }

    // ─── Document Viewer ─────────────────────────────────────────────────────
    function viewDocument(link, title, ext) {
        document.getElementById('doc-viewer-title').textContent = title;
        document.getElementById('doc-download-btn').href        = link;
        document.getElementById('doc-download-btn').download    = title;

        const body = document.getElementById('doc-viewer-body');
        if (ext === 'pdf') {
            body.innerHTML = `<iframe src="${link}" title="${title}"></iframe>`;
        } else if (['jpg','jpeg','png','gif','webp'].includes(ext.toLowerCase())) {
            body.innerHTML = `<img src="${link}" alt="${title}" style="max-width:100%; padding:16px; display:block; margin:auto;">`;
        } else {
            body.innerHTML = `<p style="padding:30px; text-align:center; color:#666;">
                Preview not available for this file type.<br>
                <a href="${link}" download class="btn btn-primary" style="margin-top:10px; display:inline-block;">⬇ Download File</a>
            </p>`;
        }

        document.getElementById('doc-viewer-modal').classList.add('active');
        logAudit('VIEW_DOCUMENT', `Viewed DO document: ${title}`);
    }

    function closeDocViewer() {
        document.getElementById('doc-viewer-modal').classList.remove('active');
        document.getElementById('doc-viewer-body').innerHTML = '';
    }

    document.getElementById('doc-viewer-modal').addEventListener('click', function(e) {
        if (e.target === this) closeDocViewer();
    });

    // ─── Vendor Status Toggle ────────────────────────────────────────────────
    function toggleVendorStatus() {
        const toggle = document.getElementById('status-toggle');
        const label  = document.getElementById('status-label');
        toggle.classList.toggle('active');
        label.innerText = toggle.classList.contains('active') ? 'ACTIVE' : 'INACTIVE';
        label.style.color = toggle.classList.contains('active') ? '#aaffaa' : '#ffaaaa';
    }

    // ─── Audit Log (fire-and-forget) ─────────────────────────────────────────
    function logAudit(action, description) {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) return;
        fetch('/audit/log', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':token.content, 'X-Requested-With':'XMLHttpRequest' },
            body: JSON.stringify({ action, description, module: 'Invoice' })
        }).catch(() => {});
    }
</script>

</body>
</html>