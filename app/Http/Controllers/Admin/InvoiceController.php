<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = PrismaService::getInvoices();
        $customers = PrismaService::getCustomers();
        $items = PrismaService::getItems();

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
            'customers' => $customers,
            'items' => $items,
            'currency' => config('app.currency_symbol', '£'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customerId' => 'required|integer',
            'dueDate' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unitPrice' => 'required|numeric|min:0',
            'items.*.taxRate' => 'nullable|numeric|min:0|max:100',
            'items.*.itemId' => 'nullable|integer',
        ]);

        $subtotal = 0;
        $taxAmount = 0;

        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unitPrice'];
            $itemTax = $itemTotal * (($item['taxRate'] ?? 0) / 100);
            $subtotal += $itemTotal;
            $taxAmount += $itemTax;
        }

        $invoiceId = PrismaService::createInvoice([
            'userId' => auth()->id(),
            'customerId' => $validated['customerId'],
            'invoiceNumber' => PrismaService::generateInvoiceNumber(),
            'issueDate' => now(),
            'dueDate' => $validated['dueDate'],
            'status' => 'DRAFT',
            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'total' => $subtotal + $taxAmount,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unitPrice'];
            $itemTax = $itemTotal * (($item['taxRate'] ?? 0) / 100);
            
            PrismaService::createInvoiceItem([
                'invoiceId' => $invoiceId,
                'itemId' => $item['itemId'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unitPrice' => $item['unitPrice'],
                'taxRate' => $item['taxRate'] ?? 0,
                'total' => $itemTotal + $itemTax,
            ]);
        }

        return redirect()->back()->with('success', 'Invoice created successfully.');
    }

    public function show($id)
    {
        $invoice = PrismaService::getInvoiceWithItems($id);

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        return response()->json($invoice);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customerId' => 'required|integer',
            'dueDate' => 'required|date',
            'status' => 'required|in:DRAFT,SENT,PAID,OVERDUE,CANCELLED',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unitPrice' => 'required|numeric|min:0',
            'items.*.taxRate' => 'nullable|numeric|min:0|max:100',
            'items.*.itemId' => 'nullable|integer',
        ]);

        $subtotal = 0;
        $taxAmount = 0;

        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unitPrice'];
            $itemTax = $itemTotal * (($item['taxRate'] ?? 0) / 100);
            $subtotal += $itemTotal;
            $taxAmount += $itemTax;
        }

        PrismaService::updateInvoice($id, [
            'customerId' => $validated['customerId'],
            'dueDate' => $validated['dueDate'],
            'status' => $validated['status'],
            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'total' => $subtotal + $taxAmount,
            'notes' => $validated['notes'] ?? null,
        ]);

        PrismaService::deleteInvoiceItems($id);

        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unitPrice'];
            $itemTax = $itemTotal * (($item['taxRate'] ?? 0) / 100);
            
            PrismaService::createInvoiceItem([
                'invoiceId' => $id,
                'itemId' => $item['itemId'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unitPrice' => $item['unitPrice'],
                'taxRate' => $item['taxRate'] ?? 0,
                'total' => $itemTotal + $itemTax,
            ]);
        }

        return redirect()->back()->with('success', 'Invoice updated successfully.');
    }

    public function destroy($id)
    {
        PrismaService::deleteInvoice($id);

        return redirect()->back()->with('success', 'Invoice deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:DRAFT,SENT,PAID,OVERDUE,CANCELLED',
        ]);

        PrismaService::updateInvoice($id, ['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Invoice status updated.');
    }
}
