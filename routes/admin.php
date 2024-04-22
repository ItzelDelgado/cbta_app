<?php

use App\Http\Controllers\Admin\HospitalController;
use App\Http\Controllers\Admin\InputController;
use App\Http\Controllers\Admin\MedicineController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SolicitudController;
use App\Http\Controllers\Admin\UserController;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Route; //Importamos para generar nuestras rutas.

//Debemos avisarle a laravel que hemos creado un nuevo archivo de rutas en providers
Route::get('/', function(){
//     session()->flash('swal', [
//         'icon'=>"error",
//         'title'=>"Oops...",
//         'text'=>"Something went wrong :(!",
//         'footer'=>'<a href="#">Why do I have this issue?</a>'
// ]);
    return view('admin.dashboard');
})->name('dashboard');



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

Route::resource('/medicines', MedicineController::class)
    ->except(['destroy'])
    ->middleware(['can:medicamentos']);

// Route::resource('solicitudes', SolicitudController::class)->parameter('solicitudes', 'solicitud')->except(['destroy'])
//     ->middleware(['can:solicitudes']);

    // Ruta para mostrar todas las solicitudes
Route::get('solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index')
->middleware(['can:solicitudes_index']);
// Ruta para mostrar el formulario de creación de solicitud
Route::get('solicitudes/create', [SolicitudController::class, 'create'])->name('solicitudes.create')
->middleware(['can:solicitudes_create']);

// Ruta para almacenar una nueva solicitud
Route::post('solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store')
->middleware(['can:solicitudes_store']);

// Ruta para mostrar una solicitud específica
Route::get('solicitudes/{solicitud}', [SolicitudController::class, 'show'])->name('solicitudes.show')
->middleware(['can:solicitudes_show']);

// Ruta para mostrar el formulario de edición de una solicitud
Route::get('solicitudes/{solicitud}/edit', [SolicitudController::class, 'edit'])->name('solicitudes.edit')
->middleware(['can:solicitudes_edit']);

// Ruta para actualizar una solicitud específica
Route::put('solicitudes/{solicitud}', [SolicitudController::class, 'update'])->name('solicitudes.update')
->middleware(['can:solicitudes_update']);

// También puedes excluir la ruta de eliminación
// Route::resource('solicitudes', SolicitudController::class)->parameter('solicitudes', 'solicitud')->except(['destroy']);


Route::get('/solicitudes/orden-de-preparacion/{solicitud}', [SolicitudController::class, 'ordenPreparacion'])->name('solicitudes.ordenPreparacion')
    ->middleware(['can:solicitudes_index']);

Route::get('/solicitudes/remision/{solicitud}', [SolicitudController::class, 'remision'])->name('solicitudes.remision')
    ->middleware(['can:solicitudes_index']);

Route::get('/solicitudes/envio/{solicitud}', [SolicitudController::class, 'envio'])->name('solicitudes.envio')
    ->middleware(['can:solicitudes_index']);

Route::get('/solicitudes/etiqueta/{solicitud}', [SolicitudController::class, 'etiqueta'])->name('solicitudes.etiqueta')
    ->middleware(['can:solicitudes_index']);
