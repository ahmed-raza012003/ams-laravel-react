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

class EstimateExportService
{
    /**
     * Generate single estimate PDF
     */
    public static function generateSinglePdf($estimateId)
    {
        $estimate = PrismaService::getEstimateWithItems($estimateId);
        
        if (!$estimate) {
            throw new \Exception('Estimate not found');
        }

        $customer = PrismaService::getCustomer($estimate->customer_id);
        $companyInfo = ExportService::getCompanyInfo();
        $currency = config('app.currency_symbol', '£');

        $pdf = Pdf::loadView('exports.estimate-pdf', [
            'estimate' => $estimate,
            'customer' => $customer,
            'company' => $companyInfo,
            'currency' => $currency,
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf;
    }

    /**
     * Generate single estimate Excel
     */
    public static function generateSingleExcel($estimateId)
    {
        $estimate = PrismaService::getEstimateWithItems($estimateId);
        
        if (!$estimate) {
            throw new \Exception('Estimate not found');
        }

        $customer = PrismaService::getCustomer($estimate->customer_id);
        $companyInfo = ExportService::getCompanyInfo();
        $currency = config('app.currency_symbol', '£');

        $data = new EstimateExcelExport($estimate, $customer, $companyInfo, $currency);
        
        return Excel::download($data, ExportService::generateFilename('Estimate', $estimate->estimate_number, 'xlsx'));
    }

    /**
     * Generate estimates list PDF
     */
    public static function generateListPdf($estimates)
    {
        $companyInfo = ExportService::getCompanyInfo();
        $currency = config('app.currency_symbol', '£');

        $pdf = Pdf::loadView('exports.estimates-list-pdf', [
            'estimates' => $estimates,
            'company' => $companyInfo,
            'currency' => $currency,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf;
    }

    /**
     * Generate estimates list Excel
     */
    public static function generateListExcel($estimates)
    {
        $data = new EstimateListExcelExport($estimates, config('app.currency_symbol', '£'));
        
        return Excel::download($data, ExportService::generateFilename('Estimates', 'All', 'xlsx'));
    }
}

/**
 * Estimate Excel Export Class
 */
class EstimateExcelExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $estimate;
    protected $customer;
    protected $company;
    protected $currency;

    public function __construct($estimate, $customer, $company, $currency)
    {
        $this->estimate = $estimate;
        $this->customer = $customer;
        $this->company = $company;
        $this->currency = $currency;
    }

    public function collection()
    {
        $data = collect();
        
        $data->push([$this->company['name'] ?? '']);
        $data->push(['Estimate Number:', $this->estimate->estimate_number]);
        $data->push(['Customer:', $this->customer->name ?? '']);
        $data->push(['Issue Date:', ExportService::formatDate($this->estimate->issue_date)]);
        $data->push(['Expiry Date:', ExportService::formatDate($this->estimate->expiry_date)]);
        $data->push(['Status:', $this->estimate->status]);
        $data->push([]);
        $data->push(['Description', 'Quantity', 'Unit Price', 'Tax Rate (%)', 'Total']);
        
        foreach ($this->estimate->items ?? [] as $item) {
            $data->push([
                $item->description ?? '',
                $item->quantity ?? 0,
                ExportService::formatCurrency($item->unit_price ?? 0, $this->currency),
                $item->tax_rate ?? 0,
                ExportService::formatCurrency($item->total ?? 0, $this->currency),
            ]);
        }

        $data->push([]);
        $data->push(['Subtotal:', '', '', '', ExportService::formatCurrency($this->estimate->subtotal ?? 0, $this->currency)]);
        $data->push(['Tax Amount:', '', '', '', ExportService::formatCurrency($this->estimate->tax_amount ?? 0, $this->currency)]);
        $data->push(['Total:', '', '', '', ExportService::formatCurrency($this->estimate->total ?? 0, $this->currency)]);

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
 * Estimate List Excel Export Class
 */
class EstimateListExcelExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $estimates;
    protected $currency;

    public function __construct($estimates, $currency)
    {
        $this->estimates = $estimates;
        $this->currency = $currency;
    }

    public function collection()
    {
        return collect($this->estimates)->map(function ($estimate) {
            return [
                $estimate->estimate_number ?? '',
                $estimate->customer_name ?? '',
                ExportService::formatDate($estimate->issue_date ?? ''),
                ExportService::formatDate($estimate->expiry_date ?? ''),
                ExportService::formatCurrency($estimate->total ?? 0, $this->currency),
                $estimate->status ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return ['Estimate Number', 'Customer', 'Issue Date', 'Expiry Date', 'Total', 'Status'];
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

