<?php

namespace App\Http\Controllers\Admin\Oncologicos;

use App\Http\Controllers\Controller;
use App\Models\Oncologicos\Laboratory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Pantalla previa: seleccionar laboratorio
     */
    public function selectLaboratory(Request $request)
    {
        $laboratories = Laboratory::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'estado']);

        return view('admin.oncologicos.inventory.select-laboratory', [
            'laboratories' => $laboratories,
        ]);
    }

    /**
     * Recibe laboratorio seleccionado y redirige al index
     */
    public function setLaboratory(Request $request)
    {
        $data = $request->validate([
            'laboratory_id' => 'required|integer|exists:laboratories,id',
        ]);

        return redirect()->route('admin.oncologicos.inventory.index', [
            'laboratory_id' => $data['laboratory_id'],
        ]);
    }

    /**
     * Inventario por laboratorio:
     * - catálogo
     * - presentaciones
     * - batch vigente del laboratorio
     *
     * Esta pantalla ya no expone:
     * - stock_reservado
     * - stock_disponible
     * - costo_unitario
     * - is_active
     * - is_current
     */
    public function index(Request $request)
    {
        $stock = (string) $request->get('stock', '');
        $q = trim((string) $request->get('q', ''));
        $laboratoryId = (int) $request->get('laboratory_id');

        if ($laboratoryId <= 0) {
            return redirect()->route('admin.oncologicos.inventory.selectLaboratory');
        }

        $lab = Laboratory::query()
            ->where('id', $laboratoryId)
            ->first();

        if (!$lab) {
            return redirect()
                ->route('admin.oncologicos.inventory.selectLaboratory')
                ->withErrors(['error' => 'Laboratorio inválido.']);
        }

        $rows = DB::table('medicines_catalog as mc')
            ->join('medicine_presentations as mp', 'mp.catalog_id', '=', 'mc.id')
            ->leftJoin('medicine_batches as mb', function ($join) use ($laboratoryId) {
                $join->on('mb.medicine_presentation_id', '=', 'mp.id')
                    ->where('mb.is_current', '=', 1)
                    ->where('mb.laboratory_id', '=', $laboratoryId);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('mc.denominacion', 'like', "%{$q}%")
                        ->orWhere('mp.presentacion', 'like', "%{$q}%")
                        ->orWhere('mp.marca', 'like', "%{$q}%")
                        ->orWhere('mb.lote', 'like', "%{$q}%");
                });
            })
            ->when($stock !== '', function ($query) use ($stock) {
                if ($stock === '1') {
                    $query->whereNotNull('mb.id')
                        ->where('mb.stock_actual', '>', 0);
                } elseif ($stock === '0') {
                    $query->where(function ($w) {
                        $w->whereNull('mb.id')
                            ->orWhere('mb.stock_actual', '<=', 0);
                    });
                }
            })
            ->select([
                'mc.id as catalog_id',
                'mc.denominacion',
                'mc.state',
                'mc.requires_infusor',
                'mc.conc_min',
                'mc.conc_max',

                'mp.id as presentation_id',
                'mp.presentacion',
                'mp.contenido_valor',
                'mp.contenido_unidad',
                'mp.marca',
                'mp.volumen_diluyente',
                'mp.precio_frasco',
                'mp.legend',
                'mp.temp_min_c',
                'mp.temp_max_c',
                'mp.stability_hours',
                'mp.is_available',

                'mb.id as batch_id',
                'mb.lote as lote_current',
                'mb.caducidad as caducidad_current',
                'mb.fecha_ingreso',
                'mb.stock_inicial',
                'mb.stock_actual',
            ])
            ->orderBy('mc.denominacion')
            ->orderBy('mp.presentacion')
            ->get();

        return view('admin.oncologicos.inventory.index', [
            'rows' => $rows,
            'q' => $q,
            'stock' => $stock,
            'laboratoryId' => $laboratoryId,
            'laboratory' => $lab,
        ]);
    }

    /**
     * Guardado masivo desde inventario
     *
     * Esta pantalla solo permite:
     * - crear lote
     * - editar lote
     * - editar fecha_ingreso
     * - ajustar stock_inicial
     * - ajustar stock_actual
     *
     * Ya NO recibe:
     * - stock_reservado
     * - costo_unitario
     * - is_current
     * - is_active
     */
    public function bulkUpdate(Request $request)
    {
        $laboratoryId = (int) $request->input('laboratory_id');

        if ($laboratoryId <= 0) {
            return back()->withErrors(['error' => 'Falta laboratory_id.']);
        }

        $labExists = DB::table('laboratories')
            ->where('id', $laboratoryId)
            ->exists();

        if (!$labExists) {
            return back()->withErrors(['error' => 'Laboratorio inválido.']);
        }

        $raw = $request->input('items');

        if (!$raw) {
            return back()->withErrors(['error' => 'No se recibieron cambios para guardar.']);
        }

        $items = json_decode($raw, true);

        if (!is_array($items)) {
            return back()->withErrors(['error' => 'Formato inválido: items no es un JSON válido.']);
        }

        if (count($items) === 0) {
            return back()->with('success', 'Sin cambios por guardar.');
        }

        foreach ($items as $i => $it) {
            $items[$i]['batch_id'] = isset($it['batch_id']) && $it['batch_id'] !== '' ? (int) $it['batch_id'] : null;
            $items[$i]['presentation_id'] = isset($it['presentation_id']) && $it['presentation_id'] !== '' ? (int) $it['presentation_id'] : null;
            $items[$i]['lote'] = isset($it['lote']) ? trim((string) $it['lote']) : '';
            $items[$i]['caducidad'] = isset($it['caducidad']) ? trim((string) $it['caducidad']) : '';
            $items[$i]['fecha_ingreso'] = isset($it['fecha_ingreso']) ? trim((string) $it['fecha_ingreso']) : '';
            $items[$i]['stock_inicial'] = $this->toIntOrNull($it['stock_inicial'] ?? null);
            $items[$i]['stock_actual'] = $this->toIntOrNull($it['stock_actual'] ?? null);

            if ($items[$i]['batch_id'] === null && !$items[$i]['presentation_id']) {
                return back()->withErrors([
                    'error' => "Falta presentation_id en el item #" . ($i + 1) . " (no hay batch_id)."
                ]);
            }

            if ($items[$i]['lote'] === '') {
                return back()->withErrors([
                    'error' => "Falta lote en el item #" . ($i + 1)
                ]);
            }

            if ($items[$i]['caducidad'] === '') {
                return back()->withErrors([
                    'error' => "Falta caducidad en el item #" . ($i + 1)
                ]);
            }

            try {
                Carbon::createFromFormat('Y-m-d', $items[$i]['caducidad']);
            } catch (\Throwable $e) {
                return back()->withErrors([
                    'error' => "Caducidad inválida (YYYY-MM-DD) en el item #" . ($i + 1)
                ]);
            }

            if ($items[$i]['fecha_ingreso'] !== '') {
                try {
                    Carbon::createFromFormat('Y-m-d', $items[$i]['fecha_ingreso']);
                } catch (\Throwable $e) {
                    return back()->withErrors([
                        'error' => "Fecha de ingreso inválida (YYYY-MM-DD) en el item #" . ($i + 1)
                    ]);
                }
            }

            if (($items[$i]['stock_inicial'] ?? 0) < 0) {
                return back()->withErrors([
                    'error' => "stock_inicial no puede ser negativo en el item #" . ($i + 1)
                ]);
            }

            if (($items[$i]['stock_actual'] ?? 0) < 0) {
                return back()->withErrors([
                    'error' => "stock_actual no puede ser negativo en el item #" . ($i + 1)
                ]);
            }
        }

        DB::beginTransaction();

        try {
            $updated = 0;
            $created = 0;
            $movements = 0;
            $userId = Auth::id();

            $batchIds = array_values(array_unique(array_filter(array_map(fn($x) => $x['batch_id'], $items))));

            if (!empty($batchIds)) {
                $batches = DB::table('medicine_batches')
                    ->whereIn('id', $batchIds)
                    ->select(
                        'id',
                        'laboratory_id',
                        'medicine_presentation_id',
                        'stock_actual',
                        'stock_inicial'
                    )
                    ->get()
                    ->keyBy('id');

                $faltantes = [];
                $foraneos = [];

                foreach ($batchIds as $id) {
                    if (!isset($batches[$id])) {
                        $faltantes[] = $id;
                        continue;
                    }

                    if ((int) $batches[$id]->laboratory_id !== $laboratoryId) {
                        $foraneos[] = $id;
                    }
                }

                if (!empty($faltantes)) {
                    throw new \Exception("No existen los siguientes batch_id: " . implode(', ', $faltantes));
                }

                if (!empty($foraneos)) {
                    throw new \Exception("Los siguientes batch_id no pertenecen al laboratorio seleccionado: " . implode(', ', $foraneos));
                }
            } else {
                $batches = collect();
            }

            foreach ($items as $it) {
                if ($it['batch_id'] === null) {
                    $presentationExists = DB::table('medicine_presentations')
                        ->where('id', $it['presentation_id'])
                        ->exists();

                    if (!$presentationExists) {
                        throw new \Exception("No existe la presentación con ID {$it['presentation_id']}.");
                    }

                    $duplicate = DB::table('medicine_batches')
                        ->where('laboratory_id', $laboratoryId)
                        ->where('medicine_presentation_id', $it['presentation_id'])
                        ->where('lote', $it['lote'])
                        ->exists();

                    if ($duplicate) {
                        throw new \Exception("Ya existe el lote '{$it['lote']}' para la presentación {$it['presentation_id']} en este laboratorio.");
                    }

                    $stockInicial = $it['stock_inicial'] ?? 0;
                    $stockActual = $it['stock_actual'] ?? $stockInicial;

                    $newBatchId = DB::table('medicine_batches')->insertGetId([
                        'laboratory_id' => $laboratoryId,
                        'medicine_presentation_id' => $it['presentation_id'],
                        'lote' => $it['lote'],
                        'caducidad' => $it['caducidad'],
                        'fecha_ingreso' => $it['fecha_ingreso'] !== '' ? $it['fecha_ingreso'] : null,
                        'stock_inicial' => $stockInicial,
                        'stock_actual' => $stockActual,
                        'stock_reservado' => 0,
                        'costo_unitario' => null,
                        'is_current' => 1,
                        'is_active' => $stockActual > 0 ? 1 : 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if ($stockActual > 0) {
                        $this->insertMovement([
                            'medicine_batch_id' => $newBatchId,
                            'laboratory_id' => $laboratoryId,
                            'user_id' => $userId,
                            'movement_type' => 'entrada',
                            'quantity' => $stockActual,
                            'stock_actual_before' => 0,
                            'stock_actual_after' => $stockActual,
                            'stock_reservado_before' => 0,
                            'stock_reservado_after' => 0,
                            'reference_type' => 'inventory_bulk_create',
                            'reference_id' => null,
                            'notes' => 'Alta inicial de lote desde inventario.',
                        ]);
                        $movements++;
                    }

                    $created++;
                    continue;
                }

                $existing = $batches[$it['batch_id']];

                $oldStockActual = (int) $existing->stock_actual;
                $newStockActual = $it['stock_actual'] ?? $oldStockActual;

                $updateData = [
                    'lote' => $it['lote'],
                    'caducidad' => $it['caducidad'],
                    'fecha_ingreso' => $it['fecha_ingreso'] !== '' ? $it['fecha_ingreso'] : null,
                    'stock_inicial' => $it['stock_inicial'] ?? (int) $existing->stock_inicial,
                    'stock_actual' => $newStockActual,
                    'is_active' => $newStockActual > 0 ? 1 : 0,
                    'updated_at' => now(),
                ];

                $affected = DB::table('medicine_batches')
                    ->where('id', $it['batch_id'])
                    ->update($updateData);

                if ($newStockActual !== $oldStockActual) {
                    $difference = $newStockActual - $oldStockActual;

                    $this->insertMovement([
                        'medicine_batch_id' => $it['batch_id'],
                        'laboratory_id' => $laboratoryId,
                        'user_id' => $userId,
                        'movement_type' => $difference > 0 ? 'ajuste_positivo' : 'ajuste_negativo',
                        'quantity' => abs($difference),
                        'stock_actual_before' => $oldStockActual,
                        'stock_actual_after' => $newStockActual,
                        'stock_reservado_before' => 0,
                        'stock_reservado_after' => 0,
                        'reference_type' => 'inventory_bulk_update',
                        'reference_id' => $it['batch_id'],
                        'notes' => 'Ajuste manual desde pantalla de inventario.',
                    ]);
                    $movements++;
                }

                $updated += (int) $affected;
            }

            DB::commit();

            return back()->with(
                'success',
                "Guardado ok. Actualizados: {$updated}. Nuevos lotes creados: {$created}. Movimientos generados: {$movements}."
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al guardar inventario: ' . $e->getMessage()]);
        }
    }

    /**
     * Crear lote para una presentación específica
     */
    public function storeBatch(Request $request, $presentation)
    {
        $presentationId = (int) $presentation;
        $laboratoryId = (int) $request->input('laboratory_id');

        if ($laboratoryId <= 0) {
            return back()->withErrors(['error' => 'Falta laboratory_id.']);
        }

        $request->validate([
            'lote' => 'required|string|max:100',
            'caducidad' => 'required|date|after_or_equal:today',
            'fecha_ingreso' => 'nullable|date',
            'stock_inicial' => 'nullable|integer|min:0',
            'stock_actual' => 'nullable|integer|min:0',
        ], [
            'caducidad.after_or_equal' => 'La caducidad no puede ser anterior a hoy.',
        ]);

        DB::beginTransaction();

        try {
            $labExists = DB::table('laboratories')->where('id', $laboratoryId)->exists();
            if (!$labExists) {
                throw new \Exception("Laboratorio inválido.");
            }

            $presExists = DB::table('medicine_presentations')->where('id', $presentationId)->exists();
            if (!$presExists) {
                throw new \Exception("No existe la presentación con ID {$presentationId}.");
            }

            $lote = trim((string) $request->lote);
            $cad = $request->caducidad;
            $fechaIngreso = $request->filled('fecha_ingreso') ? $request->fecha_ingreso : null;
            $stockInicial = (int) $request->input('stock_inicial', 0);
            $stockActual = $request->filled('stock_actual')
                ? (int) $request->input('stock_actual')
                : $stockInicial;

            $duplicate = DB::table('medicine_batches')
                ->where('laboratory_id', $laboratoryId)
                ->where('medicine_presentation_id', $presentationId)
                ->where('lote', $lote)
                ->exists();

            if ($duplicate) {
                throw new \Exception("Ya existe el lote '{$lote}' para esa presentación en este laboratorio.");
            }

            $batchId = DB::table('medicine_batches')->insertGetId([
                'laboratory_id' => $laboratoryId,
                'medicine_presentation_id' => $presentationId,
                'lote' => $lote,
                'caducidad' => $cad,
                'fecha_ingreso' => $fechaIngreso,
                'stock_inicial' => $stockInicial,
                'stock_actual' => $stockActual,
                'stock_reservado' => 0,
                'costo_unitario' => null,
                'is_current' => 1,
                'is_active' => $stockActual > 0 ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($stockActual > 0) {
                $this->insertMovement([
                    'medicine_batch_id' => $batchId,
                    'laboratory_id' => $laboratoryId,
                    'user_id' => Auth::id(),
                    'movement_type' => 'entrada',
                    'quantity' => $stockActual,
                    'stock_actual_before' => 0,
                    'stock_actual_after' => $stockActual,
                    'stock_reservado_before' => 0,
                    'stock_reservado_after' => 0,
                    'reference_type' => 'inventory_create_batch',
                    'reference_id' => $batchId,
                    'notes' => 'Alta inicial de lote.',
                ]);
            }

            DB::commit();
            return back()->with('success', 'Lote creado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear lote: ' . $e->getMessage()]);
        }
    }

    /**
     * Helper: insertar movimiento de inventario
     */
    private function insertMovement(array $data): void
    {
        DB::table('medicine_batch_movements')->insert([
            'medicine_batch_id' => $data['medicine_batch_id'],
            'laboratory_id' => $data['laboratory_id'],
            'user_id' => $data['user_id'] ?? null,
            'movement_type' => $data['movement_type'],
            'quantity' => $data['quantity'],
            'stock_actual_before' => $data['stock_actual_before'] ?? 0,
            'stock_actual_after' => $data['stock_actual_after'] ?? 0,
            'stock_reservado_before' => $data['stock_reservado_before'] ?? 0,
            'stock_reservado_after' => $data['stock_reservado_after'] ?? 0,
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id' => $data['reference_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Helper: entero nullable
     */
    private function toIntOrNull($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
