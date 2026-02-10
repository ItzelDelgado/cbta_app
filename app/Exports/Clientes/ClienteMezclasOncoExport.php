<?php

namespace App\Exports\Clientes;

use App\Models\Cliente;
use App\Models\Oncologicos\Mezcla;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ClienteMezclasOncoExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(private int $clienteId) {}

    public function array(): array
    {
        $rows = [];

        $cliente = Cliente::with('hospitals')->findOrFail($this->clienteId);

        $hospitalIds = $cliente->hospitals->pluck('id')->values()->all();

        if (empty($hospitalIds)) {
            return [];
        }

        // Traemos mezclas donde la solicitud pertenece a un usuario de esos hospitales
        $mezclas = Mezcla::with([
            'solicitud.hospital',   // ✅ snapshot
            'solicitud.user',       // para nombre del usuario
            'solicitud.user.medicineList',
            'medicamentos.medicamentoOnco.catalog',
            'medicamentos.diluyente',
            'medicamentos.presentacionesUsadas.batch.presentation',
        ])
            ->whereHas('solicitud', function ($q) use ($hospitalIds) {
                $q->whereIn('hospital_id', $hospitalIds);
            })
            ->get();

        $mezclas = $mezclas->sortBy(function ($m) {
            return optional(optional($m->solicitud)->hospital)->name ?? '';
        });

        foreach ($mezclas as $mezcla) {

            $solicitud = $mezcla->solicitud;
            $user      = optional($solicitud)->user;

            $hospital = optional($solicitud?->hospital)->name;
            $usuario  = trim(($user?->name ?? '') . ' ' . ($user?->lastname ?? ''));

            $lista = $user?->medicineList;
            $listaCharge = $lista->charge_by ?? 'frasco';

            $cfgPorPresentacion = collect();

            if ($lista) {
                $cfgPorPresentacion = DB::table('medicine_list_presentation')
                    ->where('medicine_list_id', $lista->id)
                    ->get()
                    ->keyBy('medicine_presentation_id');
            }

            foreach ($mezcla->medicamentos as $med) {

                // Denominación (catalog)
                $denominacion = optional(optional($med->medicamentoOnco)->catalog)->denominacion
                    ?? $med->nombre_medicamento
                    ?? '—';

                // Marca (primera presentación usada)
                $firstUsed = ($med->presentacionesUsadas ?? collect())->first();
                $presentation = optional(optional($firstUsed)->batch)->presentation
                    ?? optional($firstUsed)->presentation
                    ?? null;

                $marca = $presentation?->marca ?? '—';

                // ===== Lógica de cobro (igual que tu export) =====
                $unidadCobro = '';
                $cantidad    = 0;
                $precioUnit  = 0;
                $subtotal    = 0;

                $presentaciones = $med->presentacionesUsadas ?? collect();

                if ($presentaciones->isEmpty()) {
                    $unidadCobro = $listaCharge === 'mg' ? 'mg' : 'frasco';
                    $cantidad    = $unidadCobro === 'mg' ? (float) $med->dosis : 1;
                } else {
                    $first = $presentaciones->first();
                    $presentationId =
                        optional($first->batch)->medicine_presentation_id
                        ?? optional(optional($first->batch)->presentation)->id;

                    $cfg = $cfgPorPresentacion->get($presentationId);
                    $chargeBy = $cfg->charge_by ?? $listaCharge;

                    $unidadCobro = $chargeBy === 'mg' ? 'mg' : 'frasco';

                    if ($chargeBy === 'frasco') {
                        foreach ($presentaciones as $pu) {
                            $pid =
                                optional($pu->batch)->medicine_presentation_id
                                ?? optional(optional($pu->batch)->presentation)->id;

                            $cfgPres = $cfgPorPresentacion->get($pid);
                            $precio  = (float) ($cfgPres->precio ?? 0);
                            $unidades = (float) ($pu->unidades_usadas ?? 1);

                            $cantidad += $unidades;
                            $subtotal += $precio * $unidades;
                        }

                        $precioUnit = $cantidad > 0 ? $subtotal / $cantidad : 0;
                    } else {
                        $cantidad   = (float) $med->dosis;
                        $precioUnit = (float) ($cfg->precio_mg_override ?? 0);
                        $subtotal   = $cantidad * $precioUnit;
                    }
                }

                $rows[] = [
                    // Cliente / Hospital (clasificación)
                    trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')),
                    $hospital,

                    // Datos solicitud / usuario / lista
                    $usuario,
                    $lista->name ?? 'N/A',
                    $solicitud?->id ?? '—',
                    $solicitud?->created_at ?? '—',

                    // Medicamento
                    $denominacion,
                    $marca,

                    // Estados y mezcla
                    $solicitud?->estado ?? '—',
                    $mezcla->id,
                    $mezcla->estado,
                    $mezcla->volumen_dilucion,
                    $mezcla->tiempo_infusion,
                    $mezcla->set_infusion ? 'Sí' : 'No',

                    // Cobro
                    $med->dosis,
                    $unidadCobro,
                    $cantidad,
                    round($precioUnit, 4),
                    round($subtotal, 2),

                    // Extras
                    optional($med->diluyente)->denominacion_generica,
                    $mezcla->remision,
                ];
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Cliente',
            'Hospital',

            'Usuario',
            'Lista de precios',
            'ID Solicitud',
            'Fecha solicitud',

            'Denominación',
            'Marca',

            'Estado solicitud',
            'ID Mezcla',
            'Estado mezcla',
            'Volumen dilución',
            'Tiempo infusión',
            'Set infusión',

            'Dosis',
            'Unidad cobro',
            'Cantidad cobro',
            'Precio unitario',
            'Subtotal',

            'Diluyente',
            'Remisión',
        ];
    }
}
