<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM eDOIS - Delivery Orders</title>
    <style>
        /* Basic Reset */
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
            max-width: 1100px;
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

        .btn-primary {
            background-color: #003399;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background-color: #002266;
        }

        /* TABLE CARD */
        .table-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* TABLE STYLES */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background-color: #f8f9fa;
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #e9ecef;
            color: #495057;
            font-size: 0.85rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #f1f3f5;
            color: #333;
            vertical-align: middle;
            font-size: 0.95rem;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        table tr:hover {
            background-color: #f8f9fa;
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

        /* ACTION BUTTONS */
        .btn-action {
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 0.8rem;
            text-decoration: none;
            font-weight: 600;
            border: 1px solid transparent;
            transition: all 0.2s;
            cursor: pointer;
            display: inline-block;
            margin-right: 5px;
        }

        .btn-view {
            background: #e3f2fd;
            color: #003399;
            border-color: #bbdefb;
        }

        .btn-view:hover {
            background: #003399;
            color: #fff;
        }

        .btn-delete {
            background: #ffebee;
            color: #d32f2f;
            border-color: #ffcdd2;
            border: none;
        }

        .btn-delete:hover {
            background: #d32f2f;
            color: #fff;
        }

        .btn-invoice {
            background: #e8f5e9;
            color: #2e7d32;
            border-color: #c8e6c9;
        }

        .btn-invoice:hover {
            background: #2e7d32;
            color: #fff;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #888;
            font-size: 1rem;
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
        <div class="page-header">
            <a href="{{ route('dashboard') }}" class="btn-back">← Back to Dashboard</a>
        </div>

        <div class="page-header">
            <h2>Delivery Orders</h2>
            <a href="{{ route('delivery.create') }}" class="btn-primary">+ Submit New DO</a>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>DO Number</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($collection as $item)
                        <tr>
                            <td><strong>{{ $item->PO_Number }}</strong></td>
                            <td>{{ $item->DO_Number ?? 'N/A' }}</td>

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
                            <td>{{ \Carbon\Carbon::parse($item->Created_Date)->format('d M Y') }}</td>

                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('delivery.show', $item->DO_ID) }}"
                                    class="btn-action btn-view">View</a>

                                @if ($item->Status === 'Approved')
                                    <a href="#" class="btn-action btn-invoice">Create Invoice</a>
                                @elseif ($item->Status === 'Submitted' || $item->Status === 'Rejected')
                                    <form action="{{ route('delivery.destroy', $item->DO_ID) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete"
                                            onclick="return confirm('Are you sure you want to delete this?');">Delete</button>
                                    </form>
                                @elseif ($item->Status === 'Under Review')
                                @else
                                    <a href="#" class="btn-action btn-invoice">Create Invoice</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                No delivery orders found. Click "+ Submit New DO" to create your first one!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </main>
    @if (session('success'))
        <script>
            alert(@json(session('success')));
        </script>
    @endif

</body>

</html>
