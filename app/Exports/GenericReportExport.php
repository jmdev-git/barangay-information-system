<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(
        private Collection $rows,
        private array      $headings,
        private array      $keys,
        private string     $title       = 'Report',
        private string     $filters     = 'No filters applied',
        private string     $generatedAt = '',
    ) {
        $this->generatedAt = $generatedAt ?: now()->format('F d, Y h:i A');
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    /**
     * Top rows:
     *   Row 1: Report title
     *   Row 2: Generated at
     *   Row 3: Filters applied (date range, gender, etc.)
     *   Row 4: Total records
     *   Row 5: blank spacer
     *   Row 6: column headings  ← WithHeadings adds this
     */
    public function headings(): array
    {
        return [
            // Meta rows before column headers
            ['BARANGAY MANAGEMENT INFORMATION SYSTEM'],
            [$this->title],
            ['Generated: ' . $this->generatedAt],
            ['Filters: ' . $this->filters],
            ['Total Records: ' . $this->rows->count()],
            [''],  // blank spacer
            // Actual column headings
            $this->headings,
        ];
    }

    public function map($row): array
    {
        return collect($this->keys)
            ->map(fn ($key) => data_get($row, $key, ''))
            ->toArray();
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            3 => ['font' => ['italic' => true, 'color' => ['rgb' => '64748B']]],
            4 => ['font' => ['bold' => true, 'color' => ['rgb' => '1E3A8A']]],
            5 => ['font' => ['bold' => true]],
            7 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A8A']], 'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]],
        ];
    }
}
