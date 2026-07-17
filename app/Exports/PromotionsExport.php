<?php

namespace App\Exports;

use App\Models\Promotion;
use Illuminate\Support\Facades\DB;
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

class PromotionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $startDate;
    protected $endDate;
    protected $promoStatus;

    public function __construct($startDate, $endDate, $promoStatus)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->promoStatus = $promoStatus;
    }

    public function collection()
    {
        $promotionsQuery = Promotion::query();
        if ($this->promoStatus !== 'All') {
            $promotionsQuery->where('status', $this->promoStatus);
        }
        $promotionsQuery->where(function($q) {
            $q->whereBetween('start_date', [$this->startDate, $this->endDate])
              ->orWhereBetween('end_date', [$this->startDate, $this->endDate])
              ->orWhere(function($q2) {
                  $q2->where('start_date', '<=', $this->startDate)
                     ->where('end_date', '>=', $this->endDate);
              });
        });
        return $promotionsQuery->get();
    }

    public function headings(): array
    {
        return [
            'Promotion ID',
            'Name',
            'Description',
            'Status',
            'Start Date',
            'End Date',
            'Discount (%)',
            'Total Sales (Qty)',
            'Total Revenue (RM)'
        ];
    }

    public function map($promo): array
    {
        $rev = DB::table('transaction_detail')
            ->join('sales_transaction', 'transaction_detail.transaction_id', '=', 'sales_transaction.transaction_id')
            ->join('item', 'transaction_detail.item_id', '=', 'item.item_id')
            ->where('transaction_detail.promo_id', $promo->promo_id)
            ->sum(DB::raw('transaction_detail.quantity * item.price'));

        $salesCount = DB::table('transaction_detail')
            ->where('promo_id', $promo->promo_id)
            ->sum('quantity');

        return [
            'PRM-' . str_pad($promo->promo_id, 4, '0', STR_PAD_LEFT),
            $promo->promo_name,
            $promo->description,
            $promo->status,
            $promo->start_date,
            $promo->end_date,
            ($promo->final_discount ?? 10) . '%',
            (int) $salesCount,
            (float) $rev
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '"RM" #,##0.00',
            'H' => '#,##0',
            'I' => '"RM" #,##0.00',
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
                        'startColor' => ['argb' => 'F2F5F9'],
                    ],
                ]);
            }

            // Alignments
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // 3. Bold Promotion Name (Column B)
        $sheet->getStyle('B2:B' . $highestRow)->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        // 4. Highlight Important Column (Total Revenue - Column I)
        $sheet->getStyle('I2:I' . $highestRow)->applyFromArray([
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
