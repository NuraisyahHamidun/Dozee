<?php

namespace App\Exports;

use App\Models\AprioriAnalysis;
use App\Models\Product;
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

class AprioriExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    public function collection()
    {
        return AprioriAnalysis::orderBy('support', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Rule ID',
            'Antecedent',
            'Consequent',
            'Support',
            'Confidence',
            'Lift'
        ];
    }

    public function map($rule): array
    {
        $antecedentNames = '';
        if ($rule->isMultiAntecedent()) {
            $ids = $rule->antecedentIds();
            $names = Product::whereIn('item_id', $ids)->get()->map(function($p) {
                return $p->item_code ? $p->item_code . ' (' . $p->item_name . ')' : $p->item_name;
            })->toArray();
            $antecedentNames = implode(' + ', $names);
        } else {
            $p = Product::find($rule->antecedent);
            $antecedentNames = $p ? ($p->item_code ? $p->item_code . ' (' . $p->item_name . ')' : $p->item_name) : $rule->antecedent;
        }
        
        $p2 = Product::find($rule->consequent);
        $consequentName = $p2 ? ($p2->item_code ? $p2->item_code . ' (' . $p2->item_name . ')' : $p2->item_name) : $rule->consequent;

        return [
            (int) $rule->rule_id,
            $antecedentNames,
            $consequentName,
            (float) $rule->support,
            (float) $rule->confidence,
            (float) $rule->lift
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '0.0000',
            'E' => '0.0000',
            'F' => '0.0000',
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
        }

        // 3. Highlight Important Columns (Confidence - Col E & Lift - Col F)
        $sheet->getStyle('E2:F' . $highestRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => '1F497D'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF2CC'], // Soft gold
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
