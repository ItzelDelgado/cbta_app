<?php

namespace App\Livewire\Oncologicas;

use App\Models\Oncologicos\InspeccionMezcla as OncologicosInspeccionMezcla;
use Livewire\Attributes\On;
use Livewire\Component;

class InspeccionMezcla extends Component
{
    public $mostrarModalInspeccion = false;
    public $es_limpia = 0;
    public $mezclaId;
    public $es_libre = 0;
    public $tipo_contenedor = '';
    public $esta_rotulado = 0;
    public $numero_lote = 0;
    public $medicamento = 0;
    public $dosis_volumen_total = 0;
    public $volumen_medicamento = 0;
    public $rubrica_preparador = 0;
    public $sello_seguridad = 0;
    public $presenta_grietas = 0;
    public $presenta_fugas = 0;
    public $esta_roto = 0;

    public $coloracion_apropiada = 0;
    public $contenido_homogeneo = 0;
    public $presenta_particulas = 0;
    public $presenta_turbidez = 0;
    public $volumen_correcto = 0;
    public $aprueba_contenido = 0;
    public $aprueba_contenedor = 0;
    public $mezcla_aprobada = 0;

    public $dosis_volumen;
    public $peso_mezcla;
    public $observaciones = '';
    public $reviso_nombre = '';
    public $aprobo_nombre = '';

    #[On('abrir-modal-inspeccion')]
    public function abrirModalInspeccion($mezclaId)
    {
        logger()->info("Livewire 3: abrirModalInspeccion recibió ID: $mezclaId");

        $this->mezclaId = $mezclaId;
        $this->mostrarModalInspeccion = true;
    }


    public function guardarInspeccion()
    {
        $this->validate([
            'es_limpia' => 'required|boolean',
            'es_libre' => 'required|boolean',
            'tipo_contenedor' => 'nullable|string|in:Frasco,Bolsa,Jeringa,Otro',
            'esta_rotulado' => 'required|boolean',
            'numero_lote' => 'required|boolean',
            'medicamento' => 'required|boolean',
            'dosis_volumen_total' => 'required|boolean',
            'volumen_medicamento' => 'required|boolean',
            'rubrica_preparador' => 'required|boolean',
            'sello_seguridad' => 'required|boolean',
            'presenta_grietas' => 'required|boolean',
            'presenta_fugas' => 'required|boolean',
            'esta_roto' => 'required|boolean',
            'coloracion_apropiada' => 'required|boolean',
            'contenido_homogeneo' => 'required|boolean',
            'presenta_particulas' => 'required|boolean',
            'presenta_turbidez' => 'required|boolean',
            'volumen_correcto' => 'required|boolean',
            'aprueba_contenido' => 'required|boolean',
            'aprueba_contenedor' => 'required|boolean',
            'mezcla_aprobada' => 'required|boolean',
            'dosis_volumen' => 'required|numeric|min:0',
            'peso_mezcla' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
            'reviso_nombre' => 'required|string|max:255',
            'aprobo_nombre' => 'required|string|max:255',
        ]);

        OncologicosInspeccionMezcla::create([
            'mezcla_id' => $this->mezclaId,
            'es_limpia' => $this->es_limpia,
            'es_libre' => $this->es_libre,
            'fecha_inspeccion' => now()->toDateString(),
            'hora_inspeccion' => now()->toTimeString(),
            'tipo_contenedor' => $this->tipo_contenedor,
            'tipo_contenedor_otro' => $this->tipo_contenedor === 'Otro' ? 'Otro' : '',
            'esta_rotulado' => $this->esta_rotulado,
            'numero_lote' => $this->numero_lote,
            'medicamento' => $this->medicamento,
            'dosis_volumen_total' => $this->dosis_volumen_total,
            'volumen_medicamento' => $this->volumen_medicamento,
            'rubrica_preparador' => $this->rubrica_preparador,
            'sello_seguridad' => $this->sello_seguridad,
            'presenta_grietas' => $this->presenta_grietas,
            'presenta_fugas' => $this->presenta_fugas,
            'esta_roto' => $this->esta_roto,
            'coloracion_apropiada' => $this->coloracion_apropiada,
            'contenido_homogeneo' => $this->contenido_homogeneo,
            'presenta_particulas' => $this->presenta_particulas,
            'presenta_turbidez' => $this->presenta_turbidez,
            'volumen_correcto' => $this->volumen_correcto,
            'aprueba_contenido' => $this->aprueba_contenido,
            'aprueba_contenedor' => $this->aprueba_contenedor,
            'dosis_volumen' => $this->dosis_volumen,
            'peso_mezcla' => $this->peso_mezcla,
            'mezcla_aprobada' => $this->mezcla_aprobada,
            'observaciones' => $this->observaciones,
            'reviso_nombre' => $this->reviso_nombre,
            'aprobo_nombre' => $this->aprobo_nombre,
        ]);

        $this->mostrarModalInspeccion = false;

        session()->flash('success', 'Inspección registrada correctamente.');
    }

    public function render()
    {
        return view('livewire.oncologicas.inspeccion-mezcla');
    }


}
