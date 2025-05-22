<?php

namespace App\Livewire\Nutricionales;

use App\Models\Nutricionales\Solicitud as NutricionalesSolicitud;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;

class SolicitudesTable extends Component
{
    use WithPagination;

    public $buscar = ''; // <-- input del usuario
    public $search = ''; // <-- filtro que se aplica realmente

    protected $paginationTheme = 'tailwind';

    public function aplicarBusqueda()
    {
        $this->search = $this->buscar;
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
                $query->where('id', 'like', "%{$this->search}%")
                    ->orWhere('is_aprobada', 'like', "%{$this->search}%")
                    ->orWhereDate('created_at', $this->search)
                    ->orWhereHas('user.hospital', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('solicitud_patient', function ($q) {
                        $q->where('nombre_paciente', 'like', "%{$this->search}%")
                            ->orWhere('apellidos_paciente', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('solicitud_aprobada', function ($q) {
                        $q->where('id', 'like', "%{$this->search}%");
                        // 🔴 El filtro por 'lote' fue eliminado aquí
                    });
            });
        }

        $solicitudes = $query->orderByDesc('id')->paginate(50);

        return view('livewire.nutricionales.solicitudes-table', compact('solicitudes'));
    }
}
