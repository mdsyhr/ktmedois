<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        $user = Auth::user();

        $supplier = DB::table('supplier')
            ->where('User_ID', $user->User_ID)
            ->first();

        if (!$supplier) {
            return redirect()->back()->with('error', 'Supplier profile not found.');
        }

        $supplierId = $supplier->Supplier_ID;

        // Fetch delivery orders
        $collection = DB::table('delivery_order')
            ->where('Supplier_ID', $supplierId)
            ->orderBy('Created_Date', 'desc')
            ->get();

        // Pass only the necessary data to the view
        return view('deliveryOrder.list', [
            'collection'   => $collection,
            'supplierName' => $supplier->Supplier_Name ?? 'Supplier',
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $customers = DB::table('customers')->get();
        return view('deliveryOrder.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $item = DB::table('delivery_order')->where('DO_ID', $id)->first();
        return view('deliveryOrder.show', ['item' => $item]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        // 1. Find the record first to ensure it exists
        $delivery = DB::table('delivery_order')->where('DO_ID', $id)->first();

        // 2. Check if the record was actually found
        if ($delivery) {

            // NOTE: If you decided to store files on the server (using ->store())
            // instead of saving them as BLOBs in the database, you should delete
            // the physical files here to save space:
            /*
        if ($delivery->DO_Link && \Storage::disk('public')->exists($delivery->DO_Link)) {
            \Storage::disk('public')->delete($delivery->DO_Link);
        }
        if ($delivery->Proof_Link && \Storage::disk('public')->exists($delivery->Proof_Link)) {
            \Storage::disk('public')->delete($delivery->Proof_Link);
        }
        */

            // 3. Delete the record from the database
            DB::table('delivery_order')->where('DO_ID', $id)->delete();

            // 4. Redirect back with a success message
            // (Make sure 'delivery.list' matches the name in your routes/web.php)
            return redirect()->route('delivery.list')->with('success', 'Delivery Order deleted successfully.');
        }

        // If the record doesn't exist, redirect with an error
        return redirect()->route('delivery.list')->with('error', 'Delivery Order not found.');
    }

    public function insert(Request $request)
    {
        // 1. Get the logged-in user
        $user = Auth::user();

        // 2. Fetch the Supplier ID directly from the 'supplier' table using DB::table
        $supplier = DB::table('supplier')
            ->where('User_ID', $user->User_ID)
            ->first();

        // Safety check
        if (!$supplier) {
            return redirect()->back()->with('error', 'Supplier profile not found.');
        }

        // Extract the ID
        $supplierId = $supplier->Supplier_ID;


        // 2. Validate the form and files
        // Note: Adjust 'max:10240' (10MB) based on your PHP server's upload_max_filesize limit
        $validated = $request->validate([
            'PO_Number' => 'required|string|max:255',
            'DO_File'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'PO_File'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'Cust_ID'   => 'required|exists:customers,Cust_ID',
        ]);

        // 3. Read the raw binary data from the uploaded files
        $doFileContent = $request->file('DO_File')->get();
        $poFileContent = $request->file('PO_File')->get();

        // 4. Save directly to the database
        DeliveryOrder::create([
            'Supplier_ID' => $supplierId,
            'Cust_ID'     => $validated['Cust_ID'],
            'DO_Number' => 'DO-' . now()->format('ymdHis'), // Generate a unique DO number
            'PO_Number'   => $validated['PO_Number'],
            'DO_Link'      => $doFileContent,
            'Proof_Link'     => $poFileContent,
            'Status'      => 'Pending',
            'Created_Date' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Delivery Order submitted successfully!');
    }


    public function showFile(string $id, string $type)
    {
        // 1. Fetch the record
        $item = DB::table('delivery_order')->where('DO_ID', $id)->first();

        if (!$item) {
            abort(404, 'Delivery Order not found');
        }

        $content = null;
        $filename = 'document';

        // 2. Determine which BLOB column to fetch based on the URL parameter
        if ($type === 'do') {
            $content = $item->DO_Link;
            $filename = 'DO_' . ($item->DO_Number ?? $item->DO_ID);
        } elseif ($type === 'proof') {
            $content = $item->Proof_Link;
            $filename = 'Proof_' . ($item->DO_Number ?? $item->DO_ID);
        } else {
            abort(404, 'Invalid file type');
        }

        // 3. Check if the file actually exists in the database
        if (!$content) {
            abort(404, 'File content is empty or not found');
        }

        // 4. ⚠️ CONVERT RESOURCE TO STRING IF NEEDED
        if (is_resource($content)) {
            $content = stream_get_contents($content);
            fclose($content); // Close the resource after reading
        }

        // 5. Detect the MIME type (e.g., application/pdf, image/jpeg) from the binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($content) ?: 'application/octet-stream';

        // 6. Return the binary data as a response with proper headers
        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Content-Length' => strlen($content),
        ]);
    }
}
