<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use App\Services\ExpenseExportService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $expenses = PrismaService::getExpenses($userId);
        $customers = PrismaService::getCustomers($userId);
        $expenseCategories = PrismaService::getExpenseCategories();

        return Inertia::render('Customer/Expenses/Index', [
            'expenses' => $expenses,
            'customers' => $customers,
            'expenseCategories' => $expenseCategories,
            'currency' => config('app.currency_symbol', '£'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expenseCategoryId' => 'required|integer|exists:ExpenseCategory,id',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'taxAmount' => 'nullable|numeric|min:0',
            'date' => 'required|date',
            'customerId' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        if (!empty($validated['customerId'])) {
            $customer = PrismaService::getCustomer($validated['customerId']);
            if (!$customer || $customer->user_id != auth()->id()) {
                return redirect()->back()->with('error', 'Invalid customer.');
            }
        }

        $validated['userId'] = auth()->id();
        $validated['taxAmount'] = $validated['taxAmount'] ?? 0;

        PrismaService::createExpense($validated);

        return redirect()->back()->with('success', 'Expense created successfully.');
    }

    public function show($id)
    {
        $expense = PrismaService::getExpense($id);

        if (!$expense || $expense->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Expense not found.');
        }

        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $expense = PrismaService::getExpense($id);
        if (!$expense || $expense->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Expense not found.');
        }

        $validated = $request->validate([
            'expenseCategoryId' => 'required|integer|exists:ExpenseCategory,id',
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
        $expense = PrismaService::getExpense($id);
        if (!$expense || $expense->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Expense not found.');
        }

        PrismaService::deleteExpense($id);

        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }

    public function exportAllPdf()
    {
        try {
            $userId = auth()->id();
            $expenses = PrismaService::getExpenses($userId);
            $pdf = ExpenseExportService::generateListPdf($expenses);
            $filename = ExportService::generateFilename('Expenses', 'All', 'pdf');
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function exportAllExcel()
    {
        try {
            $userId = auth()->id();
            $expenses = PrismaService::getExpenses($userId);
            return ExpenseExportService::generateListExcel($expenses);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate Excel: ' . $e->getMessage());
        }
    }
}
