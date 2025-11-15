<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
// Removed: use Maatwebsite\Excel\Concerns\WithDrawings;
// Removed: use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class GenericExport implements FromCollection, WithHeadings, WithStartRow, WithEvents
{
    protected $data;
    protected $headings;
    protected static $empresa;

    public function __construct(array $data)
    {
        $this->headings = array_shift($data); // First row is headings
        $this->data = new Collection($data);
        self::$empresa = \App\Models\Empresa::first();
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function startRow(): int
    {
        return 7; // Start data on row 7 to leave space for company info
    }

    // Removed: public function drawings() { ... }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Set heading row style (now row 7)
                $sheet->getStyle('A7:' . $sheet->getHighestColumn() . '7')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFD3D3D3',
                        ],
                    ],
                ]);

                // Add company details
                if (self::$empresa) {
                    $details = self::$empresa->nombre . "\n" .
                               self::$empresa->calle . "\n" .
                               self::$empresa->distrito . ", " . self::$empresa->provincia . "\n" .
                               "Tel: " . self::$empresa->telefono;

                    $sheet->mergeCells('A1:D5'); // Merge cells for company details
                    $cell = $sheet->getCell('A1');
                    $cell->setValue($details);
                    $cell->getStyle()->getAlignment()->setWrapText(true);
                    $cell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $cell->getStyle()->getFont()->setBold(true);
                }

                // Auto-size all columns
                foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // Set row heights for the header section
                for ($i = 1; $i <= 5; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(15); // Default height
                }
            },
        ];
    }
}
