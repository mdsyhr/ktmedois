<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM eDOIS - Delivery Order Details</title>
    <style>
        /* --- CLEAN & MINIMAL CSS --- */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* HEADER */
        .ktmb-header {
            background: #002266;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .ktmb-logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .ktmb-logo-img {
            height: 40px;
            background: white;
            padding: 5px;
            border-radius: 4px;
        }

        .ktmb-system-title {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .ktmb-system-title span {
            color: #FFCC00;
        }

        .ktmb-user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            color: rgba(255, 255, 255, 0.9);
        }

        .ktmb-user-info a {
            color: #FFCC00;
            text-decoration: none;
            font-weight: 600;
        }

        /* MAIN CONTENT */
        .content-wrapper {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* PAGE HEADER */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-header h2 {
            color: #003399;
            margin: 0;
            font-size: 1.5rem;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        /* DETAILS CARD */
        .details-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 20px;
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

        /* RESPONSIVE (Stacks table on mobile) */
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

    <main class="content-wrapper">
        <!-- Page Title & Back Button -->
        <div class="page-header">
            <a href="{{ route('delivery.list') }}" class="btn-back">← Back to List</a>
        </div>
        <div class="page-header">
            <h2>Delivery Order Details</h2>
        </div>


        <div class="details-card">
            <div class="card-header">Order Information</div>
            <table class="details-table">
                <tr>
                    <th>DO Number</th>
                    <td><strong>{{ $item->DO_Number ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <th>PO Number</th>
                    <td>{{ $item->PO_Number }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @php
                            $badgeClass = 'badge-pending';
                            if (in_array($item->Status, ['Approved', 'Submitted', 'Completed'])) {
                                $badgeClass = 'badge-paid';
                            } elseif ($item->Status === 'Rejected') {
                                $badgeClass = 'badge-rejected';
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $item->Status }}</span>
                    </td>
                </tr>
                <tr>
                    <th>Created Date</th>
                    <td>{{ \Carbon\Carbon::parse($item->Created_Date)->format('d M Y, h:i A') }}</td>
                </tr>
                <tr>
                    <th>Reason / Remarks</th>
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

    </main>
</body>

</html>
