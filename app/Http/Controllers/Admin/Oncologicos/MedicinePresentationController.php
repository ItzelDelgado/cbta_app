<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\MedicinePresentation;
use App\Models\Oncologicos\MedicinesCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicinePresentationController extends Controller
{
    public function index(MedicinesCatalog $catalog)
    {
        $presentations = $catalog->presentations()
            ->with(['batches' => fn($q) => $q->latest()])
            ->orderBy('presentacion')
            ->get();

        return view('admin.oncologicos.presentations.index', compact('catalog', 'presentations'));
    }



    public function create(MedicinesCatalog $catalog)
    {
        return view('admin.oncologicos.presentations.create', compact('catalog'));
    }

    public function store(Request $request, MedicinesCatalog $catalog)
    {
        $request->validate([
            'presentations'                                   => 'required|array|min:1',

            'presentations.*.presentacion'                    => 'required|string|max:255',
            'presentations.*.contenido_valor'                 => 'required|numeric|min:0',
            'presentations.*.contenido_unidad'                => 'required|string|in:mg,g,ml,UI,smg',
            'presentations.*.marca'                           => 'nullable|string|max:255',
            'presentations.*.precio_frasco'                   => 'nullable|numeric|min:0',

            // NUEVOS CAMPOS (ya los tenías)
            'presentations.*.cantidad_medicamento'            => 'nullable|numeric|min:0',
            'presentations.*.volumen_diluyente'               => 'nullable|numeric|min:0',

            // ✅ NUEVOS CAMPOS (leyenda + estabilidad)
            'presentations.*.legend'                          => 'nullable|string|max:2000',
            'presentations.*.temp_min_c'                      => 'nullable|integer|min:0|max:99',
            'presentations.*.temp_max_c'                      => 'nullable|integer|min:0|max:99',
            'presentations.*.stability_hours'                 => 'nullable|integer|min:0|max:2000',

            // Batch vigente
            'presentations.*.batch.lote'                      => 'required|string|max:255',
            'presentations.*.batch.caducidad'                 => 'required|date',
            'presentations.*.batch.is_current'                => 'required|in:0,1',

            // disponibilidad
            'presentations.*.is_available'                    => 'required|in:0,1',
        ]);

        // Validación extra: temp_max_c >= temp_min_c (cuando ambos vienen)
        foreach ($request->input('presentations', []) as $idx => $p) {
            $min = $p['temp_min_c'] ?? null;
            $max = $p['temp_max_c'] ?? null;

            if ($min !== null && $max !== null && (int)$max < (int)$min) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "presentations.$idx.temp_max_c" => "La temperatura máxima debe ser mayor o igual a la mínima."
                    ]);
            }
        }

        DB::beginTransaction();

        try {

            foreach ($request->presentations as $p) {

                // 1) Crear presentación
                $presentation = $catalog->presentations()->create([
                    'presentacion'          => $p['presentacion'],
                    'contenido_valor'       => $p['contenido_valor'],
                    'contenido_unidad'      => $p['contenido_unidad'],
                    'marca'                 => $p['marca'] ?? null,
                    'precio_frasco'         => $p['precio_frasco'] ?? null,

                    'cantidad_medicamento'  => $p['cantidad_medicamento'] ?? null,
                    'volumen_diluyente'     => $p['volumen_diluyente'] ?? null,

                    // ✅ nuevos campos ya existentes en medicine_presentations
                    'legend'                => $p['legend'] ?? null,
                    'temp_min_c'            => $p['temp_min_c'] ?? null,
                    'temp_max_c'            => $p['temp_max_c'] ?? null,
                    'stability_hours'       => $p['stability_hours'] ?? null,

                    'is_available'          => $p['is_available'],
                ]);

                // 2) Crear lote vigente
                $presentation->batches()->create([
                    'lote'          => $p['batch']['lote'],
                    'caducidad'     => $p['batch']['caducidad'],
                    'is_current'    => true, // o (bool) $p['batch']['is_current']
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.oncologicos.medicines.catalog.presentations.index', $catalog->id)
                ->with('success', 'Presentaciones registradas correctamente.');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al guardar presentaciones: ' . $e->getMessage()]);
        }
    }


    public function edit(MedicinesCatalog $catalog, MedicinePresentation $presentation)
    {
        // Cargamos lotes para obtener el vigente
        $presentation->load('batches');

        $currentBatch = $presentation->batches
            ->firstWhere('is_current', true)
            ?? $presentation->batches->sortByDesc('caducidad')->first();

        return view('admin.oncologicos.presentations.edit', [
            'catalog'      => $catalog,
            'presentation' => $presentation,
            'currentBatch' => $currentBatch,
        ]);
    }

    public function update(Request $request, MedicinesCatalog $catalog, MedicinePresentation $presentation)
    {
        $data = $request->validate([
            'presentacion'         => 'required|string|max:255',
            'contenido_valor'      => 'required|numeric|min:0',
            'contenido_unidad'     => 'required|string|in:mg,g,ml,UI,smg',
            'marca'                => 'nullable|string|max:255',
            'precio_frasco'        => 'nullable|numeric|min:0',

            'cantidad_medicamento' => 'nullable|numeric|min:0',
            'volumen_diluyente'    => 'nullable|numeric|min:0',

            // ✅ NUEVOS CAMPOS
            'legend'               => 'nullable|string|max:2000',
            'temp_min_c'           => 'nullable|integer|min:0|max:99',
            'temp_max_c'           => 'nullable|integer|min:0|max:99',
            'stability_hours'      => 'nullable|integer|min:0|max:2000',

            'is_available'         => 'required|in:0,1',

            'batch.lote'           => 'required|string|max:255',
            'batch.caducidad'      => 'required|date',
        ]);

        // Validación extra: temp_max_c >= temp_min_c (cuando ambos vienen)
        $min = $data['temp_min_c'] ?? null;
        $max = $data['temp_max_c'] ?? null;
        if ($min !== null && $max !== null && (int)$max < (int)$min) {
            return back()
                ->withInput()
                ->withErrors([
                    'temp_max_c' => 'La temperatura máxima debe ser mayor o igual a la mínima.',
                ]);
        }

        DB::beginTransaction();

        try {
            // 1) Actualizar la presentación
            $presentation->update([
                'presentacion'         => $data['presentacion'],
                'contenido_valor'      => $data['contenido_valor'],
                'contenido_unidad'     => $data['contenido_unidad'],
                'marca'                => $data['marca'] ?? null,
                'precio_frasco'        => $data['precio_frasco'] ?? null,
                'cantidad_medicamento' => $data['cantidad_medicamento'] ?? null,
                'volumen_diluyente'    => $data['volumen_diluyente'] ?? null,

                // ✅ NUEVOS CAMPOS
                'legend'               => $data['legend'] ?? null,
                'temp_min_c'           => $data['temp_min_c'] ?? null,
                'temp_max_c'           => $data['temp_max_c'] ?? null,
                'stability_hours'      => $data['stability_hours'] ?? null,

                'is_available'         => $data['is_available'],
            ]);

            // 2) Actualizar / crear lote vigente
            $batchData = $data['batch'];

            $currentBatch = $presentation->batches()
                ->where('is_current', true)
                ->first();

            if ($currentBatch) {
                $currentBatch->update([
                    'lote'       => $batchData['lote'],
                    'caducidad'  => $batchData['caducidad'],
                    'is_current' => true,
                ]);
            } else {
                $currentBatch = $presentation->batches()->create([
                    'lote'       => $batchData['lote'],
                    'caducidad'  => $batchData['caducidad'],
                    'is_current' => true,
                ]);
            }

            // (Opcional) aseguras que solo ese sea el vigente
            $presentation->batches()
                ->where('id', '!=', $currentBatch->id)
                ->update(['is_current' => false]);

            DB::commit();

            return redirect()
                ->route('admin.oncologicos.medicines.catalog.presentations.index', $catalog->id)
                ->with('success', 'Presentación actualizada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Error al actualizar la presentación: ' . $e->getMessage(),
                ]);
        }
    }


    public function destroy(MedicinesCatalog $catalog, MedicinePresentation $presentation)
    {
        $presentation->delete();
        return redirect()
            ->route('oncologicos.medicines.catalog.presentations.index', $catalog)
            ->with('success', 'Presentación eliminada correctamente');
    }
}
