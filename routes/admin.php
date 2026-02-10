<?php

use App\Http\Controllers\Admin\HospitalController;
use App\Http\Controllers\Admin\InputController;
use App\Http\Controllers\Admin\Nutricionales\MedicineController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\Nutricionales\SolicitudController;
use App\Http\Controllers\Admin\UserController;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Route; //Importamos para generar nuestras rutas.
use App\Exports\SolicitudesExport;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\Oncologicos\DiluentController;
use App\Http\Controllers\Admin\Oncologicos\DiluentPresentationController;
use App\Http\Controllers\Admin\Oncologicos\InfusorController;
use App\Http\Controllers\Admin\Oncologicos\MedicineCatalogController;
use App\Http\Controllers\Admin\Oncologicos\MedicineController as OncologicosMedicineController;
use App\Http\Controllers\Admin\Oncologicos\MedicinePresentationController;
use App\Http\Controllers\Admin\Oncologicos\MezclaController;
use App\Http\Controllers\Admin\Oncologicos\SolicitudController as OncologicosSolicitudController;
use Maatwebsite\Excel\Facades\Excel;

//Debemos avisarle a laravel que hemos creado un nuevo archivo de rutas en providers
Route::get('/dashboard', function () {
    //     session()->flash('swal', [
    //         'icon'=>"error",
    //         'title'=>"Oops...",
    //         'text'=>"Something went wrong :(!",
    //         'footer'=>'<a href="#">Why do I have this issue?</a>'
    // ]);
    return view('admin.dashboard');
})->name('dashboard');


Route::get('nutricionales/solicitudes/exportar', [SolicitudController::class, 'exportarExcel'])
    ->name('nutricionales.solicitudes.exportar')
    ->middleware(['can:nutricionales_solicitudes_index']);


Route::resource('/users', UserController::class)
    ->middleware(['can:usuarios']);

Route::resource('/roles', RoleController::class)
    ->except('show')
    ->middleware(['can:roles']);

Route::resource('/permissions', PermissionController::class)
    ->except('show')
    ->middleware(['can:permisos']);


Route::resource('/hospitals', HospitalController::class)
    ->except(['show', 'destroy'])
    ->middleware(['can:hospitales']);

Route::resource('nutricionales/medicines', MedicineController::class)
    ->except(['destroy'])
    ->middleware(['can:medicamentos_nutricionales'])
    ->names('nutricionales.medicines');

Route::get('hospitals/{hospital}/reporte-mezclas-onco', [HospitalController::class, 'exportarMezclasOnco'])
    ->name('hospitals.exportarMezclasOnco')
    ->middleware(['can:hospitales']);

// Route::resource('solicitudes', SolicitudController::class)->parameter('solicitudes', 'solicitud')->except(['destroy'])
//     ->middleware(['can:solicitudes']);

// Ruta para mostrar todas las solicitudes
Route::get('nutricionales/solicitudes', [SolicitudController::class, 'index'])->name('nutricionales.solicitudes.index')
    ->middleware(['can:nutricionales_solicitudes_index']);
// Ruta para mostrar el formulario de creación de solicitud
Route::get('nutricionales/solicitudes/create', [SolicitudController::class, 'create'])->name('nutricionales.solicitudes.create')
    ->middleware(['can:nutricionales_solicitudes_create']);

// Ruta para almacenar una nueva solicitud
Route::post('nutricionales/solicitudes', [SolicitudController::class, 'store'])->name('nutricionales.solicitudes.store')
    ->middleware(['can:nutricionales_solicitudes_store']);

// Ruta para mostrar una solicitud específica
Route::get('nutricionales/solicitudes/{solicitud}', [SolicitudController::class, 'show'])->name('nutricionales.solicitudes.show')
    ->middleware(['can:nutricionales_solicitudes_show']);

// Ruta para mostrar el formulario de edición de una solicitud
Route::get('nutricionales/solicitudes/{solicitud}/edit', [SolicitudController::class, 'edit'])->name('nutricionales.solicitudes.edit')
    ->middleware(['can:nutricionales_solicitudes_edit']);

// Ruta para actualizar una solicitud específica
Route::put('nutricionales/solicitudes/{solicitud}', [SolicitudController::class, 'update'])->name('nutricionales.solicitudes.update')
    ->middleware(['can:nutricionales_solicitudes_update']);

// // Ruta para eliminar una solicitud específica
// Route::delete('nutricionales/solicitudes/{solicitud}', [SolicitudController::class, 'destroy'])->name('nutricionales.solicitudes.destroy')
// ->middleware(['can:solicitudes_destroy']);


// También puedes excluir la ruta de eliminación
// Route::resource('solicitudes', SolicitudController::class)->parameter('solicitudes', 'solicitud')->except(['destroy']);

Route::get('nutricionales/solicitudes/solicitud/{solicitud}', [SolicitudController::class, 'solicitud'])->name('nutricionales.solicitudes.solicitud')
    ->middleware(['can:nutricionales_solicitudes_index']);



Route::get('nutricionales/solicitudes/orden-de-preparacion/{solicitud}', [SolicitudController::class, 'ordenPreparacion'])->name('nutricionales.solicitudes.ordenPreparacion')
    ->middleware(['can:nutricionales_solicitudes_index']);

Route::get('nutricionales/solicitudes/remision/{solicitud}', [SolicitudController::class, 'remision'])->name('nutricionales.solicitudes.remision')
    ->middleware(['can:nutricionales_solicitudes_index']);

Route::get('nutricionales/solicitudes/envio/{solicitud}', [SolicitudController::class, 'envio'])->name('nutricionales.solicitudes.envio')
    ->middleware(['can:nutricionales_solicitudes_index']);

Route::get('nutricionales/solicitudes/etiqueta/{solicitud}', [SolicitudController::class, 'etiqueta'])->name('nutricionales.solicitudes.etiqueta')
    ->middleware(['can:nutricionales_solicitudes_index']);

// RUTAS PARA ONCOLOGICOS

Route::get('oncologicos/solicitudes/exportar', [OncologicosSolicitudController::class, 'exportarExcel'])
    ->name('oncologicos.solicitudes.exportar')
    ->middleware(['can:oncologicos_solicitudes_index']);

Route::get('oncologicos/solicitudes', [OncologicosSolicitudController::class, 'index'])->name('oncologicos.solicitudes.index')
    ->middleware(['can:oncologicos_solicitudes_index']);

Route::get('oncologicos/solicitudes/create', [OncologicosSolicitudController::class, 'create'])->name('oncologicos.solicitudes.create')
    ->middleware(['can:oncologicos_solicitudes_create']);

Route::post('oncologicos/solicitudes', [OncologicosSolicitudController::class, 'store'])->name('oncologicos.solicitudes.store')
    ->middleware(['can:oncologicos_solicitudes_store']);

Route::get('oncologicos/solicitudes/{id}', [OncologicosSolicitudController::class, 'show'])->name('oncologicos.solicitudes.show')
    ->middleware(['can:oncologicos_solicitudes_show']);

Route::get('oncologicos/solicitudes/{id}/edit', [OncologicosSolicitudController::class, 'edit'])->name('oncologicos.solicitudes.edit')
    ->middleware(['can:oncologicos_solicitudes_edit']);

Route::put('oncologicos/solicitudes/{id}', [OncologicosSolicitudController::class, 'update'])->name('oncologicos.solicitudes.update')
    ->middleware(['can:oncologicos_solicitudes_update']);

//RUTAS PARA MEZCLAS ONCOLOGICAS

Route::get('oncologicos/solicitudes/mezclas/{mezcla}', [MezclaController::class, 'index'])->name('oncologicos.mezclas.index')
    ->middleware(['can:oncologicos_mezclas_index']);
Route::get('oncologicos/mezclas/create', [MezclaController::class, 'create'])->name('oncologicos.mezclas.create')
    ->middleware(['can:oncologicos_mezclas_create']);
Route::get('oncologicos/mezclas/{mezcla}', [MezclaController::class, 'show'])->name('oncologicos.mezclas.show')
    ->middleware(['can:oncologicos_mezclas_show']);
Route::post('oncologicos/mezclas', [MezclaController::class, 'store'])->name('oncologicos.mezclas.store')
    ->middleware(['can:oncologicos_mezclas_store']);
Route::get('oncologicos/mezclas/{mezcla}/edit', [MezclaController::class, 'edit'])->name('oncologicos.mezclas.edit')
    ->middleware(['can:oncologicos_mezclas_edit']);
Route::put('oncologicos/mezclas/{mezcla}', [MezclaController::class, 'update'])->name('oncologicos.mezclas.update')
    ->middleware(['can:oncologicos_mezclas_update']);

// PDF para solicitud de mezcla oncologicas

Route::get('oncologicos/mezclas/orden-de-preparacion/{mezcla}', [MezclaController::class, 'ordenPreparacion'])->name('oncologicos.mezclas.ordenPreparacion')
    ->middleware(['can:oncologicos_mezclas_index']);

Route::get('oncologicos/mezclas/inspeccion/{mezcla}', [MezclaController::class, 'inspeccion'])->name('oncologicos.mezclas.inspeccion')
    ->middleware(['can:oncologicos_mezclas_index']);

Route::get('oncologicos/mezclas/etiqueta/{mezcla}', [MezclaController::class, 'etiqueta'])->name('oncologicos.mezclas.etiqueta')
    ->middleware(['can:oncologicos_mezclas_index']);

Route::get('oncologicos/mezclas/solicitud-completa/{solicitud}', [OncologicosSolicitudController::class, 'solicitud'])->name('oncologicos.mezclas.solicitudCompleta')
    ->middleware(['can:oncologicos_mezclas_index']);

Route::get('oncologicos/mezclas/envio/{solicitud}', [OncologicosSolicitudController::class, 'envio'])->name('oncologicos.mezclas.envio')
    ->middleware(['can:oncologicos_mezclas_index']);

Route::get('oncologicos/mezclas/remision/{solicitud}', [OncologicosSolicitudController::class, 'remision'])->name('oncologicos.mezclas.remision')
    ->middleware(['can:oncologicos_mezclas_index']);


//DILUENTS
// Listado
Route::get('oncologicos/diluents', [DiluentController::class, 'index'])
    ->name('oncologicos.diluents.index')
    ->middleware(['can:oncologicos_diluents_index']);

// Crear
Route::get('oncologicos/diluents/crear', [DiluentController::class, 'create'])
    ->name('oncologicos.diluents.create')
    ->middleware(['can:oncologicos_diluents_create']);

Route::post('oncologicos/diluents', [DiluentController::class, 'store'])
    ->name('oncologicos.diluents.store')
    ->middleware(['can:oncologicos_diluents_store']);
// Editar
Route::get('oncologicos/diluents/{diluent}/editar', [DiluentController::class, 'edit'])
    ->name('oncologicos.diluents.edit')
    ->middleware(['can:oncologicos_diluents_edit']);

Route::put('oncologicos/diluents/{diluent}', [DiluentController::class, 'update'])
    ->name('oncologicos.diluents.update')
    ->middleware(['can:oncologicos_diluents_update']);

// Eliminar
Route::delete('oncologicos/diluents/{diluent}', [DiluentController::class, 'destroy'])
    ->name('oncologicos.diluents.destroy')
    ->middleware(['can:oncologicos_diluents_destroy']);

//END DILUENTS

//SUBCRUD DE DILUYENTES
// LISTAR presentaciones de un diluyente
Route::get('oncologicos/diluents/{diluent}/presentaciones', [DiluentPresentationController::class, 'index'])
    ->name('oncologicos.diluent_presentations.index')
    ->middleware(['can:oncologicos_diluents_index']);

// CREAR
Route::get('oncologicos/diluents/{diluent}/presentaciones/crear', [DiluentPresentationController::class, 'create'])
    ->name('oncologicos.diluent_presentations.create')
    ->middleware(['can:oncologicos_diluents_create']);

Route::post('oncologicos/diluents/{diluent}/presentaciones', [DiluentPresentationController::class, 'store'])
    ->name('oncologicos.diluent_presentations.store')
    ->middleware(['can:oncologicos_diluents_store']);

// EDITAR
Route::get('oncologicos/diluents/{diluent}/presentaciones/{presentation}/editar', [DiluentPresentationController::class, 'edit'])
    ->name('oncologicos.diluent_presentations.edit')
    ->middleware(['can:oncologicos_diluents_edit']);

Route::put('oncologicos/diluents/{diluent}/presentaciones/{presentation}', [DiluentPresentationController::class, 'update'])
    ->name('oncologicos.diluent_presentations.update')
    ->middleware(['can:oncologicos_diluents_update']);

// ELIMINAR
Route::delete('oncologicos/diluents/{diluent}/presentaciones/{presentation}', [DiluentPresentationController::class, 'destroy'])
    ->name('oncologicos.diluent_presentations.destroy')
    ->middleware(['can:oncologicos_diluents_destroy']);

//END SUBCRUDDILUYENTES

Route::resource('oncologicos/medicines/catalog', MedicineCatalogController::class)
    ->middleware(['can:medicamentos_oncologicos'])
    ->names('oncologicos.medicines.catalog');


Route::resource('oncologicos/medicines', OncologicosMedicineController::class)
    ->except(['show'])
    ->middleware(['can:medicamentos_oncologicos'])
    ->names('oncologicos.medicines');

Route::get('oncologicos/medicines/{medicineList}/exportar', [OncologicosMedicineController::class, 'exportarExcel'])
    ->name('oncologicos.medicines.exportar')
    ->middleware(['can:medicamentos_oncologicos']);


// RUTAS PARA ONCOLÓGICOS / INFUSORES
Route::prefix('oncologicos')->name('oncologicos.')->group(function () {
    Route::resource('infusores', InfusorController::class)
        ->parameters(['infusores' => 'infusor']) // <-- fuerza {infusor}
        ->names('infusores'); // genera index, create, store, show, edit, update, destroy
});

//RUTAS PARA PRESENTACIONES

Route::resource('oncologicos/medicines/catalog.presentations', MedicinePresentationController::class)
    ->middleware(['can:medicamentos_oncologicos'])
    ->names('oncologicos.medicines.catalog.presentations');


Route::resource('/clientes', ClienteController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['can:clientes']); // ajusta permiso si quieres otro nombre

Route::get('clientes/{cliente}/exportar-mezclas-onco', [ClienteController::class, 'exportarMezclasOnco'])
    ->name('clientes.exportarMezclasOnco')
    ->middleware(['can:clientes']);
