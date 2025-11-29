<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaxTypeController extends Controller
{
    public function index()
    {
        $taxTypes = PrismaService::getTaxTypes();

        return Inertia::render('Admin/TaxTypes/Index', [
            'taxTypes' => $taxTypes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        $validated['userId'] = $request->user()->id;
        PrismaService::createTaxType($validated);

        return redirect()->back()->with('success', 'Tax type created successfully.');
    }

    public function show($id)
    {
        $taxType = PrismaService::getTaxType($id);

        if (!$taxType) {
            return redirect()->back()->with('error', 'Tax type not found.');
        }

        return response()->json($taxType);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        PrismaService::updateTaxType($id, $validated);

        return redirect()->back()->with('success', 'Tax type updated successfully.');
    }

    public function destroy($id)
    {
        PrismaService::deleteTaxType($id);

        return redirect()->back()->with('success', 'Tax type deleted successfully.');
    }
}

