<?php

namespace App\Services;

use App\Services\PrismaService;
use App\Services\ExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InvoiceExportService
{
    /**
     * Generate single invoice PDF
     */
    public static function generateSinglePdf($invoiceId)
    {
        $invoice = PrismaService::getInvoiceWithItems($invoiceId);
        
        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }

        $customer = PrismaService::getCustomer($invoice->customer_id);
        $companyInfo = ExportService::getCompanyInfo();
        $currency = config('app.currency_symbol', '£');

        $pdf = Pdf::loadView('exports.invoice-pdf', [
            'invoice' => $invoice,
            'customer' => $customer,
            'company' => $companyInfo,
            'currency' => $currency,
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf;
    }

    /**
     * Generate single invoice Excel
     */
    public static function generateSingleExcel($invoiceId)
    {
        $invoice = PrismaService::getInvoiceWithItems($invoiceId);
        
        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }

        $customer = PrismaService::getCustomer($invoice->customer_id);
        $companyInfo = ExportService::getCompanyInfo();
        $currency = config('app.currency_symbol', '£');

        $data = new InvoiceExcelExport($invoice, $customer, $companyInfo, $currency);
        
        return Excel::download($data, ExportService::generateFilename('Invoice', $invoice->invoice_number, 'xlsx'));
    }

    /**
     * Generate invoices list PDF
     */
    public static function generateListPdf($invoices)
    {
        $companyInfo = ExportService::getCompanyInfo();
        $currency = config('app.currency_symbol', '£');

        $pdf = Pdf::loadView('exports.invoices-list-pdf', [
            'invoices' => $invoices,
            'company' => $companyInfo,
            'currency' => $currency,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf;
    }

    /**
     * Generate invoices list Excel
     */
    public static function generateListExcel($invoices)
    {
        $data = new InvoiceListExcelExport($invoices, config('app.currency_symbol', '£'));
        
        return Excel::download($data, ExportService::generateFilename('Invoices', 'All', 'xlsx'));
    }
}

/**
 * Invoice Excel Export Class
 */
class InvoiceExcelExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $invoice;
    protected $customer;
    protected $company;
    protected $currency;

    public function __construct($invoice, $customer, $company, $currency)
    {
        $this->invoice = $invoice;
        $this->customer = $customer;
        $this->company = $company;
        $this->currency = $currency;
    }

    public function collection()
    {
        $data = collect();
        
        // Company Info
        $data->push([$this->company['name'] ?? '']);
        $data->push(['Invoice Number:', $this->invoice->invoice_number]);
        $data->push(['Customer:', $this->customer->name ?? '']);
        $data->push(['Issue Date:', ExportService::formatDate($this->invoice->issue_date)]);
        $data->push(['Due Date:', ExportService::formatDate($this->invoice->due_date)]);
        $data->push(['Status:', $this->invoice->status]);
        $data->push([]);

        // Headers
        $data->push(['Description', 'Quantity', 'Unit Price', 'Tax Rate (%)', 'Total']);
        
        // Items
        foreach ($this->invoice->items ?? [] as $item) {
            $data->push([
                $item->description ?? '',
                $item->quantity ?? 0,
                ExportService::formatCurrency($item->unit_price ?? 0, $this->currency),
                $item->tax_rate ?? 0,
                ExportService::formatCurrency($item->total ?? 0, $this->currency),
            ]);
        }

        $data->push([]);
        $data->push(['Subtotal:', '', '', '', ExportService::formatCurrency($this->invoice->subtotal ?? 0, $this->currency)]);
        $data->push(['Tax Amount:', '', '', '', ExportService::formatCurrency($this->invoice->tax_amount ?? 0, $this->currency)]);
        $data->push(['Total:', '', '', '', ExportService::formatCurrency($this->invoice->total ?? 0, $this->currency)]);

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 12,
            'C' => 15,
            'D' => 15,
            'E' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->collection()) + 1;
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            7 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']]],
            $lastRow - 2 => ['font' => ['bold' => true]],
            $lastRow - 1 => ['font' => ['bold' => true]],
            $lastRow => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

/**
 * Invoice List Excel Export Class
 */
class InvoiceListExcelExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $invoices;
    protected $currency;

    public function __construct($invoices, $currency)
    {
        $this->invoices = $invoices;
        $this->currency = $currency;
    }

    public function collection()
    {
        return collect($this->invoices)->map(function ($invoice) {
            return [
                $invoice->invoice_number ?? '',
                $invoice->customer_name ?? '',
                ExportService::formatDate($invoice->issue_date ?? ''),
                ExportService::formatDate($invoice->due_date ?? ''),
                ExportService::formatCurrency($invoice->total ?? 0, $this->currency),
                $invoice->status ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['Invoice Number', 'Customer', 'Issue Date', 'Due Date', 'Total', 'Status'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 30,
            'C' => 12,
            'D' => 12,
            'E' => 15,
            'F' => 12,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}

