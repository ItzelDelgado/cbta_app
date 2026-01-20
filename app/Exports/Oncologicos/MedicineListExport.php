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
        // Cargamos la lista y sus relaciones relevantes
        $this->lista = MedicineList::with([
            'distributor',
            'presentations', // incluye pivot (charge_by, precio, precio_mg_override)
        ])->findOrFail($this->medicineListId);

        // Vamos a exportar filas por cada presentación configurada.
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
            'Cobro (presentación/lista)',
            'Precio frasco',
            'Precio mg (override)',
        ];
    }

    public function map($presentation): array
    {
        // ===== Lista =====
        $lista = $this->lista;

        // ===== Hospitales asignados a esta lista =====
        // (No depende de MedicineList->users(), usa tabla users directamente)
        $hospitales = User::query()
            ->where('medicine_list_id', $lista->id)
            ->with('hospital:id,name')
            ->get()
            ->pluck('hospital.name')
            ->filter()
            ->unique()
            ->values()
            ->implode(', ');

        // ===== Distribuidor (si existe) =====
        $dist = $lista->distributor;
        $distNombre = $dist->nombre ?? ($dist->name ?? '');
        $distDireccion = $dist->direccion ?? ($dist->address ?? '');

        // ===== Medicamento + presentación =====
        // Ojo: aquí depende de cómo esté tu modelo MedicinePresentation.
        // En muchos casos trae relación al catálogo o al medicine_onco.
        // Intentamos resolverlo con fallbacks seguros:
        $medGenerico  = $presentation->medicineCatalog->denominacion ?? $presentation->catalog->denominacion ?? '';
        $medComercial = $presentation->medicineCatalog->denominacion_comercial ?? $presentation->catalog->denominacion_comercial ?? '';

        $presentacionTxt = $presentation->presentacion ?? $presentation->name ?? '';

        // Pivot de configuración por lista
        $cobro = $presentation->pivot->charge_by ?? $lista->charge_by ?? '';
        $precioFrasco = (float) ($presentation->pivot->precio ?? 0);
        $precioMg = (float) ($presentation->pivot->precio_mg_override ?? 0);

        return [
            $lista->id,
            $lista->name,
            $lista->charge_by,
            $lista->active_brands ? 'Sí' : 'No',
            $hospitales ?: '—',

            $distNombre ?: '—',
            $distDireccion ?: '—',

            $medGenerico ?: '—',
            $medComercial ?: '—',
            $presentacionTxt ?: '—',
            $cobro ?: '—',
            $precioFrasco ?: 0,
            $precioMg ?: 0,
        ];
    }
}
