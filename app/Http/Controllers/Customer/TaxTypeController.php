<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaxTypeController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $taxTypes = PrismaService::getTaxTypes($userId);

        return Inertia::render('Customer/TaxTypes/Index', [
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
        $userId = auth()->id();
        $taxType = PrismaService::getTaxType($id, $userId);

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

        $userId = auth()->id();
        $updated = PrismaService::updateTaxType($id, $validated, $userId);

        if (!$updated) {
            return redirect()->back()->with('error', 'Tax type not found or you do not have permission.');
        }

        return redirect()->back()->with('success', 'Tax type updated successfully.');
    }

    public function destroy($id)
    {
        $userId = auth()->id();
        $deleted = PrismaService::deleteTaxType($id, $userId);

        if (!$deleted) {
            return redirect()->back()->with('error', 'Tax type not found or you do not have permission.');
        }

        return redirect()->back()->with('success', 'Tax type deleted successfully.');
    }
}

