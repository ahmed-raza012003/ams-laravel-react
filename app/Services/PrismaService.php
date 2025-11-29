<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PrismaService
{
    /**
     * Convert camelCase keys to snake_case
     */
    private static function convertKeysToSnakeCase(array $data): array
    {
        $converted = [];
        foreach ($data as $key => $value) {
            $snakeKey = Str::snake($key);
            $converted[$snakeKey] = $value;
        }
        return $converted;
    }

    public static function getRoles()
    {
        return DB::table('Roles')->get();
    }

    public static function getRole($id)
    {
        return DB::table('Roles')->where('id', $id)->first();
    }

    public static function getRoleByName($name)
    {
        return DB::table('Roles')->where('name', $name)->first();
    }

    public static function getUsers()
    {
        return DB::table('Users')
            ->join('Roles', 'Users.role_id', '=', 'Roles.id')
            ->select('Users.*', 'Roles.name as role_name')
            ->get();
    }

    public static function getUser($id)
    {
        return DB::table('Users')
            ->join('Roles', 'Users.role_id', '=', 'Roles.id')
            ->select('Users.*', 'Roles.name as role_name')
            ->where('Users.id', $id)
            ->first();
    }

    public static function getUserByEmail($email)
    {
        return DB::table('Users')
            ->join('Roles', 'Users.role_id', '=', 'Roles.id')
            ->select('Users.*', 'Roles.name as role_name')
            ->where('Users.email', $email)
            ->first();
    }

    public static function getCustomers($userId = null)
    {
        $query = DB::table('Customers');
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->orderBy('created_at', 'desc')->get();
    }

    public static function getCustomer($id)
    {
        return DB::table('Customers')->where('id', $id)->first();
    }

    public static function createCustomer($data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('Customers')->insertGetId($data);
    }

    public static function updateCustomer($id, $data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['updated_at'] = now();
        return DB::table('Customers')->where('id', $id)->update($data);
    }

    public static function deleteCustomer($id)
    {
        return DB::table('Customers')->where('id', $id)->delete();
    }

    public static function getItems($userId = null)
    {
        $query = DB::table('Items')
            ->leftJoin('ItemCategory', 'Items.item_category_id', '=', 'ItemCategory.id')
            ->select('Items.*', 'ItemCategory.title as category_title');
        if ($userId) {
            $query->where('Items.user_id', $userId);
        }
        $items = $query->orderBy('Items.created_at', 'desc')->get();
        
        // Load tax types for each item
        foreach ($items as $item) {
            $item->tax_types = DB::table('ItemTaxType')
                ->join('TaxType', 'ItemTaxType.tax_type_id', '=', 'TaxType.id')
                ->where('ItemTaxType.item_id', $item->id)
                ->select('TaxType.*')
                ->get();
        }
        
        return $items;
    }

    public static function getItem($id)
    {
        $item = DB::table('Items')
            ->leftJoin('ItemCategory', 'Items.item_category_id', '=', 'ItemCategory.id')
            ->select('Items.*', 'ItemCategory.title as category_title', 'ItemCategory.id as category_id')
            ->where('Items.id', $id)
            ->first();
        
        if ($item) {
            $item->tax_types = DB::table('ItemTaxType')
                ->join('TaxType', 'ItemTaxType.tax_type_id', '=', 'TaxType.id')
                ->where('ItemTaxType.item_id', $id)
                ->select('TaxType.*')
                ->get();
        }
        
        return $item;
    }

    public static function createItem($data)
    {
        $taxTypes = $data['taxTypes'] ?? [];
        unset($data['taxTypes']);
        
        $data = self::convertKeysToSnakeCase($data);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $itemId = DB::table('Items')->insertGetId($data);
        
        // Attach tax types
        if (!empty($taxTypes)) {
            foreach ($taxTypes as $taxTypeId) {
                DB::table('ItemTaxType')->insert([
                    'item_id' => $itemId,
                    'tax_type_id' => $taxTypeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        return $itemId;
    }

    public static function updateItem($id, $data)
    {
        $taxTypes = $data['taxTypes'] ?? null;
        unset($data['taxTypes']);
        
        $data = self::convertKeysToSnakeCase($data);
        $data['updated_at'] = now();
        DB::table('Items')->where('id', $id)->update($data);
        
        // Update tax types if provided
        if ($taxTypes !== null) {
            DB::table('ItemTaxType')->where('item_id', $id)->delete();
            if (!empty($taxTypes)) {
                foreach ($taxTypes as $taxTypeId) {
                    DB::table('ItemTaxType')->insert([
                        'item_id' => $id,
                        'tax_type_id' => $taxTypeId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        
        return true;
    }

    public static function updateItemStock($id, $stockQuantity)
    {
        return DB::table('Items')
            ->where('id', $id)
            ->update([
                'stock_quantity' => $stockQuantity,
                'updated_at' => now(),
            ]);
    }

    public static function deleteItem($id)
    {
        DB::table('ItemTaxType')->where('item_id', $id)->delete();
        return DB::table('Items')->where('id', $id)->delete();
    }

    public static function getInvoices($userId = null)
    {
        $query = DB::table('Invoices')
            ->join('Customers', 'Invoices.customer_id', '=', 'Customers.id')
            ->leftJoin('SalesCategory', 'Invoices.sales_category_id', '=', 'SalesCategory.id')
            ->select('Invoices.*', 'Customers.name as customer_name', 'Customers.email as customer_email', 'SalesCategory.title as sales_category_title');
        if ($userId) {
            $query->where('Invoices.user_id', $userId);
        }
        return $query->orderBy('Invoices.created_at', 'desc')->get();
    }

    public static function getInvoice($id)
    {
        return DB::table('Invoices')
            ->join('Customers', 'Invoices.customer_id', '=', 'Customers.id')
            ->leftJoin('SalesCategory', 'Invoices.sales_category_id', '=', 'SalesCategory.id')
            ->select('Invoices.*', 'Customers.name as customer_name', 'Customers.email as customer_email', 'SalesCategory.title as sales_category_title', 'SalesCategory.id as sales_category_id')
            ->where('Invoices.id', $id)
            ->first();
    }

    public static function getInvoiceWithItems($id)
    {
        $invoice = self::getInvoice($id);
        if ($invoice) {
            $invoice->items = DB::table('InvoiceItems')
                ->leftJoin('Items', 'InvoiceItems.item_id', '=', 'Items.id')
                ->select('InvoiceItems.*', 'Items.name as item_name')
                ->where('InvoiceItems.invoice_id', $id)
                ->get();
        }
        return $invoice;
    }

    public static function createInvoice($data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('Invoices')->insertGetId($data);
    }

    public static function updateInvoice($id, $data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['updated_at'] = now();
        return DB::table('Invoices')->where('id', $id)->update($data);
    }

    public static function deleteInvoice($id)
    {
        DB::table('InvoiceItems')->where('invoice_id', $id)->delete();
        return DB::table('Invoices')->where('id', $id)->delete();
    }

    public static function createInvoiceItem($data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('InvoiceItems')->insertGetId($data);
    }

    public static function deleteInvoiceItems($invoiceId)
    {
        return DB::table('InvoiceItems')->where('invoice_id', $invoiceId)->delete();
    }

    public static function generateInvoiceNumber()
    {
        $lastInvoice = DB::table('Invoices')->orderBy('id', 'desc')->first();
        $number = $lastInvoice ? intval(substr($lastInvoice->invoice_number, 4)) + 1 : 1;
        return 'INV-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public static function getEstimates($userId = null)
    {
        $query = DB::table('Estimates')
            ->join('Customers', 'Estimates.customer_id', '=', 'Customers.id')
            ->leftJoin('SalesCategory', 'Estimates.sales_category_id', '=', 'SalesCategory.id')
            ->select('Estimates.*', 'Customers.name as customer_name', 'Customers.email as customer_email', 'SalesCategory.title as sales_category_title');
        if ($userId) {
            $query->where('Estimates.user_id', $userId);
        }
        return $query->orderBy('Estimates.created_at', 'desc')->get();
    }

    public static function getEstimate($id)
    {
        return DB::table('Estimates')
            ->join('Customers', 'Estimates.customer_id', '=', 'Customers.id')
            ->leftJoin('SalesCategory', 'Estimates.sales_category_id', '=', 'SalesCategory.id')
            ->select('Estimates.*', 'Customers.name as customer_name', 'Customers.email as customer_email', 'SalesCategory.title as sales_category_title', 'SalesCategory.id as sales_category_id')
            ->where('Estimates.id', $id)
            ->first();
    }

    public static function getEstimateWithItems($id)
    {
        $estimate = self::getEstimate($id);
        if ($estimate) {
            $estimate->items = DB::table('EstimateItems')
                ->leftJoin('Items', 'EstimateItems.item_id', '=', 'Items.id')
                ->select('EstimateItems.*', 'Items.name as item_name')
                ->where('EstimateItems.estimate_id', $id)
                ->get();
        }
        return $estimate;
    }

    public static function createEstimate($data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('Estimates')->insertGetId($data);
    }

    public static function updateEstimate($id, $data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['updated_at'] = now();
        return DB::table('Estimates')->where('id', $id)->update($data);
    }

    public static function deleteEstimate($id)
    {
        DB::table('EstimateItems')->where('estimate_id', $id)->delete();
        return DB::table('Estimates')->where('id', $id)->delete();
    }

    public static function createEstimateItem($data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('EstimateItems')->insertGetId($data);
    }

    public static function deleteEstimateItems($estimateId)
    {
        return DB::table('EstimateItems')->where('estimate_id', $estimateId)->delete();
    }

    public static function generateEstimateNumber()
    {
        $lastEstimate = DB::table('Estimates')->orderBy('id', 'desc')->first();
        $number = $lastEstimate ? intval(substr($lastEstimate->estimate_number, 4)) + 1 : 1;
        return 'EST-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public static function getExpenses($userId = null)
    {
        $query = DB::table('Expenses')
            ->leftJoin('Customers', 'Expenses.customer_id', '=', 'Customers.id')
            ->leftJoin('ExpenseCategory', 'Expenses.expense_category_id', '=', 'ExpenseCategory.id')
            ->select('Expenses.*', 'Customers.name as customer_name', 'ExpenseCategory.title as category_title');
        if ($userId) {
            $query->where('Expenses.user_id', $userId);
        }
        return $query->orderBy('Expenses.date', 'desc')->get();
    }

    public static function getExpense($id)
    {
        return DB::table('Expenses')
            ->leftJoin('Customers', 'Expenses.customer_id', '=', 'Customers.id')
            ->leftJoin('ExpenseCategory', 'Expenses.expense_category_id', '=', 'ExpenseCategory.id')
            ->select('Expenses.*', 'Customers.name as customer_name', 'ExpenseCategory.title as category_title', 'ExpenseCategory.id as category_id')
            ->where('Expenses.id', $id)
            ->first();
    }

    public static function createExpense($data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('Expenses')->insertGetId($data);
    }

    public static function updateExpense($id, $data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['updated_at'] = now();
        return DB::table('Expenses')->where('id', $id)->update($data);
    }

    public static function deleteExpense($id)
    {
        return DB::table('Expenses')->where('id', $id)->delete();
    }

    public static function getDashboardStats($userId = null)
    {
        $invoiceQuery = DB::table('Invoices');
        $expenseQuery = DB::table('Expenses');
        $customerQuery = DB::table('Customers');
        $estimateQuery = DB::table('Estimates');

        if ($userId) {
            $invoiceQuery->where('user_id', $userId);
            $expenseQuery->where('user_id', $userId);
            $customerQuery->where('user_id', $userId);
            $estimateQuery->where('user_id', $userId);
        }

        $totalInvoices = $invoiceQuery->count();
        $totalRevenue = (clone $invoiceQuery)->where('status', 'PAID')->sum('total');
        $totalExpenses = $expenseQuery->sum('amount');
        $totalCustomers = $customerQuery->count();
        $totalEstimates = $estimateQuery->count();
        $pendingInvoices = (clone $invoiceQuery)->whereIn('status', ['SENT', 'OVERDUE'])->count();

        return [
            'totalInvoices' => $totalInvoices,
            'totalRevenue' => floatval($totalRevenue),
            'totalExpenses' => floatval($totalExpenses),
            'totalProfit' => floatval($totalRevenue) - floatval($totalExpenses),
            'totalCustomers' => $totalCustomers,
            'totalEstimates' => $totalEstimates,
            'pendingInvoices' => $pendingInvoices,
        ];
    }

    public static function getRecentActivity($userId = null, $limit = 10)
    {
        $activities = collect();

        $invoiceQuery = DB::table('Invoices')
            ->join('Customers', 'Invoices.customer_id', '=', 'Customers.id')
            ->select('Invoices.id', 'Invoices.invoice_number as reference', 'Invoices.total as amount', 'Invoices.status', 'Invoices.created_at', 'Customers.name as customer_name', DB::raw("'invoice' as type"));
        
        $expenseQuery = DB::table('Expenses')
            ->leftJoin('ExpenseCategory', 'Expenses.expense_category_id', '=', 'ExpenseCategory.id')
            ->select('Expenses.id', 'Expenses.description as reference', 'Expenses.amount', 'ExpenseCategory.title as status', 'Expenses.created_at', DB::raw("NULL as customer_name"), DB::raw("'expense' as type"));

        if ($userId) {
            $invoiceQuery->where('Invoices.user_id', $userId);
            $expenseQuery->where('Expenses.user_id', $userId);
        }

        $activities = $invoiceQuery->union($expenseQuery)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $activities;
    }

    // ItemCategory methods
    public static function getItemCategories($userId = null)
    {
        return DB::table('ItemCategory')->orderBy('created_at', 'desc')->get();
    }

    public static function getItemCategory($id)
    {
        return DB::table('ItemCategory')->where('id', $id)->first();
    }

    public static function createItemCategory($data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('ItemCategory')->insertGetId($data);
    }

    public static function updateItemCategory($id, $data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['updated_at'] = now();
        return DB::table('ItemCategory')->where('id', $id)->update($data);
    }

    public static function deleteItemCategory($id)
    {
        return DB::table('ItemCategory')->where('id', $id)->delete();
    }

    // ExpenseCategory methods
    public static function getExpenseCategories($userId = null)
    {
        return DB::table('ExpenseCategory')->orderBy('created_at', 'desc')->get();
    }

    public static function getExpenseCategory($id)
    {
        return DB::table('ExpenseCategory')->where('id', $id)->first();
    }

    public static function createExpenseCategory($data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('ExpenseCategory')->insertGetId($data);
    }

    public static function updateExpenseCategory($id, $data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['updated_at'] = now();
        return DB::table('ExpenseCategory')->where('id', $id)->update($data);
    }

    public static function deleteExpenseCategory($id)
    {
        return DB::table('ExpenseCategory')->where('id', $id)->delete();
    }

    // SalesCategory methods
    public static function getSalesCategories($userId = null)
    {
        return DB::table('SalesCategory')->orderBy('created_at', 'desc')->get();
    }

    public static function getSalesCategory($id)
    {
        return DB::table('SalesCategory')->where('id', $id)->first();
    }

    public static function createSalesCategory($data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('SalesCategory')->insertGetId($data);
    }

    public static function updateSalesCategory($id, $data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['updated_at'] = now();
        return DB::table('SalesCategory')->where('id', $id)->update($data);
    }

    public static function deleteSalesCategory($id)
    {
        return DB::table('SalesCategory')->where('id', $id)->delete();
    }

    // TaxType methods
    public static function getTaxTypes($userId = null)
    {
        $query = DB::table('TaxType');
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->orderBy('created_at', 'desc')->get();
    }

    public static function getTaxType($id, $userId = null)
    {
        $query = DB::table('TaxType')->where('id', $id);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->first();
    }

    public static function createTaxType($data)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        return DB::table('TaxType')->insertGetId($data);
    }

    public static function updateTaxType($id, $data, $userId = null)
    {
        $data = self::convertKeysToSnakeCase($data);
        $data['updated_at'] = now();
        $query = DB::table('TaxType')->where('id', $id);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query->update($data);
    }

    public static function deleteTaxType($id, $userId = null)
    {
        $query = DB::table('TaxType')->where('id', $id);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        $taxType = $query->first();
        if ($taxType) {
            DB::table('ItemTaxType')->where('tax_type_id', $id)->delete();
            return DB::table('TaxType')->where('id', $id)->delete();
        }
        return false;
    }
}
