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
