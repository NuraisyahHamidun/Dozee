<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Sale::with(['salesmen'])
            ->whereBetween('sale_date', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->orderBy('sale_date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Date',
            'Salesmen',
            'Amount (RM)',
            'Payment Method'
        ];
    }

    public function map($sale): array
    {
        return [
            'TXN-' . str_pad($sale->transaction_id, 6, '0', STR_PAD_LEFT),
            $sale->sale_date,
            $sale->salesmen->name ?? 'N/A',
            (float) $sale->total_amount,
            $sale->payment_method
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '"RM" #,##0.00',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // 1. Header Styling
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '1F497D'], // Navy blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);

        // 2. Data Rows Styling
        for ($row = 2; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
            
            // Alternating row colors
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'F2F5F9'], // Very light blue/gray
                    ],
                ]);
            }

            // Alignments
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // 3. Highlight Important Column (Total Amount - Column D)
        $sheet->getStyle('D2:D' . $highestRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => '1F497D'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'E2EFDA'], // Soft green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        // Apply borders
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'D9D9D9'],
                ],
            ],
        ]);
    }
}
