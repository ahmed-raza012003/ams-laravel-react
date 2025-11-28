<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\ItemController as AdminItemController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\EstimateController as AdminEstimateController;
use App\Http\Controllers\Admin\ExpenseController as AdminExpenseController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerController as CustomerCustomerController;
use App\Http\Controllers\Customer\ItemController as CustomerItemController;
use App\Http\Controllers\Customer\InvoiceController as CustomerInvoiceController;
use App\Http\Controllers\Customer\EstimateController as CustomerEstimateController;
use App\Http\Controllers\Customer\ExpenseController as CustomerExpenseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'companyName' => config('app.company_name', 'FinanceFlow'),
        'primaryColor' => config('app.primary_color', '#2ca48b'),
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $userWithRole = DB::table('User')
        ->join('Role', 'User.roleId', '=', 'Role.id')
        ->select('User.*', 'Role.name as role_name')
        ->where('User.id', $user->id)
        ->first();

    if ($userWithRole && strtolower($userWithRole->role_name) === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('customer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [AdminCustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{id}', [AdminCustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{id}', [AdminCustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [AdminCustomerController::class, 'destroy'])->name('customers.destroy');
    
    Route::get('/items', [AdminItemController::class, 'index'])->name('items.index');
    Route::post('/items', [AdminItemController::class, 'store'])->name('items.store');
    Route::get('/items/{id}', [AdminItemController::class, 'show'])->name('items.show');
    Route::put('/items/{id}', [AdminItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [AdminItemController::class, 'destroy'])->name('items.destroy');
    
    Route::get('/invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices', [AdminInvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [AdminInvoiceController::class, 'show'])->name('invoices.show');
    Route::put('/invoices/{id}', [AdminInvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{id}', [AdminInvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::patch('/invoices/{id}/status', [AdminInvoiceController::class, 'updateStatus'])->name('invoices.status');
    
    Route::get('/estimates', [AdminEstimateController::class, 'index'])->name('estimates.index');
    Route::post('/estimates', [AdminEstimateController::class, 'store'])->name('estimates.store');
    Route::get('/estimates/{id}', [AdminEstimateController::class, 'show'])->name('estimates.show');
    Route::put('/estimates/{id}', [AdminEstimateController::class, 'update'])->name('estimates.update');
    Route::delete('/estimates/{id}', [AdminEstimateController::class, 'destroy'])->name('estimates.destroy');
    
    Route::get('/expenses', [AdminExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [AdminExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{id}', [AdminExpenseController::class, 'show'])->name('expenses.show');
    Route::put('/expenses/{id}', [AdminExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [AdminExpenseController::class, 'destroy'])->name('expenses.destroy');
    
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

Route::middleware(['auth', 'verified', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/customers', [CustomerCustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerCustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{id}', [CustomerCustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{id}', [CustomerCustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [CustomerCustomerController::class, 'destroy'])->name('customers.destroy');
    
    Route::get('/items', [CustomerItemController::class, 'index'])->name('items.index');
    Route::post('/items', [CustomerItemController::class, 'store'])->name('items.store');
    Route::get('/items/{id}', [CustomerItemController::class, 'show'])->name('items.show');
    Route::put('/items/{id}', [CustomerItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [CustomerItemController::class, 'destroy'])->name('items.destroy');
    
    Route::get('/invoices', [CustomerInvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices', [CustomerInvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [CustomerInvoiceController::class, 'show'])->name('invoices.show');
    Route::put('/invoices/{id}', [CustomerInvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{id}', [CustomerInvoiceController::class, 'destroy'])->name('invoices.destroy');
    
    Route::get('/estimates', [CustomerEstimateController::class, 'index'])->name('estimates.index');
    Route::post('/estimates', [CustomerEstimateController::class, 'store'])->name('estimates.store');
    Route::get('/estimates/{id}', [CustomerEstimateController::class, 'show'])->name('estimates.show');
    Route::put('/estimates/{id}', [CustomerEstimateController::class, 'update'])->name('estimates.update');
    Route::delete('/estimates/{id}', [CustomerEstimateController::class, 'destroy'])->name('estimates.destroy');
    
    Route::get('/expenses', [CustomerExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [CustomerExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{id}', [CustomerExpenseController::class, 'show'])->name('expenses.show');
    Route::put('/expenses/{id}', [CustomerExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [CustomerExpenseController::class, 'destroy'])->name('expenses.destroy');
});

require __DIR__.'/auth.php';
