<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalesCategoryController extends Controller
{
    public function index()
    {
        $categories = PrismaService::getSalesCategories();

        return Inertia::render('Customer/SalesCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        PrismaService::createSalesCategory($validated);

        return redirect()->back()->with('success', 'Sales category created successfully.');
    }

    public function show($id)
    {
        $category = PrismaService::getSalesCategory($id);

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

        PrismaService::updateSalesCategory($id, $validated);

        return redirect()->back()->with('success', 'Sales category updated successfully.');
    }

    public function destroy($id)
    {
        PrismaService::deleteSalesCategory($id);

        return redirect()->back()->with('success', 'Sales category deleted successfully.');
    }
}

