<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PrismaService
{
    public static function getRoles()
    {
        return DB::table('Role')->get();
    }

    public static function getRole($id)
    {
        return DB::table('Role')->where('id', $id)->first();
    }

    public static function getRoleByName($name)
    {
        return DB::table('Role')->where('name', $name)->first();
    }

    public static function getUsers()
    {
        return DB::table('User')
            ->join('Role', 'User.roleId', '=', 'Role.id')
            ->select('User.*', 'Role.name as role_name')
            ->get();
    }

    public static function getUser($id)
    {
        return DB::table('User')
            ->join('Role', 'User.roleId', '=', 'Role.id')
            ->select('User.*', 'Role.name as role_name')
            ->where('User.id', $id)
            ->first();
    }

    public static function getUserByEmail($email)
    {
        return DB::table('User')
            ->join('Role', 'User.roleId', '=', 'Role.id')
            ->select('User.*', 'Role.name as role_name')
            ->where('User.email', $email)
            ->first();
    }

    public static function getCustomers($userId = null)
    {
        $query = DB::table('Customer');
        if ($userId) {
            $query->where('userId', $userId);
        }
        return $query->orderBy('createdAt', 'desc')->get();
    }

    public static function getCustomer($id)
    {
        return DB::table('Customer')->where('id', $id)->first();
    }

    public static function createCustomer($data)
    {
        $data['createdAt'] = now();
        $data['updatedAt'] = now();
        return DB::table('Customer')->insertGetId($data);
    }

    public static function updateCustomer($id, $data)
    {
        $data['updatedAt'] = now();
        return DB::table('Customer')->where('id', $id)->update($data);
    }

    public static function deleteCustomer($id)
    {
        return DB::table('Customer')->where('id', $id)->delete();
    }

    public static function getItems($userId = null)
    {
        $query = DB::table('Item');
        if ($userId) {
            $query->where('userId', $userId);
        }
        return $query->orderBy('createdAt', 'desc')->get();
    }

    public static function getItem($id)
    {
        return DB::table('Item')->where('id', $id)->first();
    }

    public static function createItem($data)
    {
        $data['createdAt'] = now();
        $data['updatedAt'] = now();
        return DB::table('Item')->insertGetId($data);
    }

    public static function updateItem($id, $data)
    {
        $data['updatedAt'] = now();
        return DB::table('Item')->where('id', $id)->update($data);
    }

    public static function deleteItem($id)
    {
        return DB::table('Item')->where('id', $id)->delete();
    }

    public static function getInvoices($userId = null)
    {
        $query = DB::table('Invoice')
            ->join('Customer', 'Invoice.customerId', '=', 'Customer.id')
            ->select('Invoice.*', 'Customer.name as customer_name', 'Customer.email as customer_email');
        if ($userId) {
            $query->where('Invoice.userId', $userId);
        }
        return $query->orderBy('Invoice.createdAt', 'desc')->get();
    }

    public static function getInvoice($id)
    {
        return DB::table('Invoice')
            ->join('Customer', 'Invoice.customerId', '=', 'Customer.id')
            ->select('Invoice.*', 'Customer.name as customer_name', 'Customer.email as customer_email')
            ->where('Invoice.id', $id)
            ->first();
    }

    public static function getInvoiceWithItems($id)
    {
        $invoice = self::getInvoice($id);
        if ($invoice) {
            $invoice->items = DB::table('InvoiceItem')
                ->leftJoin('Item', 'InvoiceItem.itemId', '=', 'Item.id')
                ->select('InvoiceItem.*', 'Item.name as item_name')
                ->where('InvoiceItem.invoiceId', $id)
                ->get();
        }
        return $invoice;
    }

    public static function createInvoice($data)
    {
        $data['createdAt'] = now();
        $data['updatedAt'] = now();
        return DB::table('Invoice')->insertGetId($data);
    }

    public static function updateInvoice($id, $data)
    {
        $data['updatedAt'] = now();
        return DB::table('Invoice')->where('id', $id)->update($data);
    }

    public static function deleteInvoice($id)
    {
        DB::table('InvoiceItem')->where('invoiceId', $id)->delete();
        return DB::table('Invoice')->where('id', $id)->delete();
    }

    public static function createInvoiceItem($data)
    {
        $data['createdAt'] = now();
        $data['updatedAt'] = now();
        return DB::table('InvoiceItem')->insertGetId($data);
    }

    public static function deleteInvoiceItems($invoiceId)
    {
        return DB::table('InvoiceItem')->where('invoiceId', $invoiceId)->delete();
    }

    public static function generateInvoiceNumber()
    {
        $lastInvoice = DB::table('Invoice')->orderBy('id', 'desc')->first();
        $number = $lastInvoice ? intval(substr($lastInvoice->invoiceNumber, 4)) + 1 : 1;
        return 'INV-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public static function getEstimates($userId = null)
    {
        $query = DB::table('Estimate')
            ->join('Customer', 'Estimate.customerId', '=', 'Customer.id')
            ->select('Estimate.*', 'Customer.name as customer_name', 'Customer.email as customer_email');
        if ($userId) {
            $query->where('Estimate.userId', $userId);
        }
        return $query->orderBy('Estimate.createdAt', 'desc')->get();
    }

    public static function getEstimate($id)
    {
        return DB::table('Estimate')
            ->join('Customer', 'Estimate.customerId', '=', 'Customer.id')
            ->select('Estimate.*', 'Customer.name as customer_name', 'Customer.email as customer_email')
            ->where('Estimate.id', $id)
            ->first();
    }

    public static function getEstimateWithItems($id)
    {
        $estimate = self::getEstimate($id);
        if ($estimate) {
            $estimate->items = DB::table('EstimateItem')
                ->leftJoin('Item', 'EstimateItem.itemId', '=', 'Item.id')
                ->select('EstimateItem.*', 'Item.name as item_name')
                ->where('EstimateItem.estimateId', $id)
                ->get();
        }
        return $estimate;
    }

    public static function createEstimate($data)
    {
        $data['createdAt'] = now();
        $data['updatedAt'] = now();
        return DB::table('Estimate')->insertGetId($data);
    }

    public static function updateEstimate($id, $data)
    {
        $data['updatedAt'] = now();
        return DB::table('Estimate')->where('id', $id)->update($data);
    }

    public static function deleteEstimate($id)
    {
        DB::table('EstimateItem')->where('estimateId', $id)->delete();
        return DB::table('Estimate')->where('id', $id)->delete();
    }

    public static function createEstimateItem($data)
    {
        $data['createdAt'] = now();
        $data['updatedAt'] = now();
        return DB::table('EstimateItem')->insertGetId($data);
    }

    public static function deleteEstimateItems($estimateId)
    {
        return DB::table('EstimateItem')->where('estimateId', $estimateId)->delete();
    }

    public static function generateEstimateNumber()
    {
        $lastEstimate = DB::table('Estimate')->orderBy('id', 'desc')->first();
        $number = $lastEstimate ? intval(substr($lastEstimate->estimateNumber, 4)) + 1 : 1;
        return 'EST-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public static function getExpenses($userId = null)
    {
        $query = DB::table('Expense')
            ->leftJoin('Customer', 'Expense.customerId', '=', 'Customer.id')
            ->select('Expense.*', 'Customer.name as customer_name');
        if ($userId) {
            $query->where('Expense.userId', $userId);
        }
        return $query->orderBy('Expense.date', 'desc')->get();
    }

    public static function getExpense($id)
    {
        return DB::table('Expense')
            ->leftJoin('Customer', 'Expense.customerId', '=', 'Customer.id')
            ->select('Expense.*', 'Customer.name as customer_name')
            ->where('Expense.id', $id)
            ->first();
    }

    public static function createExpense($data)
    {
        $data['createdAt'] = now();
        $data['updatedAt'] = now();
        return DB::table('Expense')->insertGetId($data);
    }

    public static function updateExpense($id, $data)
    {
        $data['updatedAt'] = now();
        return DB::table('Expense')->where('id', $id)->update($data);
    }

    public static function deleteExpense($id)
    {
        return DB::table('Expense')->where('id', $id)->delete();
    }

    public static function getDashboardStats($userId = null)
    {
        $invoiceQuery = DB::table('Invoice');
        $expenseQuery = DB::table('Expense');
        $customerQuery = DB::table('Customer');
        $estimateQuery = DB::table('Estimate');

        if ($userId) {
            $invoiceQuery->where('userId', $userId);
            $expenseQuery->where('userId', $userId);
            $customerQuery->where('userId', $userId);
            $estimateQuery->where('userId', $userId);
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

        $invoiceQuery = DB::table('Invoice')
            ->join('Customer', 'Invoice.customerId', '=', 'Customer.id')
            ->select('Invoice.id', 'Invoice.invoiceNumber as reference', 'Invoice.total as amount', 'Invoice.status', 'Invoice.createdAt', 'Customer.name as customer_name', DB::raw("'invoice' as type"));
        
        $expenseQuery = DB::table('Expense')
            ->select('Expense.id', 'Expense.description as reference', 'Expense.amount', 'Expense.category as status', 'Expense.createdAt', DB::raw("NULL as customer_name"), DB::raw("'expense' as type"));

        if ($userId) {
            $invoiceQuery->where('Invoice.userId', $userId);
            $expenseQuery->where('Expense.userId', $userId);
        }

        $activities = $invoiceQuery->union($expenseQuery)
            ->orderBy('createdAt', 'desc')
            ->limit($limit)
            ->get();

        return $activities;
    }
}
