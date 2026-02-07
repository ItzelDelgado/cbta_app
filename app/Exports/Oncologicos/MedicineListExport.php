<?php

namespace App\Exports\Oncologicos;

use App\Models\Oncologicos\MedicineList;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MedicineListExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private int $medicineListId) {}

    private ?MedicineList $lista = null;

    public function collection(): Collection
    {
        $this->lista = MedicineList::with([
            'distributor',
            // ✅ Trae también el catálogo del genérico para evitar N+1
            'presentations' => function ($q) {
                $q->with('catalog:id,denominacion');
            },
        ])->findOrFail($this->medicineListId);

        // Exporta una fila por presentación configurada en la lista
        return $this->lista->presentations;
    }

    public function headings(): array
    {
        return [
            'Lista ID',
            'Lista nombre',
            'Tipo cobro default',
            'Marcas activas',
            'Hospitales asignados (por usuarios)',

            'Distribuidor nombre',
            'Distribuidor dirección',

            'Medicamento (genérico)',
            'Medicamento (comercial)',
            'Presentación',
            'Marca — Presentación',
            'Cobro (presentación/lista)',
            'Precio frasco',
            'Precio mg (override)',
        ];
    }

    public function map($presentation): array
    {
        $lista = $this->lista;

        // ===== Hospitales asignados a esta lista (por usuarios) =====
        $hospitales = User::query()
            ->where('medicine_list_id', $lista->id)
            ->with('hospital:id,name')
            ->get()
            ->pluck('hospital.name')
            ->filter()
            ->unique()
            ->values()
            ->implode(', ');

        // ===== Distribuidor =====
        $dist = $lista->distributor;
        $distNombre    = $dist->nombre ?? ($dist->name ?? '');
        $distDireccion = $dist->direccion ?? ($dist->address ?? '');

        // ===== Genérico (desde medicines_catalog) =====
        $medGenerico = $presentation->catalog->denominacion ?? '—';

        // ===== Comercial (marca, está en medicine_presentations.marca) =====
        $marca = trim((string) ($presentation->marca ?? ''));

        // ===== Presentación =====
        $presTxt = trim((string) ($presentation->presentacion ?? $presentation->name ?? ''));

        // ✅ Marca — Presentación (tal cual lo querías)
        $marcaPres = ($marca !== '' && $presTxt !== '')
            ? "{$marca} — {$presTxt}"
            : ($presTxt !== '' ? $presTxt : '—');

        // ===== Pivot configuración por lista =====
        $cobro = $presentation->pivot->charge_by ?? $lista->charge_by ?? '—';

        $precioFrasco = (float) ($presentation->pivot->precio ?? 0);
        $precioMg     = (float) ($presentation->pivot->precio_mg_override ?? 0);

        return [
            $lista->id,
            $lista->name,
            $lista->charge_by,
            $lista->active_brands ? 'Sí' : 'No',
            $hospitales ?: '—',

            $distNombre ?: '—',
            $distDireccion ?: '—',

            $medGenerico,
            $marca !== '' ? $marca : '—',
            $presTxt !== '' ? $presTxt : '—',
            $marcaPres,

            $cobro ?: '—',
            $precioFrasco,
            $precioMg,
        ];
    }
}
