<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\Diluent;
use App\Models\Oncologicos\DiluentPresentation;
use Illuminate\Http\Request;

class DiluentPresentationController extends Controller
{
    public function index(Diluent $diluent)
    {
        $presentations = $diluent->presentations()->orderBy('presentacion')->paginate(15);

        return view('admin.oncologicos.diluents.presentations.index', compact('diluent', 'presentations'));
    }

    public function create(Diluent $diluent)
    {
        return view('admin.oncologicos.diluents.presentations.create', compact('diluent'));
    }

    public function store(Request $request, Diluent $diluent)
    {
        $request->validate([
            'presentacion'           => 'required|string|max:255',
            'volume_ml'              => 'required|numeric|min:0.01',
            'denominacion_comercial' => 'nullable|string|max:255',
            'lote'                   => 'nullable|string|max:100',
            'caducidad'              => 'nullable|date',
            'is_active'              => 'sometimes|boolean',
        ]);

        $diluent->presentations()->create([
            'presentacion'           => $request->presentacion,
            'volume_ml'              => $request->volume_ml,
            'denominacion_comercial' => $request->denominacion_comercial,
            'lote'                   => $request->lote,
            'caducidad'              => $request->caducidad,
            'is_active'              => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.oncologicos.diluent_presentations.index', $diluent)
            ->with('success', 'Presentación creada correctamente.');
    }

    public function edit(Diluent $diluent, DiluentPresentation $presentation)
    {
        // Garantiza que pertenezca a este diluyente
        abort_unless($presentation->diluent_id === $diluent->id, 404);

        return view('admin.oncologicos.diluents.presentations.edit', compact('diluent', 'presentation'));
    }

    public function update(Request $request, Diluent $diluent, DiluentPresentation $presentation)
    {
        abort_unless($presentation->diluent_id === $diluent->id, 404);

        $request->validate([
            'presentacion'           => 'required|string|max:255',
            'volume_ml'              => 'required|numeric|min:0.01',
            'denominacion_comercial' => 'nullable|string|max:255',
            'lote'                   => 'nullable|string|max:100',
            'caducidad'              => 'nullable|date',
            'is_active'              => 'sometimes|boolean',
        ]);

        $presentation->update([
            'presentacion'           => $request->presentacion,
            'volume_ml'              => $request->volume_ml,
            'denominacion_comercial' => $request->denominacion_comercial,
            'lote'                   => $request->lote,
            'caducidad'              => $request->caducidad,
            'is_active'              => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.oncologicos.diluent_presentations.index', $diluent)
            ->with('success', 'Presentación actualizada correctamente.');
    }

    public function destroy(Diluent $diluent, DiluentPresentation $presentation)
    {
        abort_unless($presentation->diluent_id === $diluent->id, 404);

        $presentation->delete();

        return redirect()
            ->route('admin.oncologicos.diluent_presentations.index', $diluent)
            ->with('success', 'Presentación eliminada correctamente.');
    }
}
