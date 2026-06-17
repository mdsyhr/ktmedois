<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM eDOIS - Delivery Order Details</title>
    <link rel="stylesheet" href="{{ asset('css/ktmb.css') }}">
    <style>
        /* Basic Reset */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .btn {
            padding: 12px 28px;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-block;
        }

        .btn-cancel {
            background: #f5f5f5;
            color: #555;
            border: 1px solid #ddd;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
            color: #333;
        }

        /* --- NAV BAR --- */
        .ktmb-nav {
            background-color: #002266;
            display: flex;
            align-items: stretch;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .ktmb-nav a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .ktmb-nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .ktmb-nav a.active {
            color: #FFCC00;
            border-bottom-color: #FFCC00;
            background-color: rgba(255, 204, 0, 0.08);
        }

        /* MAIN CONTENT CONTAINER */
        .content-container {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }

        /* PAGE HEADER */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f4f7f6;
        }

        .page-header h2 {
            color: #003399;
            margin: 0;
            font-size: 1.5rem;
        }

        /* DETAILS CARD SECTIONS */
        .details-card {
            background: #ffffff;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .card-header {
            background-color: #003399;
            color: white;
            padding: 15px 20px;
            font-size: 1.1rem;
            font-weight: 600;
            border-bottom: 3px solid #FFCC00;
        }

        /* DETAILS TABLE */
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table tr {
            border-bottom: 1px solid #f1f3f5;
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table th {
            background-color: #f8f9fa;
            text-align: left;
            padding: 15px 20px;
            color: #495057;
            font-size: 0.9rem;
            font-weight: 600;
            width: 30%;
            border-right: 1px solid #f1f3f5;
        }

        .details-table td {
            padding: 15px 20px;
            color: #333;
            font-size: 1rem;
        }

        /* STATUS BADGES */
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-paid {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .badge-pending {
            background-color: #fff3e0;
            color: #e65100;
        }

        .badge-rejected {
            background-color: #ffebee;
            color: #d32f2f;
        }

        /* FILE LINKS */
        .file-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            background: #e3f2fd;
            color: #003399;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid #bbdefb;
            transition: all 0.2s;
        }

        .file-link:hover {
            background: #003399;
            color: #fff;
        }

        .no-file {
            color: #888;
            font-style: italic;
        }

        /* RESPONSIVE OVERRIDES */
        @media (max-width: 768px) {

            .details-table th,
            .details-table td {
                display: block;
                width: 100%;
                text-align: left;
                padding: 10px 15px;
            }

            .details-table th {
                border-right: none;
                border-bottom: 1px solid #eee;
                background: #fff;
                color: #003399;
                padding-bottom: 5px;
            }

            .details-table td {
                padding-top: 5px;
            }
        }
    </style>
</head>

<body>

    <header class="ktmb-header">
        <div class="ktmb-logo-container">
            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6d/KTMB_Official_Logo.jpg" alt="KTMB Logo"
                class="ktmb-logo-img">
            <div class="ktmb-system-title">KTM <span>eDOIS</span></div>
        </div>
        <div class="ktmb-user-info">
            <span>{{ auth()->user()->Username }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
            </form>
        </div>
    </header>

    <div class="ktmb-subheader">
        Electronic Delivery Order &amp; Invoice System
    </div>

    <nav class="ktmb-nav">
        <a href="{{ route('vendor.dashboard') }}">
            🏠 Home
        </a>
        <a href="{{ route('delivery.list') }}" class="active">
            📋 Manage Delivery Order
        </a>
        <a href="{{ route('invoice.list') }}">
            🧾 Manage Invoice
        </a>
    </nav>

    <main class="content-container">

        <!-- Standard wide-styled layout wrapper -->
        <div class="ktmb-card-wide">

            <div class="">
                <a href="{{ route('delivery.list') }}" class="btn btn-cancel">&lt; back to list</a>
            </div>
            <div class="page-header">
                <h2>Delivery Order Details</h2>
            </div>

            <div class="details-card">
                <div class="card-header">Order Information</div>
                <table class="details-table">
                    <tr>
                        <th>PO Number</th>
                        <td><strong>{{ $item->PO_Number }}</strong></td>
                    </tr>
                    <tr>
                        <th>DO Number</th>
                        <td>{{ $item->DO_Number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @php
                                $badgeClass = 'badge-pending';
                                if (in_array($item->Status, ['Approved'])) {
                                    $badgeClass = 'badge-paid';
                                } elseif ($item->Status === 'Rejected') {
                                    $badgeClass = 'badge-rejected';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $item->Status }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <td>{{ $item->customer ? $item->customer->Cust_Name : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Created Date</th>
                        <td>{{ \Carbon\Carbon::parse($item->Created_Date)->format('d M Y, h:i A') }}</td>
                    </tr>
                    <tr>
                        <th>Reason Of Rejection</th>
                        <td>{{ $item->Reason ?? 'Not applicable' }}</td>
                    </tr>
                </table>
            </div>

            <div class="details-card">
                <div class="card-header">Attached Documents</div>
                <table class="details-table">
                    <tr>
                        <th>Delivery Order File</th>
                        <td>
                            @if ($item->DO_Link)
                                <a href="{{ route('delivery.file', ['id' => $item->DO_ID, 'type' => 'do']) }}"
                                    target="_blank" class="file-link">
                                    View Delivery Orders
                                </a>
                            @else
                                <span class="no-file">No file attached</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Proof of Delivery</th>
                        <td>
                            @if ($item->Proof_Link)
                                <a href="{{ route('delivery.file', ['id' => $item->DO_ID, 'type' => 'proof']) }}"
                                    target="_blank" class="file-link">
                                    View Proof Of Delivery
                                </a>
                            @else
                                <span class="no-file">No file attached</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </main>
</body>

</html>
