<?php

namespace App\Exports;

use App\Models\Solicitud;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class SolicitudesExport implements FromView
{
    public function view(): View
    {
        $user = Auth::user();
        $role = $user->roles[0]->name;

        if ($role === 'Admin' || $role === 'Super Admin') {
            $solicitudes = Solicitud::with('user.hospital', 'solicitud_patient', 'solicitud_detail', 'solicitud_aprobada',  'input')
                ->latest()
                ->get();
            // echo '<pre>' . json_encode($solicitudes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            // die();
        } else {
            $solicitudes = Solicitud::where('user_id', $user->id)
                ->with('user.hospital', 'solicitud_patient', 'solicitud_detail', 'solicitud_aprobada', 'input')
                ->latest()
                ->get();
        }

        return view('admin.solicitudes.export_excel', compact('solicitudes'));

    }
}
