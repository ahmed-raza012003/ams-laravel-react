<?php

namespace App\Services;

use App\Services\ExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExpenseExportService
{
    /**
     * Generate expenses list PDF
     */
    public static function generateListPdf($expenses)
    {
        $companyInfo = ExportService::getCompanyInfo();
        $currency = config('app.currency_symbol', '£');

        $pdf = Pdf::loadView('exports.expenses-list-pdf', [
            'expenses' => $expenses,
            'company' => $companyInfo,
            'currency' => $currency,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf;
    }

    /**
     * Generate expenses list Excel
     */
    public static function generateListExcel($expenses)
    {
        $data = new ExpenseListExcelExport($expenses, config('app.currency_symbol', '£'));
        
        return Excel::download($data, ExportService::generateFilename('Expenses', 'All', 'xlsx'));
    }

    /**
     * Format category name for display
     */
    public static function formatCategory($category)
    {
        return ucwords(str_replace('_', ' ', strtolower($category)));
    }
}

/**
 * Expense List Excel Export Class
 */
class ExpenseListExcelExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $expenses;
    protected $currency;

    public function __construct($expenses, $currency)
    {
        $this->expenses = $expenses;
        $this->currency = $currency;
    }

    public function collection()
    {
        return collect($this->expenses)->map(function ($expense) {
            return [
                ExportService::formatDate($expense->date ?? ''),
                ExpenseExportService::formatCategory($expense->category ?? ''),
                $expense->description ?? '',
                $expense->customer_name ?? '',
                ExportService::formatCurrency($expense->amount ?? 0, $this->currency),
                ExportService::formatCurrency($expense->tax_amount ?? 0, $this->currency),
                ExportService::formatCurrency(($expense->amount ?? 0) + ($expense->tax_amount ?? 0), $this->currency),
            ];
        });
    }

    public function headings(): array
    {
        return ['Date', 'Category', 'Description', 'Customer', 'Amount', 'Tax Amount', 'Total'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 20,
            'C' => 35,
            'D' => 25,
            'E' => 15,
            'F' => 15,
            'G' => 15,
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

