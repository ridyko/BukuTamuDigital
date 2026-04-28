<?php

namespace App\Exports;

use App\Models\Visitor;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitorsExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    use Exportable;

    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Visitor::with('host')->latest('check_in_at');

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('check_in_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('check_in_at', '<=', $this->filters['date_to']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['host_id'])) {
            $query->where('host_id', $this->filters['host_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama Tamu',
            'Instansi',
            'Keperluan',
            'Tujuan (Staf)',
            'Departemen',
            'Check-In',
            'Check-Out',
            'Metode Checkout',
            'Status',
            'Catatan'
        ];
    }

    public function map($visitor): array
    {
        return [
            $visitor->visit_code,
            $visitor->name,
            $visitor->institution ?? '-',
            $visitor->purpose,
            $visitor->host?->name ?? '-',
            $visitor->department ?? '-',
            $visitor->check_in_at?->format('d/m/Y H:i'),
            $visitor->check_out_at?->format('d/m/Y H:i') ?? '-',
            ucfirst($visitor->checkout_method ?? '-'),
            ucfirst(str_replace('_', ' ', $visitor->status)),
            $visitor->notes ?? '-'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 25,
            'D' => 30,
            'E' => 20,
            'F' => 15,
            'G' => 18,
            'H' => 18,
            'I' => 15,
            'J' => 15,
            'K' => 30,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
