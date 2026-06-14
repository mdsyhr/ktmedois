<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KTM eDOIS - Submit Delivery Order</title>
    <style>
        /* Basic Reset */
        * {
            box-sizing: border-box;
        }

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

        .ktmb-user-info a:hover {
            text-decoration: underline;
        }

        /* MAIN CONTENT */
        .content-wrapper {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* FORM CARD */
        .form-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .form-card-header {
            background: linear-gradient(135deg, #003399, #002266);
            color: white;
            padding: 25px 35px;
        }

        .form-card-header h2 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .form-card-header p {
            margin: 5px 0 0 0;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .form-card-body {
            padding: 35px;
        }

        /* FORM STYLES */
        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .required {
            color: #d32f2f;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.95rem;
            color: #333;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: #fafafa;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #003399;
            box-shadow: 0 0 0 3px rgba(0, 51, 153, 0.1);
            background-color: #fff;
        }

        .validation-text {
            font-size: 0.8rem;
            color: #888;
            margin-top: 6px;
            font-style: italic;
        }

        /* FILE UPLOAD - Pure CSS */
        .file-upload-box {
            position: relative;
            border: 2px dashed #c5cae9;
            border-radius: 8px;
            padding: 25px;
            background: #f5f7ff;
            transition: all 0.2s;
        }

        .file-upload-box:hover {
            border-color: #003399;
            background: #eef1ff;
        }

        .file-upload-box input[type="file"] {
            width: 100%;
            padding: 10px;
            font-size: 0.95rem;
            color: #333;
            cursor: pointer;
            background: transparent;
            border: none;
        }

        /* Style the native file button */
        .file-upload-box input[type="file"]::file-selector-button {
            background: linear-gradient(135deg, #003399, #002266);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            margin-right: 15px;
            transition: all 0.2s;
            font-family: inherit;
        }

        .file-upload-box input[type="file"]::file-selector-button:hover {
            background: linear-gradient(135deg, #002266, #001a4d);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 51, 153, 0.3);
        }

        .file-upload-hint {
            display: block;
            margin-top: 10px;
            color: #888;
            font-size: 0.8rem;
            font-style: italic;
        }

        /* BUTTONS */
        .btn-group {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eee;
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

        .btn-success {
            background: linear-gradient(135deg, #003399, #002266);
            color: white;
            box-shadow: 0 2px 8px rgba(0, 51, 153, 0.3);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #002266, #001a4d);
            box-shadow: 0 4px 12px rgba(0, 51, 153, 0.4);
            transform: translateY(-1px);
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

        /* Responsive */
        @media (max-width: 600px) {
            .ktmb-header {
                flex-direction: column;
                gap: 10px;
                padding: 15px;
            }

            .form-card-body {
                padding: 20px;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
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
            <a href="{{ route('delivery.list') }}" class="btn-back">← Back to List</a>
        </div>

        <main class="content-wrapper">
            <div class="form-card">
                <div class="form-card-header">
                    <h2>Submit Delivery Order</h2>
                    <p>Fill in the details below to submit a new delivery order</p>
                </div>

                <div class="form-card-body">
                    <form action="{{ route('delivery.insert') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="PO_Number">Purchase Order (PO) Number <span class="required">*</span></label>
                            <input type="text" id="PO_Number" name="PO_Number" class="form-control"
                                placeholder="e.g., PO20241234" required>
                            <div class="validation-text"><span id="poMessage"></span></div>
                        </div>

                        <div class="form-group">
                            <label for="DO_File">Upload DO Document <span class="required">*</span></label>
                            <div class="file-upload-box">
                                <input type="file" id="DO_File" name="DO_File" accept=".pdf,.jpg,.jpeg,.png"
                                    required>
                                <span class="file-upload-hint">Accepted formats: PDF, JPG, PNG (Max 10MB)</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="PO_File">Upload Proof of Delivery <span class="required">*</span></label>
                            <div class="file-upload-box">
                                <input type="file" id="PO_File" name="PO_File" accept=".pdf,.jpg,.jpeg,.png"
                                    required>
                                <span class="file-upload-hint">Accepted formats: PDF, JPG, PNG (Max 10MB)</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="customer">Select Customer <span class="required">*</span></label>
                            <select id="customer" name="Cust_ID" class="form-control" required>
                                <option value="">-- Select Customer --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->Cust_ID }}">
                                        {{ $customer->name ?? ($customer->company_name ?? $customer->Cust_Name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="btn-group">
                            <a href="{{ route('delivery.list') }}" class="btn btn-cancel">Cancel</a>
                            <button type="submit" class="btn btn-success">Submit Delivery Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <script>
            //ini dia convert to uppercase dengan tambah dash automatic
            function formatPoNumber(input) {
                let value = input.value.toUpperCase();
                if (value.startsWith('PO') && !value.startsWith('PO-')) {
                    if (value.length >= 2) {
                        value = 'PO-' + value.substring(2);
                    }
                }
                input.value = value;
            }

            //ini dia validate format PO number
            function validatePoFormat(poValue) {
                const pattern = /^PO-2025\d{1,6}$/;
                return pattern.test(poValue);
            }

            // Validate file extension - only allow PDF and images
            function validateFileExtension(input) {
                const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
                const file = input.files[0];

                if (!file) {
                    return true; // No file selected, validation passes
                }

                const fileName = file.name.toLowerCase();
                const fileExtension = fileName.split('.').pop();

                if (!allowedExtensions.includes(fileExtension)) {
                    alert(`Invalid file type: .${fileExtension}\n\nPlease upload only PDF, JPG, JPEG, or PNG files.`);
                    input.value = ''; // Clear the invalid file
                    return false;
                }

                return true;
            }

            function checkPoNumber() {
                let poInput = document.getElementById('PO_Number');
                let poValue = poInput.value.trim();
                let messageSpan = document.getElementById('poMessage');
                let submitBtn = document.querySelector('button[type="submit"]');

                // dia clearkan message
                messageSpan.innerHTML = '';
                if (submitBtn) submitBtn.disabled = false;

                // Empty field – no validation yet
                if (poValue === '') {
                    return;
                }

                // 1. Format validation
                if (!validatePoFormat(poValue)) {
                    messageSpan.innerHTML =
                        '<span style="color:red;">Invalid format. Use PO-YYYYNNN (e.g., PO-20251234) with year + up to 6 digits.</span>';
                    if (submitBtn) submitBtn.disabled = true;
                    return;
                }

                // 2. Check existence in database (AJAX)
                fetch('{{ route('delivery.checkPo') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            PO_Number: poValue
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            messageSpan.innerHTML =
                                '<span style="color:red;">PO Number already exists in the database.</span>';
                            if (submitBtn) submitBtn.disabled = true;
                        } else {
                            messageSpan.innerHTML =
                                '<span style="color:green;">PO Number is available and format is correct.</span>';
                            if (submitBtn) submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (submitBtn) submitBtn.disabled = false;
                    });
            }

            // ALL event listeners in ONE DOMContentLoaded
            document.addEventListener('DOMContentLoaded', function() {
                // PO Number validation events
                let poInput = document.getElementById('PO_Number');
                if (poInput) {
                    poInput.addEventListener('input', function() {
                        formatPoNumber(this);
                    });
                    poInput.addEventListener('blur', checkPoNumber);
                }

                // File validation events
                const doFileInput = document.getElementById('DO_File');
                const poFileInput = document.getElementById('PO_File');

                if (doFileInput) {
                    doFileInput.addEventListener('change', function() {
                        validateFileExtension(this);
                    });
                }

                if (poFileInput) {
                    poFileInput.addEventListener('change', function() {
                        validateFileExtension(this);
                    });
                }
            });
        </script>
</body>

</html>
