<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\ItemController as AdminItemController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\EstimateController as AdminEstimateController;
use App\Http\Controllers\Admin\ExpenseController as AdminExpenseController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ItemCategoryController as AdminItemCategoryController;
use App\Http\Controllers\Admin\ExpenseCategoryController as AdminExpenseCategoryController;
use App\Http\Controllers\Admin\SalesCategoryController as AdminSalesCategoryController;
use App\Http\Controllers\Admin\TaxTypeController as AdminTaxTypeController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerController as CustomerCustomerController;
use App\Http\Controllers\Customer\ItemController as CustomerItemController;
use App\Http\Controllers\Customer\InvoiceController as CustomerInvoiceController;
use App\Http\Controllers\Customer\EstimateController as CustomerEstimateController;
use App\Http\Controllers\Customer\ExpenseController as CustomerExpenseController;
use App\Http\Controllers\Customer\ItemCategoryController as CustomerItemCategoryController;
use App\Http\Controllers\Customer\ExpenseCategoryController as CustomerExpenseCategoryController;
use App\Http\Controllers\Customer\SalesCategoryController as CustomerSalesCategoryController;
use App\Http\Controllers\Customer\TaxTypeController as CustomerTaxTypeController;
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
    $userWithRole = DB::table('Users')
        ->join('Roles', 'Users.role_id', '=', 'Roles.id')
        ->select('Users.*', 'Roles.name as role_name')
        ->where('Users.id', $user->id)
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
    Route::get('/customers/export/pdf', [AdminCustomerController::class, 'exportAllPdf'])->name('customers.export.all.pdf');
    Route::get('/customers/export/excel', [AdminCustomerController::class, 'exportAllExcel'])->name('customers.export.all.excel');
    
    Route::get('/items', [AdminItemController::class, 'index'])->name('items.index');
    Route::post('/items', [AdminItemController::class, 'store'])->name('items.store');
    Route::get('/items/{id}', [AdminItemController::class, 'show'])->name('items.show');
    Route::put('/items/{id}', [AdminItemController::class, 'update'])->name('items.update');
    Route::put('/items/{id}/stock', [AdminItemController::class, 'updateStock'])->name('items.stock');
    Route::delete('/items/{id}', [AdminItemController::class, 'destroy'])->name('items.destroy');
    Route::get('/items/export/pdf', [AdminItemController::class, 'exportAllPdf'])->name('items.export.all.pdf');
    Route::get('/items/export/excel', [AdminItemController::class, 'exportAllExcel'])->name('items.export.all.excel');
    
    Route::get('/item-categories', [AdminItemCategoryController::class, 'index'])->name('item-categories.index');
    Route::post('/item-categories', [AdminItemCategoryController::class, 'store'])->name('item-categories.store');
    Route::get('/item-categories/{id}', [AdminItemCategoryController::class, 'show'])->name('item-categories.show');
    Route::put('/item-categories/{id}', [AdminItemCategoryController::class, 'update'])->name('item-categories.update');
    Route::delete('/item-categories/{id}', [AdminItemCategoryController::class, 'destroy'])->name('item-categories.destroy');
    
    Route::get('/expense-categories', [AdminExpenseCategoryController::class, 'index'])->name('expense-categories.index');
    Route::post('/expense-categories', [AdminExpenseCategoryController::class, 'store'])->name('expense-categories.store');
    Route::get('/expense-categories/{id}', [AdminExpenseCategoryController::class, 'show'])->name('expense-categories.show');
    Route::put('/expense-categories/{id}', [AdminExpenseCategoryController::class, 'update'])->name('expense-categories.update');
    Route::delete('/expense-categories/{id}', [AdminExpenseCategoryController::class, 'destroy'])->name('expense-categories.destroy');
    
    Route::get('/sales-categories', [AdminSalesCategoryController::class, 'index'])->name('sales-categories.index');
    Route::post('/sales-categories', [AdminSalesCategoryController::class, 'store'])->name('sales-categories.store');
    Route::get('/sales-categories/{id}', [AdminSalesCategoryController::class, 'show'])->name('sales-categories.show');
    Route::put('/sales-categories/{id}', [AdminSalesCategoryController::class, 'update'])->name('sales-categories.update');
    Route::delete('/sales-categories/{id}', [AdminSalesCategoryController::class, 'destroy'])->name('sales-categories.destroy');
    
    Route::get('/tax-types', [AdminTaxTypeController::class, 'index'])->name('tax-types.index');
    Route::post('/tax-types', [AdminTaxTypeController::class, 'store'])->name('tax-types.store');
    Route::get('/tax-types/{id}', [AdminTaxTypeController::class, 'show'])->name('tax-types.show');
    Route::put('/tax-types/{id}', [AdminTaxTypeController::class, 'update'])->name('tax-types.update');
    Route::delete('/tax-types/{id}', [AdminTaxTypeController::class, 'destroy'])->name('tax-types.destroy');
    
    Route::get('/invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices', [AdminInvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [AdminInvoiceController::class, 'show'])->name('invoices.show');
    Route::put('/invoices/{id}', [AdminInvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{id}', [AdminInvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::patch('/invoices/{id}/status', [AdminInvoiceController::class, 'updateStatus'])->name('invoices.status');
    Route::get('/invoices/{id}/export/pdf', [AdminInvoiceController::class, 'exportPdf'])->name('invoices.export.pdf');
    Route::get('/invoices/{id}/export/excel', [AdminInvoiceController::class, 'exportExcel'])->name('invoices.export.excel');
    Route::get('/invoices/export/pdf', [AdminInvoiceController::class, 'exportAllPdf'])->name('invoices.export.all.pdf');
    Route::get('/invoices/export/excel', [AdminInvoiceController::class, 'exportAllExcel'])->name('invoices.export.all.excel');
    
    Route::get('/estimates', [AdminEstimateController::class, 'index'])->name('estimates.index');
    Route::post('/estimates', [AdminEstimateController::class, 'store'])->name('estimates.store');
    Route::get('/estimates/{id}', [AdminEstimateController::class, 'show'])->name('estimates.show');
    Route::put('/estimates/{id}', [AdminEstimateController::class, 'update'])->name('estimates.update');
    Route::delete('/estimates/{id}', [AdminEstimateController::class, 'destroy'])->name('estimates.destroy');
    Route::get('/estimates/{id}/export/pdf', [AdminEstimateController::class, 'exportPdf'])->name('estimates.export.pdf');
    Route::get('/estimates/{id}/export/excel', [AdminEstimateController::class, 'exportExcel'])->name('estimates.export.excel');
    Route::get('/estimates/export/pdf', [AdminEstimateController::class, 'exportAllPdf'])->name('estimates.export.all.pdf');
    Route::get('/estimates/export/excel', [AdminEstimateController::class, 'exportAllExcel'])->name('estimates.export.all.excel');
    
    Route::get('/expenses', [AdminExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [AdminExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{id}', [AdminExpenseController::class, 'show'])->name('expenses.show');
    Route::put('/expenses/{id}', [AdminExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [AdminExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/expenses/export/pdf', [AdminExpenseController::class, 'exportAllPdf'])->name('expenses.export.all.pdf');
    Route::get('/expenses/export/excel', [AdminExpenseController::class, 'exportAllExcel'])->name('expenses.export.all.excel');
    
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
    Route::get('/customers/export/pdf', [CustomerCustomerController::class, 'exportAllPdf'])->name('customers.export.all.pdf');
    Route::get('/customers/export/excel', [CustomerCustomerController::class, 'exportAllExcel'])->name('customers.export.all.excel');
    
    Route::get('/items', [CustomerItemController::class, 'index'])->name('items.index');
    Route::post('/items', [CustomerItemController::class, 'store'])->name('items.store');
    Route::get('/items/{id}', [CustomerItemController::class, 'show'])->name('items.show');
    Route::put('/items/{id}', [CustomerItemController::class, 'update'])->name('items.update');
    Route::put('/items/{id}/stock', [CustomerItemController::class, 'updateStock'])->name('items.stock');
    Route::delete('/items/{id}', [CustomerItemController::class, 'destroy'])->name('items.destroy');
    Route::get('/items/export/pdf', [CustomerItemController::class, 'exportAllPdf'])->name('items.export.all.pdf');
    Route::get('/items/export/excel', [CustomerItemController::class, 'exportAllExcel'])->name('items.export.all.excel');
    
    Route::get('/item-categories', [CustomerItemCategoryController::class, 'index'])->name('item-categories.index');
    Route::post('/item-categories', [CustomerItemCategoryController::class, 'store'])->name('item-categories.store');
    Route::get('/item-categories/{id}', [CustomerItemCategoryController::class, 'show'])->name('item-categories.show');
    Route::put('/item-categories/{id}', [CustomerItemCategoryController::class, 'update'])->name('item-categories.update');
    Route::delete('/item-categories/{id}', [CustomerItemCategoryController::class, 'destroy'])->name('item-categories.destroy');
    
    Route::get('/expense-categories', [CustomerExpenseCategoryController::class, 'index'])->name('expense-categories.index');
    Route::post('/expense-categories', [CustomerExpenseCategoryController::class, 'store'])->name('expense-categories.store');
    Route::get('/expense-categories/{id}', [CustomerExpenseCategoryController::class, 'show'])->name('expense-categories.show');
    Route::put('/expense-categories/{id}', [CustomerExpenseCategoryController::class, 'update'])->name('expense-categories.update');
    Route::delete('/expense-categories/{id}', [CustomerExpenseCategoryController::class, 'destroy'])->name('expense-categories.destroy');
    
    Route::get('/sales-categories', [CustomerSalesCategoryController::class, 'index'])->name('sales-categories.index');
    Route::post('/sales-categories', [CustomerSalesCategoryController::class, 'store'])->name('sales-categories.store');
    Route::get('/sales-categories/{id}', [CustomerSalesCategoryController::class, 'show'])->name('sales-categories.show');
    Route::put('/sales-categories/{id}', [CustomerSalesCategoryController::class, 'update'])->name('sales-categories.update');
    Route::delete('/sales-categories/{id}', [CustomerSalesCategoryController::class, 'destroy'])->name('sales-categories.destroy');
    
    Route::get('/tax-types', [CustomerTaxTypeController::class, 'index'])->name('tax-types.index');
    Route::post('/tax-types', [CustomerTaxTypeController::class, 'store'])->name('tax-types.store');
    Route::get('/tax-types/{id}', [CustomerTaxTypeController::class, 'show'])->name('tax-types.show');
    Route::put('/tax-types/{id}', [CustomerTaxTypeController::class, 'update'])->name('tax-types.update');
    Route::delete('/tax-types/{id}', [CustomerTaxTypeController::class, 'destroy'])->name('tax-types.destroy');
    
    Route::get('/invoices', [CustomerInvoiceController::class, 'index'])->name('invoices.index');
    Route::post('/invoices', [CustomerInvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [CustomerInvoiceController::class, 'show'])->name('invoices.show');
    Route::put('/invoices/{id}', [CustomerInvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{id}', [CustomerInvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{id}/export/pdf', [CustomerInvoiceController::class, 'exportPdf'])->name('invoices.export.pdf');
    Route::get('/invoices/{id}/export/excel', [CustomerInvoiceController::class, 'exportExcel'])->name('invoices.export.excel');
    Route::get('/invoices/export/pdf', [CustomerInvoiceController::class, 'exportAllPdf'])->name('invoices.export.all.pdf');
    Route::get('/invoices/export/excel', [CustomerInvoiceController::class, 'exportAllExcel'])->name('invoices.export.all.excel');
    
    Route::get('/estimates', [CustomerEstimateController::class, 'index'])->name('estimates.index');
    Route::post('/estimates', [CustomerEstimateController::class, 'store'])->name('estimates.store');
    Route::get('/estimates/{id}', [CustomerEstimateController::class, 'show'])->name('estimates.show');
    Route::put('/estimates/{id}', [CustomerEstimateController::class, 'update'])->name('estimates.update');
    Route::delete('/estimates/{id}', [CustomerEstimateController::class, 'destroy'])->name('estimates.destroy');
    Route::get('/estimates/{id}/export/pdf', [CustomerEstimateController::class, 'exportPdf'])->name('estimates.export.pdf');
    Route::get('/estimates/{id}/export/excel', [CustomerEstimateController::class, 'exportExcel'])->name('estimates.export.excel');
    Route::get('/estimates/export/pdf', [CustomerEstimateController::class, 'exportAllPdf'])->name('estimates.export.all.pdf');
    Route::get('/estimates/export/excel', [CustomerEstimateController::class, 'exportAllExcel'])->name('estimates.export.all.excel');
    
    Route::get('/expenses', [CustomerExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [CustomerExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{id}', [CustomerExpenseController::class, 'show'])->name('expenses.show');
    Route::put('/expenses/{id}', [CustomerExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [CustomerExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/expenses/export/pdf', [CustomerExpenseController::class, 'exportAllPdf'])->name('expenses.export.all.pdf');
    Route::get('/expenses/export/excel', [CustomerExpenseController::class, 'exportAllExcel'])->name('expenses.export.all.excel');
});

require __DIR__.'/auth.php';
