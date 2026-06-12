<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <main>
        <div>
            <h2>Submit Delivery Order</h2>

            <form action="{{ route('delivery.insert') }}" method="POST" enctype="multipart/form-data">
                @csrf


                <div class="form-group">
                    <label for="PO_Number">Purchase Order (PO) Number <span class="required">*</span></label>
                    <input type="text" id="PO_Number" name="PO_Number" placeholder="e.g., PO-2024-1234" required>
                    <div class="validation-text">Validating against procurement records...</div>
                </div>


                <div class="form-group">
                    <label for="DO_File">Upload DO Document <span class="required">*</span></label>
                    <div class="file-upload-box">
                        <input type="file" id="DO_File" name="DO_File" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="PO_File">Upload Proof of Delivery (PDF/JPG):<span class="required">*</span></label>
                    <div class="file-upload-box">
                        <input type="file" id="PO_File" name="PO_File" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="customer">Select Customer <span class="required">*</span></label>
                    <select id="customer" name="Cust_ID" class="form-control" required>
                        <option value="">-- Select Customer --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->Cust_ID }}">
                                {{ $customer->name ?? ($customer->company_name ?? $customer->Cust_Name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="btn-group">
                    <a href="{{ route('dashboard') }}" class="btn btn-cancel">Cancel</a>
                    <button type="submit" class="btn btn-success">Submit Delivery Order</button>
                </div> {{-- <-- Added this closing div --}}
            </form>

        </div>
    </main>
</body>

</html>
