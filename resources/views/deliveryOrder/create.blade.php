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

        /* FILE UPLOAD */
        .file-upload-box {
            position: relative;
            border: 2px dashed #c5cae9;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            background: #f5f7ff;
            transition: all 0.2s;
            cursor: pointer;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .file-upload-box:hover {
            border-color: #003399;
            background: #eef1ff;
        }

        .file-upload-box.has-file {
            border-color: #2e7d32;
            background: #e8f5e9;
        }

        .file-upload-box input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: block;
        }

        .file-upload-text {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .file-upload-hint {
            color: #999;
            font-size: 0.8rem;
        }

        .file-name-display {
            display: none;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: white;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 0.9rem;
            color: #2e7d32;
            font-weight: 600;
        }

        .file-name-display.show {
            display: flex;
        }

        .file-name-display .file-icon {
            font-size: 1.2rem;
        }

        .file-name-display .file-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .file-name-display .file-name {
            font-weight: 600;
            color: #333;
        }

        .file-name-display .file-size {
            font-size: 0.75rem;
            color: #888;
            font-weight: 400;
        }

        .file-name-display .change-file {
            margin-left: auto;
            padding: 4px 12px;
            background: #003399;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .file-name-display .change-file:hover {
            background: #002266;
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
        <div class="form-card">
            <div class="form-card-header">
                <h2>Submit Delivery Order</h2>
                <p>Fill in the details below to submit a new delivery order</p>
            </div>

            <div class="form-card-body">
                <form action="{{ route('delivery.insert') }}" method="POST" enctype="multipart/form-data" id="deliveryForm">
                    @csrf

                    <div class="form-group">
                        <label for="PO_Number">Purchase Order (PO) Number <span class="required">*</span></label>
                        <input type="text" id="PO_Number" name="PO_Number" class="form-control"
                            placeholder="e.g., PO-2024-1234" required>
                        <div class="validation-text">Validating against procurement records...</div>
                    </div>

                    <div class="form-group">
                        <label for="DO_File">Upload DO Document <span class="required">*</span></label>
                        <div class="file-upload-box" id="doUploadBox">
                            <input type="file" id="DO_File" name="DO_File" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="upload-prompt">
                                <span class="file-upload-icon">📁</span>
                                <div class="file-upload-text">Click or drag file to upload</div>
                                <div class="file-upload-hint">PDF, JPG, PNG (Max 10MB)</div>
                            </div>
                            <div class="file-name-display" id="doFileName">
                                <span class="file-icon">📄</span>
                                <div class="file-info">
                                    <span class="file-name"></span>
                                    <span class="file-size"></span>
                                </div>
                                <button type="button" class="change-file" onclick="document.getElementById('DO_File').click()">Change</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="PO_File">Upload Proof of Delivery (PDF/JPG): <span class="required">*</span></label>
                        <div class="file-upload-box" id="proofUploadBox">
                            <input type="file" id="PO_File" name="PO_File" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="upload-prompt">
                                <span class="file-upload-icon">📁</span>
                                <div class="file-upload-text">Click or drag file to upload</div>
                                <div class="file-upload-hint">PDF, JPG, PNG (Max 10MB)</div>
                            </div>
                            <div class="file-name-display" id="proofFileName">
                                <span class="file-icon">📄</span>
                                <div class="file-info">
                                    <span class="file-name"></span>
                                    <span class="file-size"></span>
                                </div>
                                <button type="button" class="change-file" onclick="document.getElementById('PO_File').click()">Change</button>
                            </div>
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
        // Handle DO File upload
        document.getElementById('DO_File').addEventListener('change', function(e) {
            handleFileSelect(e, 'doUploadBox', 'doFileName');
        });

        // Handle Proof File upload
        document.getElementById('PO_File').addEventListener('change', function(e) {
            handleFileSelect(e, 'proofUploadBox', 'proofFileName');
        });

        function handleFileSelect(event, boxId, displayId) {
            const file = event.target.files[0];
            const uploadBox = document.getElementById(boxId);
            const fileNameDisplay = document.getElementById(displayId);
            const uploadPrompt = uploadBox.querySelector('.upload-prompt');

            if (file) {
                // Format file size
                const fileSize = formatFileSize(file.size);

                // Update display
                fileNameDisplay.querySelector('.file-name').textContent = file.name;
                fileNameDisplay.querySelector('.file-size').textContent = fileSize;

                // Show file name display, hide prompt
                fileNameDisplay.classList.add('show');
                uploadPrompt.style.display = 'none';
                uploadBox.classList.add('has-file');
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Drag and drop visual feedback
        ['doUploadBox', 'proofUploadBox'].forEach(boxId => {
            const box = document.getElementById(boxId);
            const input = box.querySelector('input[type="file"]');

            box.addEventListener('dragover', (e) => {
                e.preventDefault();
                box.style.borderColor = '#003399';
                box.style.background = '#eef1ff';
            });

            box.addEventListener('dragleave', (e) => {
                e.preventDefault();
                if (!box.classList.contains('has-file')) {
                    box.style.borderColor = '#c5cae9';
                    box.style.background = '#f5f7ff';
                }
            });

            box.addEventListener('drop', (e) => {
                e.preventDefault();
                if (!box.classList.contains('has-file')) {
                    box.style.borderColor = '#c5cae9';
                    box.style.background = '#f5f7ff';
                }
            });
        });
    </script>
</body>

</html>
