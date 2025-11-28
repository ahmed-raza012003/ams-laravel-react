<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EstimateController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $estimates = PrismaService::getEstimates($userId);
        $customers = PrismaService::getCustomers($userId);
        $items = PrismaService::getItems($userId);

        return Inertia::render('Customer/Estimates/Index', [
            'estimates' => $estimates,
            'customers' => $customers,
            'items' => $items,
            'currency' => config('app.currency_symbol', '£'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customerId' => 'required|integer',
            'expiryDate' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unitPrice' => 'required|numeric|min:0',
            'items.*.taxRate' => 'nullable|numeric|min:0|max:100',
            'items.*.itemId' => 'nullable|integer',
        ]);

        $customer = PrismaService::getCustomer($validated['customerId']);
        if (!$customer || $customer->userId != auth()->id()) {
            return redirect()->back()->with('error', 'Invalid customer.');
        }

        $subtotal = 0;
        $taxAmount = 0;

        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unitPrice'];
            $itemTax = $itemTotal * (($item['taxRate'] ?? 0) / 100);
            $subtotal += $itemTotal;
            $taxAmount += $itemTax;
        }

        $estimateId = PrismaService::createEstimate([
            'userId' => auth()->id(),
            'customerId' => $validated['customerId'],
            'estimateNumber' => PrismaService::generateEstimateNumber(),
            'issueDate' => now(),
            'expiryDate' => $validated['expiryDate'],
            'status' => 'DRAFT',
            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'total' => $subtotal + $taxAmount,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unitPrice'];
            $itemTax = $itemTotal * (($item['taxRate'] ?? 0) / 100);
            
            PrismaService::createEstimateItem([
                'estimateId' => $estimateId,
                'itemId' => $item['itemId'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unitPrice' => $item['unitPrice'],
                'taxRate' => $item['taxRate'] ?? 0,
                'total' => $itemTotal + $itemTax,
            ]);
        }

        return redirect()->back()->with('success', 'Estimate created successfully.');
    }

    public function show($id)
    {
        $estimate = PrismaService::getEstimateWithItems($id);

        if (!$estimate || $estimate->userId != auth()->id()) {
            return redirect()->back()->with('error', 'Estimate not found.');
        }

        return response()->json($estimate);
    }

    public function update(Request $request, $id)
    {
        $estimate = PrismaService::getEstimate($id);
        if (!$estimate || $estimate->userId != auth()->id()) {
            return redirect()->back()->with('error', 'Estimate not found.');
        }

        $validated = $request->validate([
            'customerId' => 'required|integer',
            'expiryDate' => 'required|date',
            'status' => 'required|in:DRAFT,SENT,ACCEPTED,REJECTED,EXPIRED',
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

        PrismaService::updateEstimate($id, [
            'customerId' => $validated['customerId'],
            'expiryDate' => $validated['expiryDate'],
            'status' => $validated['status'],
            'subtotal' => $subtotal,
            'taxAmount' => $taxAmount,
            'total' => $subtotal + $taxAmount,
            'notes' => $validated['notes'] ?? null,
        ]);

        PrismaService::deleteEstimateItems($id);

        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity'] * $item['unitPrice'];
            $itemTax = $itemTotal * (($item['taxRate'] ?? 0) / 100);
            
            PrismaService::createEstimateItem([
                'estimateId' => $id,
                'itemId' => $item['itemId'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unitPrice' => $item['unitPrice'],
                'taxRate' => $item['taxRate'] ?? 0,
                'total' => $itemTotal + $itemTax,
            ]);
        }

        return redirect()->back()->with('success', 'Estimate updated successfully.');
    }

    public function destroy($id)
    {
        $estimate = PrismaService::getEstimate($id);
        if (!$estimate || $estimate->userId != auth()->id()) {
            return redirect()->back()->with('error', 'Estimate not found.');
        }

        PrismaService::deleteEstimate($id);

        return redirect()->back()->with('success', 'Estimate deleted successfully.');
    }
}
