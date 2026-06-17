<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;
use App\Models\ItemDetail;
use App\Models\AuditLog;
use App\Models\DeliveryOrder;
use Carbon\Carbon;
use Smalot\PdfParser\Parser as PdfParser;

class InvoiceController extends Controller
{
    // 1. Main Invoice List Page
    public function index()
{
    // 1. Get the logged-in user and identify their Supplier Profile
    $user = \Illuminate\Support\Facades\Auth::user();
    $supplier = \App\Models\Supplier::where('User_ID', $user->User_ID)->first();

    if (!$supplier) {
        return redirect()->back()->with('error', 'Supplier profile not found.');
    }

    $supplierId = $supplier->Supplier_ID;

    // 2. Grab all DO_IDs that already have an invoice attached
    $claimedDoIds = Invoice::pluck('DO_ID')->toArray();

    // 3. Fetch approved DOs belonging ONLY to this specific supplier
    $deliveryOrders = DeliveryOrder::select(
            'delivery_order.*',
            'customers.Cust_Name',
            'staff.Staff_Name'
        )
        ->leftJoin('customers', 'delivery_order.Cust_ID', '=', 'customers.Cust_ID')
        ->leftJoin('staff',     'delivery_order.Staff_ID', '=', 'staff.Staff_ID')
        ->where('delivery_order.Supplier_ID', $supplierId) // <-- Filtered by Supplier!
        ->where('delivery_order.Status', 'Approved') 
        ->orderBy('delivery_order.Created_Date', 'desc')
        ->get();

    // 4. Fetch all invoices belonging ONLY to this supplier's Delivery Orders
    $invoices = Invoice::select(
            'invoice.*',
            'delivery_order.DO_Number as do_number'
        )
        ->leftJoin('delivery_order', 'invoice.DO_ID', '=', 'delivery_order.DO_ID')
        ->where('delivery_order.Supplier_ID', $supplierId) // <-- Filtered by Supplier!
        ->orderBy('invoice.Created_At', 'desc')
        ->get();

    // 5. Summary stats (Dynamically calculates based on the filtered vendor invoices)
    $stats = [
        'total'    => $invoices->count(),
        'paid'     => $invoices->where('Status', 'Approved')->sum('Total'),
        'pending'  => $invoices->whereIn('Status', ['Submitted', 'Pending'])->sum('Total'),
        'rejected' => $invoices->where('Status', 'Rejected')->count(),
    ];

    // 6. Keep your Audit Log intact
    $this->auditLog('VIEW', 'Invoice', 'Accessed Invoice Management page');

    // 7. Return your precise view directory structure
    return view('claiminvoice.ApprovedDOList', compact('deliveryOrders', 'invoices', 'stats', 'claimedDoIds'));
}

    // 2. AJAX: Return item_details for a given DO
    public function getDoItems($doId)
    {
        $do = DeliveryOrder::findOrFail($doId);

        $items = ItemDetail::select('item_details.*')
            ->join('invoice', 'item_details.Invoice_ID', '=', 'invoice.Invoice_ID')
            ->where('invoice.DO_ID', $doId)
            ->get();

        $this->auditLog('VIEW', 'InvoiceItemDetails', "Viewed item details for DO_ID: {$doId} (DO: {$do->DO_Number})");

        return response()->json(['items' => $items]);
    }

    // 3. AJAX: Calculate invoice summary
    public function calculateSummary(Request $request)
    {
        $subtotal = 0;
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                $subtotal += ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
            }
        }

        $tax        = $subtotal * 0.06;
        $creditNote = floatval($request->input('credit_note', 0.00));
        $isLate     = $request->input('is_late') === true || $request->input('is_late') === 'true';
        $penalty    = $isLate ? ($subtotal * 0.01) : 0.00;
        $total      = ($subtotal + $tax + $penalty) - $creditNote;

        return response()->json([
            'subtotal'    => number_format($subtotal,   2, '.', ''),
            'tax'         => number_format($tax,        2, '.', ''),
            'credit_note' => number_format($creditNote, 2, '.', ''),
            'penalty'     => number_format($penalty,    2, '.', ''),
            'total'       => number_format($total,      2, '.', '')
        ]);
    }

    // 4. Store new invoice
    public function store(Request $request)
    {
        $request->validate([
            'invoice_num' => 'required|string|unique:invoice,Invoice_Num',
            'do_id'       => 'required|integer|exists:delivery_order,DO_ID',
        ]);

        $subtotal = 0;
        if ($request->has('items')) {
            foreach ($request->items as $item) {
                $subtotal += ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
            }
        }

        $tax        = $subtotal * 0.06;
        $creditNote = floatval($request->input('credit_note', 0.00));
        $discountReason = $request->input('discount_reason', null);
        $isLate     = $request->input('is_late') === 'true';
        $penalty    = $isLate ? ($subtotal * 0.01) : 0.00;
        $total      = ($subtotal + $tax + $penalty) - $creditNote;

        if ($creditNote > 0 && $discountReason) {
            $this->auditLog('DISCOUNT', 'Invoice', "Discount of RM {$creditNote} applied for Invoice: {$request->invoice_num} — Reason: {$discountReason}");
        }

        $proofLink = null;
        if ($request->hasFile('proof_file')) {
            $proofLink = $request->file('proof_file')->store('proofs', 'public');
            $this->auditLog('UPLOAD', 'ProofOfDelivery', "Uploaded proof file for Invoice: {$request->invoice_num}");
        }

        $invoice = Invoice::create([
            'Invoice_Num' => $request->input('invoice_num'),
            'Description' => $request->input('description', 'Invoice Claim Submission via eDOIS'),
            'DO_ID'       => $request->input('do_id'),
            'Issue_Date'  => Carbon::now()->toDateString(),
            'Subtotal'    => $subtotal,
            'Tax'         => $tax,
            'Credit_Note' => $creditNote,
            'Total'       => $total,
            'Status'      => 'Submitted',
            'Reason'      => $discountReason,
            'Created_At'  => Carbon::now()
        ]);

        if ($request->has('items')) {
            foreach ($request->items as $item) {
                ItemDetail::create([
                    'Invoice_ID' => $invoice->Invoice_ID,
                    'Item_Desc'  => $item['desc']       ?? 'N/A',
                    'Quantity'   => $item['quantity']   ?? 0,
                    'Unit_Price' => $item['unit_price'] ?? 0,
                ]);
            }
        }

        if ($proofLink) {
            DeliveryOrder::where('DO_ID', $request->input('do_id'))
                ->update(['Proof_Link' => $proofLink]);
        }

        $this->auditLog('SUBMIT', 'Invoice', "Invoice submitted — Ref: {$invoice->Invoice_Num}, DO_ID: {$invoice->DO_ID}, Total: RM {$total}");

        return response()->json([
            'success'    => true,
            'message'    => 'Invoice submitted successfully.',
            'invoice_id' => $invoice->Invoice_ID,
        ]);
    }

    public function create($doId)
    {
        $do = DeliveryOrder::select('delivery_order.*', 'customers.Cust_Name', 'staff.Staff_Name')
            ->leftJoin('customers', 'delivery_order.Cust_ID', '=', 'customers.Cust_ID')
            ->leftJoin('staff',     'delivery_order.Staff_ID', '=', 'staff.Staff_ID')
            ->where('delivery_order.DO_ID', $doId)
            ->firstOrFail();

        $items = ItemDetail::join('invoice', 'item_details.Invoice_ID', '=', 'invoice.Invoice_ID')
            ->where('invoice.DO_ID', $doId)
            ->select('item_details.*')
            ->get();

        $taxRate = 0.06;
        $taxLabel = "SST (6%)";

        return view('claiminvoice.InvoiceManagement', compact('do', 'items', 'taxRate', 'taxLabel'));
    }

    /**
     * Step 3: Handle Document Extraction/Parsing requests via AJAX upload
     */
    public function extractData(Request $request)
    {
        // 1. Validate the file constraints
        $request->validate([
            'invoice_file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:4096',
        ]);

        $file      = $request->file('invoice_file');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            $this->auditLog('AI_PARSER', 'Invoice', "Initiated data parsing for file: {$file->getClientOriginalName()}");

            if ($extension === 'pdf') {
                $result = $this->parseKtmbInvoicePdf($file->getRealPath());
            } else {
                // PNG/JPG: run through Tesseract OCR, then apply the same
                // line-item heuristics on the recognized text.
                $result = $this->parseInvoiceImageWithOcr($file->getRealPath());
            }

            if (!$result['success']) {
                $this->auditLog('AI_PARSER', 'Invoice', "Parsing failed for file: {$file->getClientOriginalName()} — {$result['message']}");

                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 422);
            }

            $this->auditLog('AI_PARSER', 'Invoice', "Parsed Invoice No: {$result['invoice_num']} with " . count($result['items']) . " item(s) from file: {$file->getClientOriginalName()}");

            return response()->json([
                'success'     => true,
                'invoice_num' => $result['invoice_num'],
                'items'       => $result['items'],
                // True for OCR-based (image) extraction — the frontend can use
                // this to prompt the user to double-check the auto-filled fields,
                // since OCR is less reliable than text extraction from a PDF.
                'ocr_warning' => $result['ocr_warning'] ?? false,
            ]);

        } catch (\Exception $e) {
            Log::error('Invoice extraction failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to parse invoice document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse a KTMB-format invoice PDF and pull out the invoice number
     * and line items (description, quantity, unit price).
     *
     * NOTE: This relies on the KTMB invoice layout being consistent.
     * If KTMB changes their invoice template, the regex patterns below
     * will need to be updated.
     *
     * Requires: composer require smalot/pdfparser
     */
    private function parseKtmbInvoicePdf(string $filePath): array
    {
        $parser = new PdfParser();
        $pdf    = $parser->parseFile($filePath);
        $text   = $pdf->getText();

        if (trim($text) === '') {
            return [
                'success' => false,
                'message' => 'This PDF appears to contain no readable text (it may be a scanned image). Please fill in the details manually.',
            ];
        }

        // --- 1. Invoice Number ---
        // KTMB invoice numbers look like: SO00226IN215 (2 letters, 5 digits, 2 letters, 2-4 digits)
        $invoiceNum = null;
        if (preg_match('/\b([A-Z]{2}\d{5}[A-Z]{2}\d{2,4})\b/', $text, $m)) {
            $invoiceNum = $m[1];
        }

        // --- 2. Line Items ---
        // Each item row looks like:
        //   1 KTMB 038 NEW ( 04 - 05 MAY 2026 ) 3 489.5 1,468.50
        //   <No.> <Description...> <Quantity> <Unit Price> <Amount>
        $items = [];
        $lines = preg_split('/\r\n|\r|\n/', $text);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (preg_match('/^(\d+)\s+(.+?)\s+(\d+(?:\.\d+)?)\s+([\d]+(?:\.\d+)?)\s+([\d,]+\.\d{2})$/', $line, $m)) {
                $items[] = [
                    'Item_Desc'  => trim($m[2]),
                    'Quantity'   => (float) $m[3],
                    'Unit_Price' => (float) $m[4],
                ];
            }
        }

        if (!$invoiceNum && empty($items)) {
            return [
                'success' => false,
                'message' => 'Could not find an invoice number or line items in this PDF. Please check the file or fill in the details manually.',
            ];
        }

        return [
            'success'     => true,
            'invoice_num' => $invoiceNum ?? '',
            'items'       => $items,
        ];
    }

    /**
     * Run a PNG/JPG invoice image through Tesseract OCR and parse the
     * recognized text using the same KTMB layout heuristics.
     *
     * Requires the `tesseract-ocr` binary to be installed on the server:
     *   sudo apt install tesseract-ocr tesseract-ocr-eng
     */
    private function parseInvoiceImageWithOcr(string $filePath): array
    {
        $outputBase = sys_get_temp_dir() . '/invoice_ocr_' . uniqid();

        $command = sprintf(
            'tesseract %s %s 2>&1',
            escapeshellarg($filePath),
            escapeshellarg($outputBase)
        );

        exec($command, $output, $returnCode);

        $textFile = $outputBase . '.txt';

        if ($returnCode !== 0 || !file_exists($textFile)) {
            Log::error('Tesseract OCR failed: ' . implode("\n", $output));

            return [
                'success' => false,
                'message' => 'OCR processing failed on this image. Please try a clearer photo/scan, or fill in the details manually.',
            ];
        }

        $text = file_get_contents($textFile);
        @unlink($textFile);

        return $this->parseInvoiceTextOcr($text);
    }

    /**
     * Parse OCR'd text from a KTMB invoice image.
     *
     * OCR output is noisier than text extracted directly from a PDF:
     * - The invoice number, description, and quantity/price columns often
     *   end up on separate lines instead of one row.
     * - Individual characters (especially in alphanumeric codes like the
     *   invoice number) can be misread, e.g. 'S' <-> '$', 'O' <-> '0'.
     *
     * This method uses the table headers ("Product Description" and
     * "Quantity Unit Price Amount") as anchors to locate the item rows,
     * which is more reliable than matching a single combined row pattern.
     *
     * Because of this, results from image uploads should be treated as
     * "best effort" — the response includes an `ocr_warning` flag so the
     * frontend can prompt the user to double-check the auto-filled fields
     * before submitting.
     */
    private function parseInvoiceTextOcr(string $text): array
    {
        if (trim($text) === '') {
            return [
                'success' => false,
                'message' => 'No readable text could be detected in this image. Please try a clearer, well-lit photo or fill in the details manually.',
            ];
        }

        $lines = preg_split('/\r\n|\r|\n/', $text);
        $lines = array_map('trim', $lines);
        $lines = array_values(array_filter($lines, fn($l) => $l !== ''));

        // --- 1. Invoice Number ---
        // Look for the "Invoice No" label and grab the token that follows.
        // OCR commonly misreads 'S' as '$', so normalize that back.
        $invoiceNum = null;
        foreach ($lines as $line) {
            if (preg_match('/Invoice\s*No\.?\s*[:\-]?\s*([A-Za-z0-9$]{8,14})/i', $line, $m)) {
                $invoiceNum = str_replace('$', 'S', $m[1]);
                break;
            }
        }

        // --- 2. Item descriptions ---
        // Find the "Product Description" header, then read item rows
        // (number + description) until we hit the next section.
        $descriptions = [];
        $descStart = null;
        foreach ($lines as $i => $line) {
            if (preg_match('/Product\s*Description/i', $line)) {
                $descStart = $i + 1;
                break;
            }
        }
        if ($descStart !== null) {
            for ($i = $descStart; $i < count($lines); $i++) {
                $line = $lines[$i];
                if (preg_match('/^(please|line\s*total|invoice|payment)/i', $line)) {
                    break;
                }
                if (preg_match('/^\d+\s+(.+)$/', $line, $m)) {
                    $descriptions[] = trim($m[1]);
                }
            }
        }

        // --- 3. Quantity / Unit Price / Amount rows ---
        // Find the "Quantity Unit Price Amount" header, then read numeric
        // rows until "Line Total".
        $numericRows = [];
        $numStart = null;
        foreach ($lines as $i => $line) {
            if (preg_match('/Quantity\s*Unit\s*Price\s*Amount/i', $line)) {
                $numStart = $i + 1;
                break;
            }
        }
        if ($numStart !== null) {
            for ($i = $numStart; $i < count($lines); $i++) {
                $line = $lines[$i];
                if (preg_match('/^line\s*total/i', $line)) {
                    break;
                }
                if (preg_match('/^(\d+(?:\.\d+)?)\s+([\d]+(?:\.\d+)?)\s+([\d,]+\.\d{2})$/', $line, $m)) {
                    $numericRows[] = [
                        'Quantity'   => (float) $m[1],
                        'Unit_Price' => (float) $m[2],
                    ];
                }
            }
        }

        // --- 4. Combine descriptions + numeric rows into items ---
        $items = [];
        $itemCount = max(count($descriptions), count($numericRows));
        for ($i = 0; $i < $itemCount; $i++) {
            $items[] = [
                'Item_Desc'  => $descriptions[$i] ?? ('Item ' . ($i + 1) . ' (please fill in description)'),
                'Quantity'   => $numericRows[$i]['Quantity'] ?? 0,
                'Unit_Price' => $numericRows[$i]['Unit_Price'] ?? 0,
            ];
        }

        if (!$invoiceNum && empty($items)) {
            return [
                'success' => false,
                'message' => 'Could not extract invoice details from this image clearly enough. Please try a clearer photo or fill in the details manually.',
            ];
        }

        return [
            'success'     => true,
            'invoice_num' => $invoiceNum ?? '',
            'items'       => $items,
            'ocr_warning' => true,
        ];
    }

    // 5. Client-side audit log endpoint
    public function clientAuditLog(Request $request)
    {
        $this->auditLog(
            $request->input('action', 'CLIENT_EVENT'),
            $request->input('module', 'Invoice'),
            $request->input('description', 'Client-side event')
        );
        return response()->json(['logged' => true]);
    }

    // Private: write to audit_log table
    private function auditLog(string $action, string $module, string $description): void
    {
        try {
            AuditLog::create([
                'User_ID'         => Auth::id() ?? 1, 
                'Action'          => strtoupper($action),
                'Affected_Record' => "[{$module}] {$description}",
                'Timestamp'       => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Audit log write failed: ' . $e->getMessage());
        }
    }
}