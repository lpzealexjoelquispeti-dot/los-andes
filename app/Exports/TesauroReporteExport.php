<?php

namespace App\Exports;

use App\Models\BusquedaLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TesauroReporteExport implements WithMultipleSheets
{
    public function __construct(
        protected string $fechaDesde,
        protected string $fechaHasta
    ) {}

    public function sheets(): array
    {
        return [
            new TesauroTerminosSheet($this->fechaDesde, $this->fechaHasta),
            new TesauroSinResultadoSheet($this->fechaDesde, $this->fechaHasta),
        ];
    }
}

// ── HOJA 1: Términos más buscados ──
class TesauroTerminosSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(
        protected string $fechaDesde,
        protected string $fechaHasta
    ) {}

    public function title(): string { return 'Términos Buscados'; }

    public function headings(): array
    {
        return ['Término Buscado', 'Tipo de Resultado', 'Veces Buscado'];
    }

    public function collection()
    {
        return BusquedaLog::whereBetween('created_at', [$this->fechaDesde, $this->fechaHasta . ' 23:59:59'])
            ->selectRaw('termino_buscado, tipo_resultado, COUNT(*) as total')
            ->groupBy('termino_buscado', 'tipo_resultado')
            ->orderByDesc('total')
            ->get()
            ->map(fn($r) => [
                strtoupper($r->termino_buscado),
                ucfirst($r->tipo_resultado),
                $r->total,
            ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

// ── HOJA 2: Términos sin resultado ──
class TesauroSinResultadoSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function __construct(
        protected string $fechaDesde,
        protected string $fechaHasta
    ) {}

    public function title(): string { return 'Sin Resultado (Mejorar)'; }

    public function headings(): array
    {
        return ['Término Sin Resultado', 'Veces Buscado', 'Sugerencia'];
    }

    public function collection()
    {
        return BusquedaLog::whereBetween('created_at', [$this->fechaDesde, $this->fechaHasta . ' 23:59:59'])
            ->where('tipo_resultado', 'sin_resultado')
            ->selectRaw('termino_buscado, COUNT(*) as total')
            ->groupBy('termino_buscado')
            ->orderByDesc('total')
            ->get()
            ->map(fn($r) => [
                strtoupper($r->termino_buscado),
                $r->total,
                'Agregar al Tesauro',
            ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}