<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemCategoryController extends Controller
{
    public function index()
    {
        $categories = PrismaService::getItemCategories();

        return Inertia::render('Admin/ItemCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        PrismaService::createItemCategory($validated);

        return redirect()->back()->with('success', 'Item category created successfully.');
    }

    public function show($id)
    {
        $category = PrismaService::getItemCategory($id);

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

        PrismaService::updateItemCategory($id, $validated);

        return redirect()->back()->with('success', 'Item category updated successfully.');
    }

    public function destroy($id)
    {
        PrismaService::deleteItemCategory($id);

        return redirect()->back()->with('success', 'Item category deleted successfully.');
    }
}

