<?php

namespace App\Livewire;

use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Solicituds extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $user = Auth::user(); // Obtener el usuario actual
        $role = $user->roles[0]->name;
        if ($role === 'Admin' or $role === 'Super Admin') {
            // Si el usuario es un administrador, cargar todas las solicitudes
            $solicitudes = Solicitud::with('user', 'solicitud_detail', 'solicitud_patient', 'input', 'user.hospital', 'solicitud_aprobada')
                ->latest()
                ->paginate(10);
        } elseif ($role === 'Cliente') {
            // Si el usuario es un cliente, cargar solo sus propias solicitudes
            $solicitudes = Solicitud::where('user_id', $user->id)
                ->with('user', 'solicitud_detail', 'solicitud_patient', 'input', 'user.hospital', 'solicitud_aprobada')
                ->latest()
                ->paginate(10);
            //return $solicitudes;
        }
 // Ajusta el número según la cantidad de registros que quieras mostrar por página
        return view('livewire.solicituds', [
            'solicitudes' => $solicitudes,
        ]);
    }

    public function readSolicitud($id)
    {
        $user = Auth::user();
        $role = $user->roles[0]->name;
        if ($role === 'Admin' or $role === 'Super Admin') {
            $solicitud = Solicitud::find($id);
            $solicitud->read_at = now();
            $solicitud->save();
        }
        return redirect()->route('admin.solicitudes.show', $id);
    }

    public function readSolicitudEdit($id)
    {
        $user = Auth::user();
        $role = $user->roles[0]->name;
        if ($role === 'Admin' or $role === 'Super Admin') {
            $solicitud = Solicitud::find($id);
            $solicitud->read_at = now();
            $solicitud->save();
        }
        return redirect()->route('admin.solicitudes.edit', $id);
    }
}
