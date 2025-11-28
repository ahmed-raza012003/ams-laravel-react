<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function index()
    {
        $items = PrismaService::getItems();

        return Inertia::render('Admin/Items/Index', [
            'items' => $items,
            'currency' => config('app.currency_symbol', '£'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unitPrice' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'taxRate' => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['userId'] = auth()->id();

        PrismaService::createItem($validated);

        return redirect()->back()->with('success', 'Item created successfully.');
    }

    public function show($id)
    {
        $item = PrismaService::getItem($id);

        if (!$item) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unitPrice' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'taxRate' => 'nullable|numeric|min:0|max:100',
        ]);

        PrismaService::updateItem($id, $validated);

        return redirect()->back()->with('success', 'Item updated successfully.');
    }

    public function destroy($id)
    {
        PrismaService::deleteItem($id);

        return redirect()->back()->with('success', 'Item deleted successfully.');
    }
}
