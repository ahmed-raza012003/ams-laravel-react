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

class ItemExportService
{
    /**
     * Generate items list PDF
     */
    public static function generateListPdf($items)
    {
        $companyInfo = ExportService::getCompanyInfo();
        $currency = config('app.currency_symbol', '£');

        $pdf = Pdf::loadView('exports.items-list-pdf', [
            'items' => $items,
            'company' => $companyInfo,
            'currency' => $currency,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf;
    }

    /**
     * Generate items list Excel
     */
    public static function generateListExcel($items)
    {
        $data = new ItemListExcelExport($items, config('app.currency_symbol', '£'));
        
        return Excel::download($data, ExportService::generateFilename('Items', 'All', 'xlsx'));
    }
}

/**
 * Item List Excel Export Class
 */
class ItemListExcelExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $items;
    protected $currency;

    public function __construct($items, $currency)
    {
        $this->items = $items;
        $this->currency = $currency;
    }

    public function collection()
    {
        return collect($this->items)->map(function ($item) {
            return [
                $item->name ?? '',
                $item->description ?? '',
                ExportService::formatCurrency($item->unit_price ?? 0, $this->currency),
                $item->unit ?? '',
                ($item->tax_rate ?? 0) . '%',
                ExportService::formatDate($item->created_at ?? ''),
            ];
        });
    }

    public function headings(): array
    {
        return ['Name', 'Description', 'Unit Price', 'Unit', 'Tax Rate', 'Created At'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 40,
            'C' => 15,
            'D' => 10,
            'E' => 12,
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

