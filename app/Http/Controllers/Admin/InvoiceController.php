<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use App\Services\StatusWorkflowService;
use App\Services\InvoiceExportService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = PrismaService::getInvoices();
        $customers = PrismaService::getCustomers();
        $items = PrismaService::getItems();
        $salesCategories = PrismaService::getSalesCategories();

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
            'customers' => $customers,
            'items' => $items,
            'salesCategories' => $salesCategories,
            'currency' => config('app.currency_symbol', '£'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customerId' => 'required|integer',
            'dueDate' => 'required|date',
            'salesCategoryId' => 'nullable|integer|exists:SalesCategory,id',
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
            'salesCategoryId' => $validated['salesCategoryId'] ?? null,
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
        $invoice = PrismaService::getInvoice($id);
        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        $validated = $request->validate([
            'customerId' => 'required|integer',
            'dueDate' => 'required|date',
            'status' => 'required|in:DRAFT,PENDING,OPEN,PARTIALLY_PAID,PAID,OVERDUE,UNPAID,VOID,REFUNDED',
            'salesCategoryId' => 'nullable|integer|exists:SalesCategory,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unitPrice' => 'required|numeric|min:0',
            'items.*.taxRate' => 'nullable|numeric|min:0|max:100',
            'items.*.itemId' => 'nullable|integer',
        ]);

        // Validate status transition
        if ($invoice->status !== $validated['status']) {
            if (!StatusWorkflowService::isValidInvoiceTransition($invoice->status, $validated['status'])) {
                return redirect()->back()->with('error', 'Invalid status transition from ' . $invoice->status . ' to ' . $validated['status']);
            }
        }

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
            'salesCategoryId' => $validated['salesCategoryId'] ?? null,
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

    public function exportPdf($id)
    {
        try {
            $pdf = InvoiceExportService::generateSinglePdf($id);
            $invoice = PrismaService::getInvoice($id);
            $filename = ExportService::generateFilename('Invoice', $invoice->invoice_number ?? $id, 'pdf');
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function exportExcel($id)
    {
        try {
            return InvoiceExportService::generateSingleExcel($id);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate Excel: ' . $e->getMessage());
        }
    }

    public function exportAllPdf()
    {
        try {
            $invoices = PrismaService::getInvoices();
            $pdf = InvoiceExportService::generateListPdf($invoices);
            $filename = ExportService::generateFilename('Invoices', 'All', 'pdf');
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function exportAllExcel()
    {
        try {
            $invoices = PrismaService::getInvoices();
            return InvoiceExportService::generateListExcel($invoices);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate Excel: ' . $e->getMessage());
        }
    }
}
