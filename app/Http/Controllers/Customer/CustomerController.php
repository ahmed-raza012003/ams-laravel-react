<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = PrismaService::getCustomers(auth()->id());

        return Inertia::render('Customer/Customers/Index', [
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

        if (!$customer || $customer->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Customer not found.');
        }

        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = PrismaService::getCustomer($id);
        if (!$customer || $customer->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Customer not found.');
        }

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
        $customer = PrismaService::getCustomer($id);
        if (!$customer || $customer->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Customer not found.');
        }

        PrismaService::deleteCustomer($id);

        return redirect()->back()->with('success', 'Customer deleted successfully.');
    }
}
