<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use App\Services\ItemExportService;
use App\Services\ExportService;
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

    public function exportAllPdf()
    {
        try {
            $items = PrismaService::getItems();
            $pdf = ItemExportService::generateListPdf($items);
            $filename = ExportService::generateFilename('Items', 'All', 'pdf');
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function exportAllExcel()
    {
        try {
            $items = PrismaService::getItems();
            return ItemExportService::generateListExcel($items);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate Excel: ' . $e->getMessage());
        }
    }
}
