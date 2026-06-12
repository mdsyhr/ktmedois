<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM eDOIS - Vendor Portal (Enhanced)</title>
    <style>
        /* --- CSS STYLES --- */
        :root {
            --primary-blue: #003399;
            /* KTM Blue */
            --accent-yellow: #FFCC00;
            /* KTM Yellow */
            --white: #ffffff;
            --light-gray: #f4f7f6;
            --text-dark: #333;
            --border-color: #ddd;
            --error-red: #d32f2f;
            --success-green: #2e7d32;
            --warning-orange: #ff9800;
        }

        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--light-gray);
            color: var(--text-dark);
            height: 100vh;
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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-img {
            width: 80px;
            /* Adjusted width for landscape logos */
            height: 50px;
            background-color: var(--white);
            border-radius: 5px;
            object-fit: contain;
            border: 2px solid var(--accent-yellow);
            padding: 2px;
        }

        .system-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .system-title span {
            color: var(--accent-yellow);
        }

        /* --- TASKBAR --- */
        .taskbar {
            background-color: #002266;
            /* Darker blue for taskbar */
            padding: 0;
            display: flex;
            align-items: stretch;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .taskbar-nav {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .taskbar-item {
            position: relative;
            flex: 1;
            /* Makes both items equal width */
            text-align: center;
        }

        .taskbar-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px 20px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
            height: 100%;
            width: 100%;
        }

        .taskbar-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white);
        }

        .taskbar-link.active {
            color: var(--accent-yellow);
            border-bottom-color: var(--accent-yellow);
            background-color: rgba(255, 204, 0, 0.1);
        }

        .taskbar-link .icon {
            margin-right: 8px;
            font-size: 1.1rem;
        }

        /* --- SIMULATION CONTROLS (For Testing E2) --- */
        .simulation-controls {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-switch {
            cursor: pointer;
            background: #333;
            border-radius: 10px;
            padding: 2px;
            width: 40px;
            height: 20px;
            position: relative;
            transition: 0.3s;
        }

        .toggle-switch.active {
            background: var(--accent-yellow);
        }

        .toggle-knob {
            width: 16px;
            height: 16px;
            background: white;
            border-radius: 50%;
            position: absolute;
            left: 2px;
            transition: 0.3s;
        }

        .toggle-switch.active .toggle-knob {
            left: 22px;
        }

        /* --- MAIN CONTAINER --- */
        main {
            flex: 1;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            background-color: var(--white);
            width: 100%;
            max-width: 800px;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-top: 5px solid var(--accent-yellow);
            position: relative;
        }

        /* --- VIEWS --- */
        .view-section {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .view-section.active {
            display: block;
        }

        h2 {
            color: var(--primary-blue);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--light-gray);
            padding-bottom: 10px;
        }

        /* --- FORMS & INPUTS --- */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="file"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 1rem;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: var(--primary-blue);
            outline: none;
            box-shadow: 0 0 5px rgba(0, 51, 153, 0.2);
        }

        /* Error state styling */
        input.error-input,
        select.error-input {
            border-color: var(--error-red);
            background-color: #fff8f8;
        }

        .error-text {
            color: var(--error-red);
            font-size: 0.9rem;
            margin-top: 5px;
            display: none;
            font-weight: bold;
        }

        .file-upload-box {
            border: 2px dashed var(--primary-blue);
            padding: 20px;
            text-align: center;
            background-color: #f0f4ff;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        /* --- BUTTONS --- */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: background 0.3s;
            display: block;
            /* <--- ADD THIS: Makes margin-bottom work */
            text-align: center;
            /* <--- ADD THIS: Centers the text inside the button */
        }


        .btn-primary {
            background-color: var(--primary-blue);
            color: var(--white);
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #002266;
        }

        .btn-secondary {
            background-color: #666;
            color: white;
            margin-right: 10px;
        }

        .dashboard-btn {
            background-color: var(--accent-yellow);
            color: var(--primary-blue);
            margin-bottom: 30px;
            width: 100%;
        }

        /* --- MODALS --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-box {
            background: var(--white);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 90%;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .restriction-modal .modal-box {
            background: #ffebee;
            border: 1px solid #ef5350;
        }

        .success-modal .modal-box {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
        }

        .error-modal .modal-box {
            background: #ffebee;
            border: 1px solid #ef5350;
        }

        /* --- ALERTS & STATUS --- */
        .status-card {
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .success-icon {
            color: var(--success-green);
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-blue);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        /* --- TABLE --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-blue);
            color: white;
        }


        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 1.2rem;
            border-top: 2px solid var(--primary-blue);
            margin-top: 10px;
            padding-top: 15px;
            color: var(--primary-blue);
        }

        .status-tracker {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            position: relative;
        }

        .status-tracker::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 10%;
            right: 10%;
            height: 3px;
            background: #ddd;
            z-index: 1;
        }

        .status-step {
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        .status-icon {
            width: 40px;
            height: 40px;
            background: white;
            border: 3px solid #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            color: #666;
        }

        .status-step.active .status-icon {
            border-color: var(--primary-blue);
            background: var(--primary-blue);
            color: white;
        }

        .status-step.completed .status-icon {
            border-color: var(--success-green);
            background: var(--success-green);
            color: white;
        }

        .status-step.pending .status-icon {
            border-color: var(--warning-orange);
            background: var(--warning-orange);
            color: white;
        }

        .status-label {
            font-size: 0.9rem;
            color: #666;
        }

        .status-step.active .status-label {
            color: var(--primary-blue);
            font-weight: bold;
        }

        .status-step.completed .status-label {
            color: var(--success-green);
            font-weight: bold;
        }


        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="logo-container">
            <img src="https://www.bernama.com/storage/photos/b2b32fe44095c63a213df09421007c3f64a8454176890"
                alt="KTMB Logo" class="logo-img">
            <div class="system-title">KTM <span>eDOIS</span></div>
        </div>

        <div class="simulation-controls">
            <span>Vendor Status:</span>
            <span id="status-label" style="font-weight: bold; color: #aaffaa;">ACTIVE</span>
            <div class="toggle-switch active" id="status-toggle">
                <div class="toggle-knob"></div>
            </div>
        </div>
    </header>

    <!-- TASKBAR WITH DO & INVOICE LINKS - 50/50 SPLIT -->
    <nav class="taskbar">
        <ul class="taskbar-nav">
            <li class="taskbar-item">
                <a href="#" class="taskbar-link do-tab active">
                    <span class="icon">📋</span> Delivery Order (DO)
                </a>
            </li>
            <li class="taskbar-item">
                <a href="#" class="taskbar-link invoice-tab">
                    <span class="icon">🧾</span> Invoice
                </a>
            </li>
        </ul>
    </nav>

    <main>
        <div class="container">

            <section id="page-dashboard" class="">


                <h3>Delivery Order Details</h3>
            </section>
            <table>
                <tr>
                    <th>DO Number</th>
                    <td>{{ $item->DO_Number }}</td>
                </tr>
                <tr>
                    <th>PO Number</th>
                    <td>{{ $item->PO_Number }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ $item->Status }}</td>
                </tr>
                <tr>
                    <th>Created Date</th>
                    <td>{{ $item->Created_Date }}</td>
                </tr>
                <tr>
                    <th>Reason</th>
                    <td>{{ $item->Reason ?? 'Not applicable' }}</td>
                </tr>
            </table>

            <a href="{{ route('dashboard') }}" class="btn btn-cancel">Cancel</a>
</body>

</html>
