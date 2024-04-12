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

Route::resource('solicitudes', SolicitudController::class)->parameter('solicitudes', 'solicitud')->except(['destroy'])
    ->middleware(['can:solicitudes']);

Route::get('/solicitudes/orden-de-preparacion/{solicitud}', [SolicitudController::class, 'ordenPreparacion'])->name('solicitudes.ordenPreparacion')
    ->middleware(['can:solicitudes']);

Route::get('/solicitudes/remision/{solicitud}', [SolicitudController::class, 'remision'])->name('solicitudes.remision')
    ->middleware(['can:solicitudes']);

Route::get('/solicitudes/envio/{solicitud}', [SolicitudController::class, 'envio'])->name('solicitudes.envio')
    ->middleware(['can:solicitudes']);

Route::get('/solicitudes/etiqueta/{solicitud}', [SolicitudController::class, 'etiqueta'])->name('solicitudes.etiqueta')
    ->middleware(['can:solicitudes']);
