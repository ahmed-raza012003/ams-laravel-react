<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\PrismaService;
use Carbon\Carbon;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customerRole = PrismaService::getRoleByName('customer');

        if (!$customerRole) {
            $customerRoleId = DB::table('Roles')->insertGetId([
                'name' => 'customer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $customerRoleId = $customerRole->id;
        }

        $existingCustomer = DB::table('Users')->where('email', 'customer@financeflow.com')->first();

        if (!$existingCustomer) {
            $userId = DB::table('Users')->insertGetId([
                'name' => 'Test Customer',
                'email' => 'customer@financeflow.com',
                'password' => Hash::make('password123'),
                'role_id' => $customerRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create Customer record linked to the user
            $customerId = PrismaService::createCustomer([
                'userId' => $userId,
                'name' => 'Test Customer',
                'email' => 'customer@financeflow.com',
                'phone' => '+44 20 1234 5678',
                'address' => '123 Business Street',
                'city' => 'London',
                'postcode' => 'SW1A 1AA',
                'country' => 'United Kingdom',
            ]);

            // Create sample items for the customer
            $item1Id = PrismaService::createItem([
                'userId' => $userId,
                'name' => 'Web Development Service',
                'description' => 'Custom web development and design',
                'unitPrice' => 150.00,
                'unit' => 'hour',
                'taxRate' => 20.00,
            ]);

            $item2Id = PrismaService::createItem([
                'userId' => $userId,
                'name' => 'Consulting Service',
                'description' => 'Business consulting and advisory',
                'unitPrice' => 200.00,
                'unit' => 'hour',
                'taxRate' => 20.00,
            ]);

            $item3Id = PrismaService::createItem([
                'userId' => $userId,
                'name' => 'Hosting Service',
                'description' => 'Monthly web hosting and maintenance',
                'unitPrice' => 50.00,
                'unit' => 'month',
                'taxRate' => 20.00,
            ]);

            // Create sample invoices
            $invoice1Id = PrismaService::createInvoice([
                'userId' => $userId,
                'customerId' => $customerId,
                'invoiceNumber' => PrismaService::generateInvoiceNumber(),
                'issueDate' => Carbon::now()->subDays(30),
                'dueDate' => Carbon::now()->subDays(15),
                'status' => 'PAID',
                'subtotal' => 1500.00,
                'taxAmount' => 300.00,
                'total' => 1800.00,
                'notes' => 'Payment received on time. Thank you!',
            ]);

            // Add items to invoice 1
            PrismaService::createInvoiceItem([
                'invoiceId' => $invoice1Id,
                'itemId' => $item1Id,
                'description' => 'Web Development Service - 10 hours',
                'quantity' => 10.00,
                'unitPrice' => 150.00,
                'taxRate' => 20.00,
                'total' => 1800.00,
            ]);

            $invoice2Id = PrismaService::createInvoice([
                'userId' => $userId,
                'customerId' => $customerId,
                'invoiceNumber' => PrismaService::generateInvoiceNumber(),
                'issueDate' => Carbon::now()->subDays(15),
                'dueDate' => Carbon::now()->addDays(15),
                'status' => 'SENT',
                'subtotal' => 1000.00,
                'taxAmount' => 200.00,
                'total' => 1200.00,
                'notes' => 'Please pay within 30 days.',
            ]);

            // Add items to invoice 2
            PrismaService::createInvoiceItem([
                'invoiceId' => $invoice2Id,
                'itemId' => $item2Id,
                'description' => 'Consulting Service - 5 hours',
                'quantity' => 5.00,
                'unitPrice' => 200.00,
                'taxRate' => 20.00,
                'total' => 1200.00,
            ]);

            $invoice3Id = PrismaService::createInvoice([
                'userId' => $userId,
                'customerId' => $customerId,
                'invoiceNumber' => PrismaService::generateInvoiceNumber(),
                'issueDate' => Carbon::now()->subDays(5),
                'dueDate' => Carbon::now()->addDays(25),
                'status' => 'DRAFT',
                'subtotal' => 250.00,
                'taxAmount' => 50.00,
                'total' => 300.00,
                'notes' => 'Draft invoice - pending review.',
            ]);

            // Add items to invoice 3
            PrismaService::createInvoiceItem([
                'invoiceId' => $invoice3Id,
                'itemId' => $item3Id,
                'description' => 'Hosting Service - 5 months',
                'quantity' => 5.00,
                'unitPrice' => 50.00,
                'taxRate' => 20.00,
                'total' => 300.00,
            ]);

            // Create sample estimates
            $estimate1Id = PrismaService::createEstimate([
                'userId' => $userId,
                'customerId' => $customerId,
                'estimateNumber' => PrismaService::generateEstimateNumber(),
                'issueDate' => Carbon::now()->subDays(10),
                'expiryDate' => Carbon::now()->addDays(20),
                'status' => 'SENT',
                'subtotal' => 3000.00,
                'taxAmount' => 600.00,
                'total' => 3600.00,
                'notes' => 'Project estimate for Q1 2024 development work.',
            ]);

            // Add items to estimate 1
            PrismaService::createEstimateItem([
                'estimateId' => $estimate1Id,
                'itemId' => $item1Id,
                'description' => 'Web Development Service - 20 hours',
                'quantity' => 20.00,
                'unitPrice' => 150.00,
                'taxRate' => 20.00,
                'total' => 3600.00,
            ]);

            $estimate2Id = PrismaService::createEstimate([
                'userId' => $userId,
                'customerId' => $customerId,
                'estimateNumber' => PrismaService::generateEstimateNumber(),
                'issueDate' => Carbon::now()->subDays(3),
                'expiryDate' => Carbon::now()->addDays(27),
                'status' => 'DRAFT',
                'subtotal' => 800.00,
                'taxAmount' => 160.00,
                'total' => 960.00,
                'notes' => 'Draft estimate for additional services.',
            ]);

            // Add items to estimate 2
            PrismaService::createEstimateItem([
                'estimateId' => $estimate2Id,
                'itemId' => $item2Id,
                'description' => 'Consulting Service - 4 hours',
                'quantity' => 4.00,
                'unitPrice' => 200.00,
                'taxRate' => 20.00,
                'total' => 960.00,
            ]);

            // Create sample expenses
            PrismaService::createExpense([
                'userId' => $userId,
                'customerId' => $customerId,
                'category' => 'SOFTWARE',
                'description' => 'Adobe Creative Cloud Subscription',
                'amount' => 49.99,
                'taxAmount' => 9.99,
                'date' => Carbon::now()->subDays(20),
                'notes' => 'Monthly subscription for design software',
            ]);

            PrismaService::createExpense([
                'userId' => $userId,
                'customerId' => $customerId,
                'category' => 'OFFICE_SUPPLIES',
                'description' => 'Office stationery and supplies',
                'amount' => 125.50,
                'taxAmount' => 25.10,
                'date' => Carbon::now()->subDays(15),
                'notes' => 'Purchased from office supply store',
            ]);

            PrismaService::createExpense([
                'userId' => $userId,
                'customerId' => $customerId,
                'category' => 'TRAVEL',
                'description' => 'Client meeting travel expenses',
                'amount' => 85.00,
                'taxAmount' => 17.00,
                'date' => Carbon::now()->subDays(10),
                'notes' => 'Train tickets and parking',
            ]);

            PrismaService::createExpense([
                'userId' => $userId,
                'customerId' => null,
                'category' => 'UTILITIES',
                'description' => 'Office internet and phone bill',
                'amount' => 89.99,
                'taxAmount' => 18.00,
                'date' => Carbon::now()->subDays(5),
                'notes' => 'Monthly utilities payment',
            ]);

            PrismaService::createExpense([
                'userId' => $userId,
                'customerId' => $customerId,
                'category' => 'MARKETING',
                'description' => 'Google Ads campaign',
                'amount' => 200.00,
                'taxAmount' => 40.00,
                'date' => Carbon::now()->subDays(2),
                'notes' => 'Monthly marketing budget',
            ]);
        }
    }
}

