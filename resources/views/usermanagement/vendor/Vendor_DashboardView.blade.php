<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM eDOIS - Vendor Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/ktmb.css') }}">
    <style>
        /* NAV */
        .ktmb-nav {
            background-color: #002266;
            display: flex;
            align-items: stretch;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .ktmb-nav a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .ktmb-nav a:hover {
            background-color: rgba(255,255,255,0.1);
            color: #ffffff;
        }

        .ktmb-nav a.active {
            color: #FFCC00;
            border-bottom-color: #FFCC00;
            background-color: rgba(255,204,0,0.08);
        }

        /* DASHBOARD CONTENT */
        .dashboard-wrapper {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        /* WELCOME BANNER */
        .welcome-banner {
            background: linear-gradient(135deg, #003399, #002266);
            color: #ffffff;
            border-radius: 8px;
            padding: 25px 30px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,51,153,0.2);
        }

        .welcome-banner h3 {
            font-size: 1.3rem;
            margin-bottom: 6px;
        }

        .welcome-banner p {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
            margin: 2px 0;
        }

        .welcome-banner .vendor-badge {
            background-color: #FFCC00;
            color: #003399;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.85rem;
        }

        /* STATS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 25px;
        }

        .stat-card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid #003399;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-card.yellow { border-left-color: #FFCC00; }
        .stat-card.green  { border-left-color: #2e7d32; }
        .stat-card.orange { border-left-color: #ff9800; }

        .stat-card .stat-info .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #003399;
        }

        .stat-card.yellow .stat-info .stat-number { color: #b8860b; }
        .stat-card.green  .stat-info .stat-number { color: #2e7d32; }
        .stat-card.orange .stat-info .stat-number { color: #e65100; }

        .stat-card .stat-info .stat-label {
            font-size: 0.85rem;
            color: #666;
            margin-top: 4px;
        }

        .stat-card .stat-icon {
            font-size: 2rem;
            opacity: 0.15;
        }

        /* TABLES SECTION */
        .section-card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            border-top: 3px solid #FFCC00;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .section-header h3 {
            color: #003399;
            font-size: 1.1rem;
        }

        .section-header a {
            font-size: 0.85rem;
            color: #003399;
            text-decoration: none;
            border: 1px solid #003399;
            padding: 5px 14px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .section-header a:hover {
            background-color: #003399;
            color: #ffffff;
        }

        /* STATUS BADGES */
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-paid     { background-color: #e8f5e9; color: #2e7d32; }
        .badge-submitted { background-color: #e3f2fd; color: #003399; }
        .badge-pending  { background-color: #fff3e0; color: #e65100; }
        .badge-rejected { background-color: #ffebee; color: #d32f2f; }

        /* VENDOR INFO */
        .vendor-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 25px;
        }

        .vendor-info-item {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 15px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-top: 3px solid #003399;
        }

        .vendor-info-item .info-label {
            font-size: 0.78rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .vendor-info-item .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .vendor-info-grid { grid-template-columns: 1fr 1fr; }
            .welcome-banner { flex-direction: column; gap: 15px; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .vendor-info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <header class="ktmb-header">
        <div class="ktmb-logo-container">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6d/KTMB_Official_Logo.jpg"
                 alt="KTMB Logo" class="ktmb-logo-img">
            <div class="ktmb-system-title">KTM <span>eDOIS</span></div>
        </div>
        <div class="ktmb-user-info">
            <span>{{ auth()->user()->Username }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); this.closest('form').submit();">
                    Logout
                </a>
            </form>
        </div>
    </header>

    <!-- SUBHEADER -->
    <div class="ktmb-subheader">
        Electronic Delivery Order &amp; Invoice System
    </div>

    <!-- NAVIGATION -->
    <nav class="ktmb-nav">
        <a href="{{ route('vendor.dashboard') }}" class="active">
            🏠 Home
        </a>
        <a href="{{ route('delivery.list') }}">
            📋 Manage Delivery Order
        </a>
        <a href="#">
            🧾 Manage Invoice
        </a>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="dashboard-wrapper">

        <!-- WELCOME BANNER -->
        <div class="welcome-banner">
            <div>
                <h3>Welcome, {{ auth()->user()->Username }}!</h3>
                <p>Vendor Portal — KTM eDOIS System</p>
                <p>{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="vendor-badge">
                VENDOR
            </div>
        </div>

        <!-- VENDOR INFO (Read Only) -->
        <div class="vendor-info-grid">
            <div class="vendor-info-item">
                <div class="info-label">Vendor ID</div>
                <div class="info-value">
                    {{ $supplier->Supplier_ID ?? '—' }}
                </div>
            </div>
            <div class="vendor-info-item">
                <div class="info-label">Company Name</div>
                <div class="info-value">
                    {{ $supplier->Supplier_Name ?? '—' }}
                </div>
            </div>
            <div class="vendor-info-item">
                <div class="info-label">Vendor Number</div>
                <div class="info-value">
                    {{ $supplier->Vendor_Number ?? '—' }}
                </div>
            </div>
            <div class="vendor-info-item">
                <div class="info-label">Contact Person</div>
                <div class="info-value">
                    {{ $supplier->Contact_Person ?? '—' }}
                </div>
            </div>
            <div class="vendor-info-item">
                <div class="info-label">Phone</div>
                <div class="info-value">
                    {{ $supplier->Phone ?? '—' }}
                </div>
            </div>
            <div class="vendor-info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    @if(($supplier->Status ?? '') === 'active')
                        <span style="color:#2e7d32; font-weight:600;">● Active</span>
                    @elseif(($supplier->Status ?? '') === 'inactive')
                        <span style="color:#d32f2f; font-weight:600;">● Inactive</span>
                    @else
                        —
                    @endif
                </div>
            </div>
        </div>

        <!-- STATS CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Total Delivery Orders</div>
                </div>
                <div class="stat-icon">📋</div>
            </div>
            <div class="stat-card orange">
                <div class="stat-info">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Pending Orders</div>
                </div>
                <div class="stat-icon">⏳</div>
            </div>
            <div class="stat-card green">
                <div class="stat-info">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Total Invoices</div>
                </div>
                <div class="stat-icon">🧾</div>
            </div>
        </div>

        <!-- RECENT DELIVERY ORDERS -->
        <div class="section-card">
            <div class="section-header">
                <h3>📋 Recent Delivery Orders</h3>
                <a href="#">View All</a>
            </div>
            <table class="ktmb-table">
                <thead>
                    <tr>
                        <th>DO Reference</th>
                        <th>PO Number</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="text-align:center; color:#999; padding:20px;">
                            No delivery orders found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- RECENT INVOICES -->
        <div class="section-card">
            <div class="section-header">
                <h3>🧾 Recent Invoices</h3>
                <a href="#">View All</a>
            </div>
            <table class="ktmb-table">
                <thead>
                    <tr>
                        <th>Invoice No</th>
                        <th>DO Reference</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" style="text-align:center; color:#999; padding:20px;">
                            No invoices found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- FOOTER -->
    <footer class="ktmb-footer">
        &copy; {{ date('Y') }} Keretapi Tanah Melayu Berhad (KTMB). All rights reserved.
    </footer>

    {{-- SUCCESS ALERT --}}
    @if(session('success'))
        <script>
            alert(@json(session('success')));
        </script>
    @endif

</body>
</html>
