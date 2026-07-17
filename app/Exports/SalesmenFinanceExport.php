<?php

namespace App\Exports;

use App\Models\SaleItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SalesmenFinanceExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $salesmenId;
    protected $startDate;
    protected $endDate;
    protected $promoId;

    public function __construct($salesmenId, $startDate, $endDate, $promoId = null)
    {
        $this->salesmenId = $salesmenId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->promoId = $promoId;
    }

    public function collection()
    {
        $query = SaleItem::with(['sale.salesmen', 'product', 'promotion'])
            ->whereHas('sale', function($q) {
                $q->where('salesmen_id', $this->salesmenId)
                  ->whereBetween('sale_date', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
            });

        if ($this->promoId && $this->promoId !== 'All') {
            $query->where('promo_id', $this->promoId);
        }

        return $query->orderBy(
            \DB::raw('(select sale_date from sales_transaction where sales_transaction.transaction_id = transaction_detail.transaction_id)'),
            'desc'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Sale Date',
            'Transaction ID',
            'Salesmen Name',
            'Customer / Event',
            'Item Name',
            'Volume',
            'Quantity Sold',
            'Unit Price (RM)',
            'Promotion Applied',
            'Total Price per Item (RM)',
            'Total Sale Amount (RM)',
            'Approval Status',
        ];
    }

    public function map($saleItem): array
    {
        $unitPrice = $saleItem->product->price ?? 0;
        $quantity  = $saleItem->quantity;

        return [
            optional($saleItem->sale->sale_date)->format('Y-m-d H:i') ?? 'N/A',
            'TXN-' . str_pad($saleItem->sale->transaction_id ?? 0, 6, '0', STR_PAD_LEFT),
            $saleItem->sale->salesmen->name ?? 'N/A',
            $saleItem->sale->event_name ?? 'N/A',
            $saleItem->product->item_name ?? 'N/A',
            $saleItem->product->volume ?? 'N/A',
            (int) $quantity,
            (float) $unitPrice,
            $saleItem->promotion->promo_name ?? 'None',
            (float) ($unitPrice * $quantity),
            (float) ($saleItem->sale->total_amount ?? 0),
            $saleItem->sale->status ?? 'N/A',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0',
            'H' => '"RM" #,##0.00',
            'J' => '"RM" #,##0.00',
            'K' => '"RM" #,##0.00',
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
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // 3. Highlight Important Column (Total Sale Amount - Column K)
        $sheet->getStyle('K2:K' . $highestRow)->applyFromArray([
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

    public function title(): string
    {
        return 'Salesmen Finance Report';
    }
}

