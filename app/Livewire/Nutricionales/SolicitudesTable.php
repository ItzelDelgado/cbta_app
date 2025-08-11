<?php

namespace App\Livewire\Nutricionales;

use App\Models\Nutricionales\Solicitud as NutricionalesSolicitud;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class SolicitudesTable extends Component
{
    use WithPagination;

    public $buscar = ''; // input del usuario
    public $search = ''; // filtro aplicado realmente

    public $sortField = 'id';
    public $sortDirection = 'desc';

    protected $paginationTheme = 'tailwind';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function aplicarBusqueda()
    {
        $this->search = trim($this->buscar);
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $role = $user->roles[0]->name;

        $query = NutricionalesSolicitud::with([
            'user.hospital',
            'solicitud_detail',
            'solicitud_patient',
            'solicitud_aprobada'
        ]);

        if ($role === 'Cliente') {
            $query->where('user_id', $user->id);
        }

        if ($this->search !== '') {
            $query->where(function ($query) {
                $query->where('solicituds.id', 'like', "%{$this->search}%")
                    ->orWhere('solicituds.is_aprobada', 'like', "%{$this->search}%")
                    ->orWhereDate('solicituds.created_at', $this->search)
                    ->orWhereHas('user.hospital', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('solicitud_patient', function ($q) {
                        $q->where('nombre_paciente', 'like', "%{$this->search}%")
                          ->orWhere('apellidos_paciente', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('solicitud_aprobada', function ($q) {
                        $q->where('id', 'like', "%{$this->search}%")
                          ->orWhere('lote', 'like', "%{$this->search}%");
                    });
            });
        }

        // Ordenamientos especiales
        if ($this->sortField === 'solicitud_aprobadas.lote') {
            // Ordena por fecha del lote (ddmmaa) y luego por consecutivo (NNN).
            // Empuja los NULL al final.
            $dir = $this->sortDirection; // 'asc' | 'desc'

            $query = $query
                ->leftJoin('solicitud_aprobadas as sa', 'solicituds.id', '=', 'sa.solicitud_id')
                ->select('solicituds.*')
                // NULLs al final
                ->orderByRaw("CASE WHEN sa.lote IS NULL THEN 1 ELSE 0 END ASC")
                // Fecha del lote: SUBSTRING(lote,2,6) => ddmmaa
                ->orderByRaw("STR_TO_DATE(SUBSTRING(sa.lote, 2, 6), '%d%m%y') {$dir}")
                // Consecutivo: SUBSTRING(lote,8,3) => NNN
                ->orderByRaw("CAST(SUBSTRING(sa.lote, 8, 3) AS UNSIGNED) {$dir}");
        } elseif ($this->sortField === 'is_aprobada') {
            // Estado + más recientes primero
            $query->orderBy('is_aprobada', $this->sortDirection)
                  ->orderBy('created_at', 'desc');
        } else {
            // Default
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $solicitudes = $query->paginate(50);

        return view('livewire.nutricionales.solicitudes-table', compact('solicitudes'));
    }
}
