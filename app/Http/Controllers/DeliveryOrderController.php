<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;


class DeliveryOrderController extends Controller
{
    public function list()
    {
        $user = Auth::user();
        $supplier = Supplier::where('User_ID', $user->User_ID)->first();

        if (!$supplier) {
            return redirect()->back()->with('error', 'Supplier profile not found.');
        }

        $supplierId = $supplier->Supplier_ID;

        $collection = DeliveryOrder::where('Supplier_ID', $supplierId)
            ->orderBy('Created_Date', 'desc')
            ->get();

        return view('deliveryOrder.list', [
            'collection'   => $collection,
            'supplierName' => $supplier->Supplier_Name ?? 'Supplier',
        ]);
    }

    public function create()
    {
        $customers = Customer::all();
        return view('deliveryOrder.create', compact('customers'));
    }

    public function show(string $id)
    {
        $item = DeliveryOrder::findOrFail($id);
        return view('deliveryOrder.show', ['item' => $item]);
    }

    public function destroy(string $id)
    {
        date_default_timezone_set('Asia/Kuala_Lumpur');
        $user = Auth::user();
        $supplier = Supplier::where('User_ID', $user->User_ID)->first();

        if (!$supplier) {
            return redirect()->back()->with('error', 'Supplier profile not found.');
        }

        $delivery = DeliveryOrder::find($id);
        if ($delivery) {
            $delivery->delete();
            $affectedRecord = "[DeliveryOrder] supplier {$supplier->Supplier_ID} delete {$delivery->PO_Number}";

            AuditLog::create([
                'User_ID'         => $user->User_ID,
                'Action'          => 'DELETE',
                'Affected_Record' => $affectedRecord,
                'Timestamp'       => now(),
            ]);

            return redirect()->route('delivery.list')->with('success', 'Delivery Order deleted successfully.');
        }
        return redirect()->route('delivery.list')->with('error', 'Delivery Order not found.');
    }

    public function insert(Request $request)
    {
        $user = Auth::user();
        $supplier = Supplier::where('User_ID', $user->User_ID)->first();

        if (!$supplier) {
            return redirect()->back()->with('error', 'Supplier profile not found.');
        }

        $supplierId = $supplier->Supplier_ID;

        $validated = $request->validate([
            'PO_Number' => 'required|string|max:255',
            'DO_File'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'PO_File'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'Cust_ID'   => 'required|exists:customers,Cust_ID',
        ]);

        $doFileContent = $request->file('DO_File')->get();
        $poFileContent = $request->file('PO_File')->get();
        date_default_timezone_set('Asia/Kuala_Lumpur');

        DeliveryOrder::create([
            'Supplier_ID' => $supplierId,
            'Cust_ID'     => $validated['Cust_ID'],
            'DO_Number' => 'DO-' . date('ymdHis'),
            'PO_Number'   => $validated['PO_Number'],
            'DO_Link'      => $doFileContent,
            'Proof_Link'     => $poFileContent,
            'Status'      => 'Submitted',
            'Created_Date' => now(),
        ]);

        $affectedRecord = sprintf(
            "[DeliveryOrder] supplier %s create %s",
            $supplierId,
            $validated['PO_Number']
        );

        AuditLog::create([
            'User_ID'         => $user->User_ID,
            'Action'          => 'CREATE',
            'Affected_Record' => $affectedRecord,
            'Timestamp'       => now(),
        ]);

        return redirect()->route('delivery.list')->with('success', 'Delivery Order submitted successfully!');
    }

    public function showFile(string $id, string $type)
    {
        $item = DeliveryOrder::findOrFail($id);
        if (!$item) {
            abort(404, 'Delivery Order not found');
        }

        $content = null;
        $filename = 'document';

        if ($type === 'do') {
            $content = $item->DO_Link;
            $filename = 'DO_' . ($item->DO_Number ?? $item->DO_ID);
        } elseif ($type === 'proof') {
            $content = $item->Proof_Link;
            $filename = 'Proof_' . ($item->DO_Number ?? $item->DO_ID);
        } else {
            abort(404, 'Invalid file type');
        }

        if (!$content) {
            abort(404, 'File content is empty or not found');
        }

        if (is_resource($content)) {
            $content = stream_get_contents($content);
            fclose($content);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($content) ?: 'application/octet-stream';

        if ($mimeType === 'application/pdf') {
            $filename .= '.pdf';
        }

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Content-Length' => strlen($content),
        ]);
    }
    public function checkPoNumber(Request $request)
    {
        $poNumber = $request->input('PO_Number');
        $exists = DeliveryOrder::where('PO_Number', $poNumber)->exists();
        return response()->json(['exists' => $exists]);
    }
}
