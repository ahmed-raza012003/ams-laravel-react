<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = PrismaService::getExpenseCategories();

        return Inertia::render('Customer/ExpenseCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        PrismaService::createExpenseCategory($validated);

        return redirect()->back()->with('success', 'Expense category created successfully.');
    }

    public function show($id)
    {
        $category = PrismaService::getExpenseCategory($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        PrismaService::updateExpenseCategory($id, $validated);

        return redirect()->back()->with('success', 'Expense category updated successfully.');
    }

    public function destroy($id)
    {
        PrismaService::deleteExpenseCategory($id);

        return redirect()->back()->with('success', 'Expense category deleted successfully.');
    }
}

