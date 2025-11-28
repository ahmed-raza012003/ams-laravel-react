<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = PrismaService::getExpenses();
        $customers = PrismaService::getCustomers();

        $categories = [
            'OFFICE_SUPPLIES' => 'Office Supplies',
            'TRAVEL' => 'Travel',
            'UTILITIES' => 'Utilities',
            'RENT' => 'Rent',
            'MARKETING' => 'Marketing',
            'SOFTWARE' => 'Software',
            'EQUIPMENT' => 'Equipment',
            'PAYROLL' => 'Payroll',
            'PROFESSIONAL_SERVICES' => 'Professional Services',
            'INSURANCE' => 'Insurance',
            'TAXES' => 'Taxes',
            'OTHER' => 'Other',
        ];

        return Inertia::render('Admin/Expenses/Index', [
            'expenses' => $expenses,
            'customers' => $customers,
            'categories' => $categories,
            'currency' => config('app.currency_symbol', '£'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:OFFICE_SUPPLIES,TRAVEL,UTILITIES,RENT,MARKETING,SOFTWARE,EQUIPMENT,PAYROLL,PROFESSIONAL_SERVICES,INSURANCE,TAXES,OTHER',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'taxAmount' => 'nullable|numeric|min:0',
            'date' => 'required|date',
            'customerId' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $validated['userId'] = auth()->id();
        $validated['taxAmount'] = $validated['taxAmount'] ?? 0;

        PrismaService::createExpense($validated);

        return redirect()->back()->with('success', 'Expense created successfully.');
    }

    public function show($id)
    {
        $expense = PrismaService::getExpense($id);

        if (!$expense) {
            return redirect()->back()->with('error', 'Expense not found.');
        }

        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category' => 'required|in:OFFICE_SUPPLIES,TRAVEL,UTILITIES,RENT,MARKETING,SOFTWARE,EQUIPMENT,PAYROLL,PROFESSIONAL_SERVICES,INSURANCE,TAXES,OTHER',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'taxAmount' => 'nullable|numeric|min:0',
            'date' => 'required|date',
            'customerId' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $validated['taxAmount'] = $validated['taxAmount'] ?? 0;

        PrismaService::updateExpense($id, $validated);

        return redirect()->back()->with('success', 'Expense updated successfully.');
    }

    public function destroy($id)
    {
        PrismaService::deleteExpense($id);

        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }
}
