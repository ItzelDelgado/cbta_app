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
use App\Http\Controllers\Admin\Oncologicos\MedicineCatalogController;
use App\Http\Controllers\Admin\Oncologicos\MedicineController as OncologicosMedicineController;
use App\Http\Controllers\Admin\Oncologicos\SolicitudController as OncologicosSolicitudController;
use Maatwebsite\Excel\Facades\Excel;

//Debemos avisarle a laravel que hemos creado un nuevo archivo de rutas en providers
Route::get('/dashboard', function(){
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
    ->middleware(['can:nutricionales_nutricionales_solicitudes_index']);


Route::resource('/users', UserController::class)
    ->middleware(['can:usuarios']);

Route::resource('/roles', RoleController::class)
    ->except('show')
    ->middleware(['can:roles']);

Route::resource('/permissions', PermissionController::class)
    ->except('show')
    ->middleware(['can:permisos']);


Route::resource('/hospitals', HospitalController::class)
    ->except(['show','destroy'])
    ->middleware(['can:hospitales']);

Route::resource('nutricionales/medicines', MedicineController::class)
    ->except(['destroy'])
    ->middleware(['can:medicamentos_nutricionales'])
    ->names('nutricionales.medicines');



// Route::resource('solicitudes', SolicitudController::class)->parameter('solicitudes', 'solicitud')->except(['destroy'])
//     ->middleware(['can:solicitudes']);

    // Ruta para mostrar todas las solicitudes
Route::get('nutricionales/solicitudes', [SolicitudController::class, 'index'])->name('nutricionales.solicitudes.index')
->middleware(['can:nutricionales_solicitudes_index']);
// Ruta para mostrar el formulario de creación de solicitud
Route::get('nutricionales/solicitudes/create', [SolicitudController::class, 'create'])->name('nutricionales.solicitudes.create')
->middleware(['can:solicitudes_create']);

// Ruta para almacenar una nueva solicitud
Route::post('nutricionales/solicitudes', [SolicitudController::class, 'store'])->name('nutricionales.solicitudes.store')
->middleware(['can:solicitudes_store']);

// Ruta para mostrar una solicitud específica
Route::get('nutricionales/solicitudes/{solicitud}', [SolicitudController::class, 'show'])->name('nutricionales.solicitudes.show')
->middleware(['can:solicitudes_show']);

// Ruta para mostrar el formulario de edición de una solicitud
Route::get('nutricionales/solicitudes/{solicitud}/edit', [SolicitudController::class, 'edit'])->name('nutricionales.solicitudes.edit')
->middleware(['can:solicitudes_edit']);

// Ruta para actualizar una solicitud específica
Route::put('nutricionales/solicitudes/{solicitud}', [SolicitudController::class, 'update'])->name('nutricionales.solicitudes.update')
->middleware(['can:solicitudes_update']);

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

Route::get('oncologicos/solicitudes', [OncologicosSolicitudController::class, 'index'])->name('oncologicos.solicitudes.index');

Route::get('oncologicos/solicitudes/create', [OncologicosSolicitudController::class, 'create'])->name('oncologicos.solicitudes.create');

Route::post('oncologicos/solicitudes', [OncologicosSolicitudController::class, 'store'])->name('oncologicos.solicitudes.store');


Route::resource('oncologicos/medicines/catalog', MedicineCatalogController::class)
    ->middleware(['can:medicamentos_oncologicos'])
    ->names('oncologicos.medicines.catalog');


Route::resource('oncologicos/medicines', OncologicosMedicineController::class)
    ->middleware(['can:medicamentos_oncologicos'])
    ->names('oncologicos.medicines');

