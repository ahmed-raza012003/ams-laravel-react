<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use App\Services\CustomerExportService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = PrismaService::getCustomers();

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
            'currency' => config('app.currency_symbol', '£'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['userId'] = auth()->id();

        PrismaService::createCustomer($validated);

        return redirect()->back()->with('success', 'Customer created successfully.');
    }

    public function show($id)
    {
        $customer = PrismaService::getCustomer($id);

        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found.');
        }

        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        PrismaService::updateCustomer($id, $validated);

        return redirect()->back()->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        PrismaService::deleteCustomer($id);

        return redirect()->back()->with('success', 'Customer deleted successfully.');
    }

    public function exportAllPdf()
    {
        try {
            $customers = PrismaService::getCustomers();
            $pdf = CustomerExportService::generateListPdf($customers);
            $filename = ExportService::generateFilename('Customers', 'All', 'pdf');
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function exportAllExcel()
    {
        try {
            $customers = PrismaService::getCustomers();
            return CustomerExportService::generateListExcel($customers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate Excel: ' . $e->getMessage());
        }
    }
}
