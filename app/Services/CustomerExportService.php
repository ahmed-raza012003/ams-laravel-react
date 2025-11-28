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

class CustomerExportService
{
    /**
     * Generate customers list PDF
     */
    public static function generateListPdf($customers)
    {
        $companyInfo = ExportService::getCompanyInfo();

        $pdf = Pdf::loadView('exports.customers-list-pdf', [
            'customers' => $customers,
            'company' => $companyInfo,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf;
    }

    /**
     * Generate customers list Excel
     */
    public static function generateListExcel($customers)
    {
        $data = new CustomerListExcelExport($customers);
        
        return Excel::download($data, ExportService::generateFilename('Customers', 'All', 'xlsx'));
    }
}

/**
 * Customer List Excel Export Class
 */
class CustomerListExcelExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $customers;

    public function __construct($customers)
    {
        $this->customers = $customers;
    }

    public function collection()
    {
        return collect($this->customers)->map(function ($customer) {
            return [
                $customer->name ?? '',
                $customer->email ?? '',
                $customer->phone ?? '',
                $customer->address ?? '',
                $customer->city ?? '',
                $customer->postcode ?? '',
                $customer->country ?? '',
                ExportService::formatDate($customer->created_at ?? ''),
            ];
        });
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Address', 'City', 'Postcode', 'Country', 'Created At'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 30,
            'C' => 15,
            'D' => 30,
            'E' => 15,
            'F' => 12,
            'G' => 20,
            'H' => 12,
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

