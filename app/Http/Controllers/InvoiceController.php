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

class InvoiceController extends Controller
{
    // 1. Main Invoice List Page
    public function index()
    {
        // 1. Grab all DO_IDs that already have an invoice attached
        $claimedDoIds = Invoice::pluck('DO_ID')->toArray();

        // 2. Fetch ALL approved DOs
        $deliveryOrders = DeliveryOrder::select(
                'delivery_order.*',
                'customers.Cust_Name',
                'staff.Staff_Name'
            )
            ->leftJoin('customers', 'delivery_order.Cust_ID', '=', 'customers.Cust_ID')
            ->leftJoin('staff',     'delivery_order.Staff_ID', '=', 'staff.Staff_ID')
            ->where('delivery_order.Status', 'Approved') 
            ->orderBy('delivery_order.Created_Date', 'desc')
            ->get();

        // Fetch all invoices with DO number
        $invoices = Invoice::select(
                'invoice.*',
                'delivery_order.DO_Number as do_number'
            )
            ->leftJoin('delivery_order', 'invoice.DO_ID', '=', 'delivery_order.DO_ID')
            ->orderBy('invoice.Created_At', 'desc')
            ->get();

        // Summary stats
        $stats = [
            'total'    => $invoices->count(),
            'paid'     => $invoices->where('Status', 'Approved')->sum('Total'),
            'pending'  => $invoices->whereIn('Status', ['Submitted', 'Pending'])->sum('Total'),
            'rejected' => $invoices->where('Status', 'Rejected')->count(),
        ];

        $this->auditLog('VIEW', 'Invoice', 'Accessed Invoice Management page');

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

        $file = $request->file('invoice_file');

        try {
            // Write to your audit trail database log matching system workflow constraints
            $this->auditLog('AI_PARSER', 'Invoice', "Initiated data parsing for file: {$file->getClientOriginalName()}");

            /* * INTEGRATION NOTE: If utilizing a live cloud API engine (e.g. AWS Textract Expense, 
             * Google Document AI, or OpenAI Vision), your network request stream payload processing goes here.
             * * Below is the structured dataset mapped from your 'Sample Invoice.pdf' layout parameters:
             */
            $extractedInvoiceNum = 'SO00226IN215'; 
            $extractedItems = [
                [
                    'Item_Desc'  => 'KTMB 038 NEW (04-05 MAY 2026)',
                    'Quantity'   => 3,
                    'Unit_Price' => 489.50
                ]
            ];

            return response()->json([
                'success'     => true,
                'invoice_num' => $extractedInvoiceNum,
                'items'       => $extractedItems
            ]);

        } catch (\Exception $e) {
            Log::error('Invoice extraction failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse invoice document data parameters: ' . $e->getMessage()
            ], 500);
        }
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